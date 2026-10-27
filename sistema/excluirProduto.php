<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produto'])) {
    
    require_once '../conecta/conecta.php';

    $idUsuario = (int)$_SESSION['idUsuario'];
    $idProduto = (int)$_POST['id_produto'];

    if ($idProduto > 0) {
        // Inicia a transacao para garantir que o historico e o produto sejam excluidos juntos com seguranca
        $conn->begin_transaction();

        try {
            // exclui primeiro os registros de entrada/saida vinculados a este produto
            $sqlDeleteHistorico = "DELETE FROM tb_entradasaida WHERE idProduto = ? AND idUsuario = ?";
            $stmtHist = $conn->prepare($sqlDeleteHistorico);
            if (!$stmtHist) {
                throw new Exception("Erro ao preparar a exclusão do histórico.");
            }
            $stmtHist->bind_param("ii", $idProduto, $idUsuario);
            $stmtHist->execute();
            $stmtHist->close();

            // exclui o produto da tabela tb_produtos
            $sqlDeleteProduto = "DELETE FROM tb_produtos WHERE idProduto = ? AND idUsuario = ?";
            $stmtProd = $conn->prepare($sqlDeleteProduto);
            if (!$stmtProd) {
                throw new Exception("Erro ao preparar a exclusão do produto.");
            }
            $stmtProd->bind_param("ii", $idProduto, $idUsuario);
            $stmtProd->execute();
            $stmtProd->close();
            $conn->commit();
            $conn->close();

            $_SESSION['msg_sucesso'] = "Produto excluído com sucesso!";

        } catch (Exception $e) {
            // Reverte as alterações em caso de falha
            $conn->rollback();
            $conn->close();

            $_SESSION['msg_erro'] = "Erro ao tentar excluir o produto: " . $e->getMessage();
        }

    } else {
        $_SESSION['msg_erro'] = "Identificador de produto inválido.";
    }

} else {
    $_SESSION['msg_erro'] = "Requisição inválida.";
}

header("Location: exibirEstoque.php");
exit();
?>
