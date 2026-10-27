<?php
require_once 'conecta/conecta.php';

if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}

$nomeUsuario     = trim($_POST['nomeCompleto'] ?? '');
$emailUsuario    = trim($_POST['email'] ?? '');
$telefoneUsuario = trim($_POST['telefone'] ?? '');
$senhaUsuario    = $_POST['senha'] ?? '';
$confirmaSenha   = $_POST['confirma_senha'] ?? '';
$cpf_raw         = $_POST['cpf'] ?? '';

$cpf = preg_replace('/[^0-9]/', '', $cpf_raw);
$telefone = preg_replace('/[^0-9]/', '', $telefoneUsuario);

if (empty($nomeUsuario) || empty($emailUsuario) || empty($telefone) || empty($senhaUsuario) || empty($confirmaSenha) || empty($cpf)) {
    header("Location: cadastro.php?erro=campos_vazios");
    exit();
}

if ($senhaUsuario !== $confirmaSenha) {
    header("Location: cadastro.php?erro=senhas_diferentes");
    exit();
}

if (!filter_var($emailUsuario, FILTER_VALIDATE_EMAIL)) {
    header("Location: cadastro.php?erro=email_invalido");
    exit();
}

if (strlen($cpf) !== 11) {
    header("Location: cadastro.php?erro=cpf_invalido");
    exit();
}

$sql = "SELECT emailUsuario, cpfUsuario FROM tb_usuario WHERE emailUsuario = ? OR cpfUsuario = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro ao preparar consulta: " . $conn->error);
}

$stmt->bind_param("ss", $emailUsuario, $cpf);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $conn->close();

    header("Location: cadastro.php?erro=usuario_existente");
    exit();
}

$stmt->close();

$senhaHash = password_hash($senhaUsuario, PASSWORD_DEFAULT);

$sql = "INSERT INTO tb_usuario (nomeUsuario, emailUsuario, telefoneUsuario, senhaUsuario, cpfUsuario) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro ao preparar cadastro: " . $conn->error);
}

$stmt->bind_param("sssss", $nomeUsuario, $emailUsuario, $telefoneUsuario, $senhaHash, $cpf);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    header("Location: login.php?sucesso=cadastrado");
    exit();
} else {
    $stmt->close();
    $conn->close();

    header("Location: cadastro.php?erro=cadastro_falhou");
    exit();
}
?>
