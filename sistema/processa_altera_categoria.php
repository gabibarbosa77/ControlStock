
<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

//  Aceita somente requisições POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cadastrarCategoria.php");
    exit();
}

//  Recupera o usuário da sessão.
$idUsuario = (int) $_SESSION['idUsuario'];

//  Recebe os dados do formulário.
$idCategoria = filter_input(
    INPUT_POST,
    'idCategoria',
    FILTER_VALIDATE_INT
);

$novoNome = trim($_POST['novo_nome_cat'] ?? '');

//  Valida os dados.
if (
    !$idCategoria ||
    $idCategoria < 1 ||
    $novoNome === '' ||
    mb_strlen($novoNome) > 500
) {
    header("Location: cadastrarCategoria.php?status=edicao_invalida");
    exit();
}


require_once '../conecta/conecta.php';

// Atualiza somente a categoria que pertence ao usuário conectado.
$sql = "UPDATE tb_categoria
        SET nomeCategoria = ?
        WHERE idCategoria = ?
          AND idUsuario = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    header("Location: cadastrarCategoria.php?status=erro_edicao");
    exit();
}

$stmt->bind_param(
    "sii",
    $novoNome,
    $idCategoria,
    $idUsuario
);

// Executa a atualização.
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    header("Location: cadastrarCategoria.php?status=edicao_concluida");
    exit();
}

$stmt->close();
$conn->close();

header("Location: cadastrarCategoria.php?status=erro_edicao");
exit();
