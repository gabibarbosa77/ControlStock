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
$nomeCategoria = trim($_POST['nome_categoria'] ?? '');

if ($nomeCategoria === '' || mb_strlen($nomeCategoria) > 500) {
    header("Location: cadastrarCategoria.php?status=nome_invalido");
    exit();
}

require_once '../conecta/conecta.php';

$sql = "INSERT INTO tb_categoria (nomeCategoria, idUsuario) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    header("Location: cadastrarCategoria.php?status=erro");
    exit();
}

$stmt->bind_param("si", $nomeCategoria, $idUsuario);

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