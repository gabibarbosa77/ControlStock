<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cadastrarProduto.php");
    exit();
}

$idUsuario = (int)$_SESSION['idUsuario'];

$nomeProduto    = trim($_POST['nome_produto'] ?? '');
$codigoBarras   = (int)preg_replace('/\D/', '', $_POST['codigo_barras'] ?? '');
$estoqueProduto = (int)($_POST['quantidade_inicial'] ?? 0);
$estoqueMinimo  = (int)($_POST['estoque_minimo'] ?? 0);
$idCategoria    = (int)($_POST['id_categoria'] ?? 0);

// Converte a string formatada em moeda para um valor decimal no padrão MySQL
$precoRaw = $_POST['preco'] ?? '0';
$precoRaw = str_replace(['R$', ' ', '.'], '', $precoRaw);
$precoRaw = str_replace(',', '.', $precoRaw);
$precoProduto = (float)$precoRaw;

if ($nomeProduto === '' || $codigoBarras <= 0 || $idCategoria <= 0 || $precoProduto <= 0) {
    header("Location: cadastrarProduto.php?status=invalid_input");
    exit();
}

require_once '../conecta/conecta.php';

// Verifica se o código de barras já está cadastrado para o usuário especifico
$sqlCheck = "SELECT idProduto FROM tb_produtos WHERE codigoBarras = ? AND idUsuario = ?";
$stmtCheck = $conn->prepare($sqlCheck);

if ($stmtCheck) {
    $stmtCheck->bind_param("ii", $codigoBarras, $idUsuario);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        $stmtCheck->close();
        $conn->close();
        header("Location: cadastrarProduto.php?status=codigo_duplicado");
        exit();
    }
    $stmtCheck->close();
}

// Prepara a instrucao SQL para inserir o produto na tabela tb_produtos
$sql = "INSERT INTO tb_produtos 
        (nomeProduto, codigoBarras, estoqueProduto, precoProduto, estoqueMinimo, idUsuario, idCategoria) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    header("Location: cadastrarProduto.php?status=error");
    exit();
} 

$stmt->bind_param("siidiii", $nomeProduto, $codigoBarras, $estoqueProduto, $precoProduto, $estoqueMinimo, $idUsuario, $idCategoria);
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: cadastrarProduto.php?status=sucesso");
    exit();
}

$stmt->close();
$conn->close();
header("Location: cadastrarProduto.php?status=error");
exit();
?>
