<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../conecta/conecta.php';

// Função para validar o CPF matematicamente
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario     = (int)$_SESSION['idUsuario'];
    $nomeCompleto  = trim($_POST['nome_completo']);
    $cpf           = preg_replace('/[^0-9]/', '', $_POST['cpf']); // Limpa pontuação
    $email         = trim($_POST['email']);
    $telefone      = preg_replace('/[^0-9]/', '', $_POST['telefone']); // Limpa caracteres não numéricos do telefone

    // 1. Verifica se há campos vazios
    if (empty($nomeCompleto) || empty($cpf) || empty($email) || empty($telefone)) {
        header("Location: configuracao.php?status=campos_vazios");
        exit();
    }

    // 2. Valida se o CPF é válido
    if (!validarCPF($cpf)) {
        header("Location: configuracao.php?status=cpf_invalido");
        exit();
    }

    // 3. Valida se o Telefone possui no mínimo 10 dígitos (DDD + 8 ou 9 dígitos)
    if (strlen($telefone) < 10) {
        header("Location: configuracao.php?status=telefone_invalido");
        exit();
    }

    // Processa a atualização dos dados
    $sql = "UPDATE tb_usuario SET nomeUsuario = ?, emailUsuario = ?, cpfUsuario = ?, telefoneUsuario = ? WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nomeCompleto, $email, $cpf, $telefone, $idUsuario);

    if ($stmt->execute()) {
        header("Location: configuracao.php?status=dados_sucesso");
        exit();
    } else {
        header("Location: configuracao.php?status=erro");
        exit();
    }
} else {
    header("Location: configuracao.php");
    exit();
}
?>