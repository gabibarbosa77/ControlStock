<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cadastrarCategoria.php");
    exit();
}

$idUsuario = (int)$_SESSION['idUsuario'];
$idCategoria = (int)($_POST['id_categoria'] ?? 0);
$novoNomeCat = trim($_POST['novo_nome_cat'] ?? '');

if ($idCategoria <= 0 || $novoNomeCat === '' || mb_strlen($novoNomeCat) > 500) {
    header("Location: cadastrarCategoria.php?status=nome_invalido");
    exit();
}

require_once '../conecta/conecta.php';

$sql = "UPDATE tb_categoria 
        SET nomeCategoria = ? 
        WHERE idCategoria = ? AND idUsuario = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    header("Location: cadastrarCategoria.php?status=erro");
    exit();
}

$stmt->bind_param("sii", $novoNomeCat, $idCategoria, $idUsuario);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: cadastrarCategoria.php?status=sucesso");
    exit();
}

$stmt->close();
$conn->close();
header("Location: cadastrarCategoria.php?status=erro");
exit();