<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

$idUsuario = (int)$_SESSION['idUsuario'];
require_once '../conecta/conecta.php';

$mensagemErro = '';
$mensagemSucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idProduto = isset($_POST['id_produto']) ? (int)$_POST['id_produto'] : 0;
    $tipoOpcao = isset($_POST['tipo_opcao']) ? trim($_POST['tipo_opcao']) : '';
    $qtdInformada = isset($_POST['atualizar_estoque']) ? (int)$_POST['atualizar_estoque'] : 0;

    if ($idProduto <= 0 || empty($tipoOpcao) || $qtdInformada < 0) {
        $mensagemErro = "Dados inválidos. Preencha todos os campos corretamente.";
    } else {
        // Inicia transação SQL
        $conn->begin_transaction();

        try {
            // Consulta o estoque atual e trava a linha para atualização (FOR UPDATE)
            $sqlBusca = "SELECT estoqueProduto FROM tb_produtos WHERE idProduto = ? AND idUsuario = ? FOR UPDATE";
            $stmtBusca = $conn->prepare($sqlBusca);
            $stmtBusca->bind_param("ii", $idProduto, $idUsuario);
            $stmtBusca->execute();
            $resBusca = $stmtBusca->get_result();

            if ($resBusca->num_rows === 0) {
                throw new Exception("Produto não encontrado.");
            }

            $produtoAtual = $resBusca->fetch_assoc();
            $estoqueAtual = (int)$produtoAtual['estoqueProduto'];
            $stmtBusca->close();

            $novoEstoque = $estoqueAtual;
            $qtdMovimentada = $qtdInformada;

            // Lógica do novo estoque e quantidade movimentada
            if ($tipoOpcao === 'adicionar') {
                $novoEstoque = $estoqueAtual + $qtdInformada;
                $qtdMovimentada = $qtdInformada;
            } elseif ($tipoOpcao === 'retirada') {
                if ($qtdInformada > $estoqueAtual) {
                    throw new Exception("Estoque insuficiente para esta retirada.");
                }
                $novoEstoque = $estoqueAtual - $qtdInformada;
                $qtdMovimentada = $qtdInformada;
            } elseif ($tipoOpcao === 'ajustar') {
                $novoEstoque = $qtdInformada;
                $qtdMovimentada = $qtdInformada;
            } else {
                throw new Exception("Opção de movimentação inválida.");
            }

            // Update em tb_produtos
            $sqlUpdate = "UPDATE tb_produtos SET estoqueProduto = ? WHERE idProduto = ? AND idUsuario = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("iii", $novoEstoque, $idProduto, $idUsuario);
            $stmtUpdate->execute();
            $stmtUpdate->close();

            // Insert em tb_entradasaida
            $sqlInsert = "INSERT INTO tb_entradasaida (tipoEntradaSaida, quantidadeEntradaSaida, idUsuario, idProduto) VALUES (?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("siii", $tipoOpcao, $qtdMovimentada, $idUsuario, $idProduto);
            $stmtInsert->execute();
            $stmtInsert->close();

            // Confirma alterações no banco
            $conn->commit();

            header("Location: exibirEstoque.php?sucesso=1");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $mensagemErro = $e->getMessage();
        }
    }
}

// CARREGAMENTO DOS DADOS DO PRODUTO (GET)
$idProduto = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id_produto']) ? (int)$_POST['id_produto'] : 0);

if ($idProduto <= 0) {
    header("Location: exibirEstoque.php");
    exit();
}

$sql = "SELECT idProduto, nomeProduto, codigoBarras, estoqueProduto 
        FROM tb_produtos 
        WHERE idProduto = ? AND idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $idProduto, $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: exibirEstoque.php");
    exit();
}

$produto = $resultado->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar/Entrada Estoque - controlStoke</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <main class="page-container d-flex justify-content-center pt-5 mt-4 mb-5">
        
        <div class="form-card mx-auto">
            
            <div class="d-flex align-items-center justify-content-center position-relative mb-4">
                <a href="exibirEstoque.php" class="btn-back position-absolute start-0" title="Voltar ao Estoque">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                <h2 class="m-0 text-center">ALTERAR/ENTRADA ESTOQUE</h2>
            </div>

            <!-- Exibicao de Alerta de Erro -->
            <?php if (!empty($mensagemErro)): ?>
                <div class="alert alert-danger text-center mb-3" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <?= htmlspecialchars($mensagemErro) ?>
                </div>
            <?php endif; ?>

            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $produto['idProduto'] ?>" method="POST" class="product-form" id="form-altera-estoque">
                
                <input type="hidden" name="id_produto" value="<?= $produto['idProduto'] ?>">

                <!-- Campos Bloqueados -->
                <div class="form-row-2col">
                    <div class="form-group mb-3">
                        <label for="nome_produto">NOME PRODUTO</label>
                        <input type="text" id="nome_produto" value="<?= htmlspecialchars($produto['nomeProduto']) ?>" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label for="codigo_barras">CÓDIGO BARRAS</label>
                        <input type="text" id="codigo_barras" value="<?= htmlspecialchars($produto['codigoBarras']) ?>" readonly>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="estoque_atual">ESTOQUE ATUAL</label>
                    <input type="text" id="estoque_atual" value="<?= (int)$produto['estoqueProduto'] ?>" readonly>
                </div>

                <hr class="form-divider my-4">

                <!-- Campos Editáveis -->
                <div class="form-group mb-3">
                    <label for="tipo_opcao">ESCOLHA UMA OPÇÃO</label>
                    <select id="tipo_opcao" name="tipo_opcao" required>
                        <option value="" disabled selected>NENHUMA OPÇÃO SELECIONADA</option>
                        <option value="adicionar">ADICIONAR</option>
                        <option value="retirada">RETIRADA</option>
                        <option value="ajustar">AJUSTAR</option>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="atualizar_estoque">QUANTIDADE</label>
                    <input type="number" id="atualizar_estoque" name="atualizar_estoque" value="0" min="0" required>
                </div>

                <button type="submit" class="btn-save w-100 justify-content-center">
                    <i class="fa-regular fa-floppy-disk"></i>
                    <span>SALVAR</span>
                </button>
            </form>
        </div>

    </main>

    <footer class="footer text-center">
        <p>© <?php echo date('Y'); ?> controlStoke — Sistema de Controle de Estoque</p>
        <p><a href="politica.php">Política de Privacidade</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
