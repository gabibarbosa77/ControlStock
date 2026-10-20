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

if ($idCategoria <= 0) {
    header("Location: cadastrarCategoria.php?status=erro");
    exit();
}

require_once '../conecta/conecta.php';

// Inicia transação
$conn->begin_transaction();

try {
    // 1. Exclui os registros de entrada/saída vinculados aos produtos dessa categoria
    $sqlEntradaSaida = "DELETE FROM tb_entradasaida 
                        WHERE idProduto IN (
                            SELECT idProduto FROM tb_produtos WHERE idCategoria = ? AND idUsuario = ?
                        ) AND idUsuario = ?";
    $stmtEntradaSaida = $conn->prepare($sqlEntradaSaida);
    if (!$stmtEntradaSaida) {
        throw new Exception("Erro ao preparar exclusão do histórico de movimentações.");
    }
    $stmtEntradaSaida->bind_param("iii", $idCategoria, $idUsuario, $idUsuario);
    $stmtEntradaSaida->execute();
    $stmtEntradaSaida->close();

    // 2. Exclui os produtos vinculados à categoria
    $sqlProdutos = "DELETE FROM tb_produtos WHERE idCategoria = ? AND idUsuario = ?";
    $stmtProdutos = $conn->prepare($sqlProdutos);
    if (!$stmtProdutos) {
        throw new Exception("Erro ao preparar exclusão dos produtos.");
    }
    $stmtProdutos->bind_param("ii", $idCategoria, $idUsuario);
    $stmtProdutos->execute();
    $stmtProdutos->close();

    // 3. Exclui a categoria
    $sqlCategoria = "DELETE FROM tb_categoria WHERE idCategoria = ? AND idUsuario = ?";
    $stmtCategoria = $conn->prepare($sqlCategoria);
    if (!$stmtCategoria) {
        throw new Exception("Erro ao preparar exclusão da categoria.");
    }
    $stmtCategoria->bind_param("ii", $idCategoria, $idUsuario);
    $stmtCategoria->execute();
    $stmtCategoria->close();

    // Se tudo deu certo, confirma no banco
    $conn->commit();
    $conn->close();

    header("Location: cadastrarCategoria.php?status=sucesso");
    exit();

} catch (Exception $e) {
    // Cancela todas as operações em caso de falha
    $conn->rollback();
    $conn->close();

    header("Location: cadastrarCategoria.php?status=erro");
    exit();
}