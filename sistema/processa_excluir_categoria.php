<?php
    session_start();

    if (!isset($_SESSION['idUsuario'])) {
        header("Location: ../login.php");
        exit();
    }

    // 2. Aceita somente requisições POST.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: cadastrarCategoria.php");
        exit();
    }

    $idUsuario = (int) $_SESSION['idUsuario'];

    // 4. Recebe o ID da categoria.
    $idCategoria = filter_input(
        INPUT_POST,
        'idCategoria',
        FILTER_VALIDATE_INT
    );

    if (!$idCategoria || $idCategoria < 1) {
        header("Location: cadastrarCategoria.php?status=exclusao_invalida");
        exit();
    }

    require_once '../conecta/conecta.php';

    // Verifica se a categoria pertence ao usuário.
    $sql = "SELECT idCategoria
            FROM tb_categoria
            WHERE idCategoria = ?
              AND idUsuario = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $idCategoria, $idUsuario);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $categoriaExiste = $resultado->num_rows > 0;

    $stmt->close();

    if (!$categoriaExiste) {
        $conn->close();
        header("Location: cadastrarCategoria.php?status=categoria_nao_encontrada");
        exit();
    }

    // Verifica se existem produtos vinculados.
    $sql = "SELECT idProduto
            FROM tb_produtos
            WHERE idCategoria = ?
              AND idUsuario = ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $idCategoria, $idUsuario);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $possuiProdutos = $resultado->num_rows > 0;

    $stmt->close();

    if ($possuiProdutos) {
        $conn->close();
        header("Location: cadastrarCategoria.php?status=categoria_em_uso");
        exit();
    }

    // Exclui somente a categoria do usuário conectado.
    $sql = "DELETE FROM tb_categoria
            WHERE idCategoria = ?
              AND idUsuario = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $idCategoria, $idUsuario);

    if ($stmt->execute() && $stmt->affected_rows === 1) {
        $stmt->close();
        $conn->close();

        header("Location: cadastrarCategoria.php?status=exclusao_concluida");
        exit();
    }

    $stmt->close();
    $conn->close();

    header("Location: cadastrarCategoria.php?status=erro_exclusao");
    exit();
