<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../conecta/conecta.php';

$idUsuario = (int)$_SESSION['idUsuario'];

$sql = "DELETE FROM tb_usuario WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);

if ($stmt->execute()) {
    // Limpa todas as variáveis de sessão e destrói a sessão atual
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();

    header("Location: ../login.php?status=conta_excluida");
    exit();
} else {
    header("Location: configuracao.php?status=erro");
    exit();
}
?>
