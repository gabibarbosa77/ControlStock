<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

$idUsuario = (int)$_SESSION['idUsuario'];
require_once '../conecta/conecta.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idProduto = (int)$_POST['id_produto'];
    $nomeProduto = trim($_POST['nome_produto']);
    $codigoBarras = (int)$_POST['codigo_barras'];
    $idCategoria = (int)$_POST['categoria'];
    $estoqueMinimo = (int)$_POST['estoque_minimo'];

    // Tratamento do valor do Preco
    $precoRaw = $_POST['preco'];
    $precoLimpo = preg_replace('/[^\d,]/', '', $precoRaw);
    $precoFloat = (float)str_replace(',', '.', $precoLimpo);

    // Verifica se o codigo de barras ja pertence a OUTRO produto
    $sqlCheck = "SELECT idProduto FROM tb_produtos WHERE codigoBarras = ? AND idUsuario = ? AND idProduto <> ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("iii", $codigoBarras, $idUsuario, $idProduto);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        $stmtCheck->close();
        $mensagemErro = "Atenção: Já existe outro produto cadastrado com este código de barras!";
    } else {
        $stmtCheck->close();

        $sqlUpdate = "UPDATE tb_produtos SET nomeProduto = ?, codigoBarras = ?, idCategoria = ?, precoProduto = ?, estoqueMinimo = ? WHERE idProduto = ? AND idUsuario = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("siidiii", $nomeProduto, $codigoBarras, $idCategoria, $precoFloat, $estoqueMinimo, $idProduto, $idUsuario);

        if ($stmtUpdate->execute()) {
            $stmtUpdate->close();
            header("Location: exibirEstoque.php?status=sucesso");
            exit();
        } else {
            $stmtUpdate->close();
            $mensagemErro = "Erro ao atualizar o produto no banco de dados.";
        }
    }
}

// BUSCA DE DADOS PARA RENDERIZAR O FORMULÁRIO (MÉTODO GET)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: exibirEstoque.php");
    exit();
}

$idProduto = (int)$_GET['id'];

$sqlProduto = "SELECT idProduto, nomeProduto, codigoBarras, precoProduto, estoqueMinimo, idCategoria 
               FROM tb_produtos 
               WHERE idProduto = ? AND idUsuario = ?";
$stmt = $conn->prepare($sqlProduto);
$stmt->bind_param("ii", $idProduto, $idUsuario);
$stmt->execute();
$resProduto = $stmt->get_result();

if ($resProduto->num_rows === 0) {
    header("Location: exibirEstoque.php");
    exit();
}

$produto = $resProduto->fetch_assoc();
$stmt->close();

$sqlCategorias = "SELECT idCategoria, nomeCategoria FROM tb_categoria WHERE idUsuario = ? ORDER BY nomeCategoria ASC";
$stmtCat = $conn->prepare($sqlCategorias);
$stmtCat->bind_param("i", $idUsuario);
$stmtCat->execute();
$resCategorias = $stmtCat->get_result();
$stmtCat->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Produto - controlStoke</title>
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
                <h2 class="m-0 text-center">ALTERAR PRODUTO</h2>
            </div>

            <!-- Exibição de Erro -->
            <?php if (isset($mensagemErro)): ?>
                <div class="alert-box erro mb-3">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?= htmlspecialchars($mensagemErro) ?></span>
                </div>
            <?php endif; ?>

            <form action="alteraProduto.php?id=<?= $produto['idProduto'] ?>" method="POST" class="product-form" id="form-altera-produto">
                <input type="hidden" name="id_produto" value="<?= $produto['idProduto'] ?>">

                <div class="form-group mb-3">
                    <label for="nome_produto">NOME PRODUTO</label>
                    <input type="text" id="nome_produto" name="nome_produto" value="<?= htmlspecialchars($produto['nomeProduto']) ?>" required autocomplete="off">
                </div>

                <div class="form-group mb-3">
                    <label for="codigo_barras">CÓDIGO BARRAS (MÁX: 10 CARACTERES)</label>
                    <input type="text" id="codigo_barras" name="codigo_barras" value="<?= htmlspecialchars($produto['codigoBarras']) ?>" maxlength="10" readonly required autocomplete="off">
                </div>

                <div class="form-group mb-3">
                    <label for="categoria">CATEGORIA</label>
                    <select id="categoria" name="categoria" required>
                        <option value="">Selecione uma Categoria</option>
                        <?php while ($cat = $resCategorias->fetch_assoc()): ?>
                            <option value="<?= $cat['idCategoria'] ?>" <?= ($cat['idCategoria'] == $produto['idCategoria']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nomeCategoria']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="preco">PREÇO</label>
                    <input type="text" id="preco" name="preco" value="R$<?= number_format($produto['precoProduto'], 2, ',', '.') ?>" required>
                </div>

                <div class="form-group mb-4">
                    <label for="estoque_minimo">ESTOQUE MÍNIMO</label>
                    <input type="number" id="estoque_minimo" name="estoque_minimo" value="<?= (int)$produto['estoqueMinimo'] ?>" required min="0">
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function applyCurrencyMask(input) {
                input.addEventListener('input', function (e) {
                    let value = e.target.value.replace(/\D/g, '');
                    value = (value / 100).toFixed(2) + '';
                    value = value.replace(".", ",");
                    value = value.replace(/(\d)(\d{3})(,\d{2})$/, "$1.$2$3");
                    e.target.value = value ? 'R$' + value : '';
                });
            }

            applyCurrencyMask(document.getElementById('preco'));
        });
    </script>
</body>
</html>
