<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

$idUsuario = (int)$_SESSION['idUsuario'];

// Conexão com o banco de dados
require_once '../conecta/conecta.php';

// Busca as categorias cadastradas pelo usuário logado
$sqlCategorias = "SELECT idCategoria, nomeCategoria 
                  FROM tb_categoria 
                  WHERE idUsuario = ? 
                  ORDER BY nomeCategoria";

$stmtCat = $conn->prepare($sqlCategorias);
$stmtCat->bind_param("i", $idUsuario);
$stmtCat->execute();
$resCategorias = $stmtCat->get_result();

// Tratamento das mensagens de retorno via URL (?status=...)
$status = $_GET['status'] ?? '';
$mensagem = '';
$tipoStatus = '';

switch ($status) {
    case 'sucesso':
        $mensagem = "PRODUTO CADASTRADO COM SUCESSO!";
        $tipoStatus = 'sucesso';
        break;
    case 'codigo_duplicado':
        $mensagem = "CÓDIGO DE BARRAS JÁ CADASTRADO PARA OUTRO PRODUTO!";
        $tipoStatus = 'erro';
        break;
    case 'invalid_input':
        $mensagem = "PREENCHA TODOS OS CAMPOS OBRIGATÓRIOS CORRETAMENTE.";
        $tipoStatus = 'erro';
        break;
    case 'error':
        $mensagem = "ERRO AO SALVAR O PRODUTO NO BANCO DE DADOS.";
        $tipoStatus = 'erro';
        break;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto - controlStoke</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <main class="page-container">
        
        <div class="form-card">
            <h2>CADASTRAR PRODUTO</h2>

            <!-- Exibição do Alerta de Erro caso o código seja duplicado ou haja falha -->
            <?php if (!empty($mensagem) && $tipoStatus === 'erro'): ?>
                <div class="alert-box erro">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?= htmlspecialchars($mensagem) ?></span>
                </div>
            <?php endif; ?>

            <form action="processaProduto.php" method="POST" class="product-form" id="form-cadastrar-produto">
                
                <!-- Nome do Produto -->
                <div class="form-group">
                    <label for="nome_produto">NOME PRODUTO</label>
                    <input type="text" id="nome_produto" name="nome_produto" required autocomplete="off">
                </div>

                <!-- Código de Barras (Max 10) -->
                <div class="form-group">
                    <label for="codigo_barras">CÓDIGO BARRAS (MAX: 10 CARACTERES)</label>
                    <input type="text" id="codigo_barras" name="codigo_barras" maxlength="10" required autocomplete="off">
                </div>

                <!-- Quantidade Inicial -->
                <div class="form-group">
                    <label for="quantidade_inicial">QUANTIDADE INICIAL</label>
                    <input type="number" id="quantidade_inicial" name="quantidade_inicial" min="0" required>
                </div>

                <!-- Preço -->
                <div class="form-group">
                    <label for="preco">PREÇO</label>
                    <input type="text" id="preco" name="preco" placeholder="R$ 0,00" required>
                </div>

                <!-- Estoque Mínimo -->
                <div class="form-group">
                    <label for="estoque_minimo">ESTOQUE MÍNIMO</label>
                    <input type="number" id="estoque_minimo" name="estoque_minimo" min="0" required>
                </div>

                <!-- Categoria Dinâmica do Banco de Dados -->
                <div class="form-group">
                    <label for="categoria">CATEGORIA</label>
                    <select id="categoria" name="id_categoria" required>
                        <option value="" disabled selected>NENHUMA OPÇÃO SELECIONADA</option>
                        <?php while ($cat = $resCategorias->fetch_assoc()): ?>
                            <option value="<?= (int)$cat['idCategoria'] ?>">
                                <?= htmlspecialchars($cat['nomeCategoria']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fa-regular fa-floppy-disk"></i>
                    <span>SALVAR</span>
                </button>
            </form>
        </div>

        <!-- Exibição do Toast de Sucesso utilizando o estilo nativo do style.css -->
        <?php if (!empty($mensagem) && $tipoStatus === 'sucesso'): ?>
            <div class="toast-success">
                <i class="fa-solid fa-circle-check toast-icon"></i>
                <span class="toast-text"><?= htmlspecialchars($mensagem) ?></span>
            </div>
        <?php endif; ?>

    </main>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> controlStoke — Sistema de Controle de Estoque</p>
        <p><a href="politica.php">Política de Privacidade</a></p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const precoInput = document.getElementById('preco');

            precoInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                value = (value / 100).toFixed(2) + '';
                value = value.replace(".", ",");
                value = value.replace(/(\d)(\d{3})(,\d{2})$/, "$1.$2$3");
                e.target.value = value ? 'R$ ' + value : '';
            });
        });
    </script>
</body>
</html>