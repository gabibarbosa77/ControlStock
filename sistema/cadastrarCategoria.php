<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

$idUsuario = (int)$_SESSION['idUsuario'];

require_once '../conecta/conecta.php';

// Consulta para buscar todas as categorias cadastradas do utilizador logado
$sql = "SELECT idCategoria, nomeCategoria FROM tb_categoria WHERE idUsuario = ? ORDER BY nomeCategoria";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();

// Tratamento de status para exibição de alertas
$status = $_GET['status'] ?? '';
$mensagem = '';
$tipoStatus = '';

switch ($status) {
    case 'sucesso':
        $mensagem = "CATEGORIA PROCESSADA COM SUCESSO!";
        $tipoStatus = 'sucesso';
        break;
    case 'nome_invalido':
        $mensagem = "INFORME UM NOME VÁLIDO PARA A CATEGORIA.";
        $tipoStatus = 'erro';
        break;
    case 'erro':
    case 'error':
        $mensagem = "OCORREU UM ERRO AO PROCESSAR A REQUISIÇÃO.";
        $tipoStatus = 'erro';
        break;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Categoria - controlStoke</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <main class="page-container d-block">
        
        <div class="form-card mx-auto">
            <h2>CADASTRAR CATEGORIAS</h2>

            <!-- Mensagem de erro -->
            <?php if (!empty($mensagem) && $tipoStatus === 'erro'): ?>
                <div class="alert-box erro">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?= htmlspecialchars($mensagem) ?></span>
                </div>
            <?php endif; ?>

            <form action="adicionarCategoria.php" method="POST" class="product-form" id="form-cadastrar-categoria">
                <div class="form-group">
                    <label for="nome_categoria">CATEGORIA</label>
                    <input type="text" id="nome_categoria" name="nome_categoria" required autocomplete="off">
                </div>

                <button type="submit" class="btn-save">
                    <i class="fa-regular fa-floppy-disk"></i>
                    <span>SALVAR</span>
                </button>
            </form>
        </div>

        <div class="categories-list-container">
            <h3 class="categories-title">CATEGORIAS</h3>
            <div class="divider mb-3"></div>

            <div class="categories-table-card">
                <div class="table-responsive">
                    <table class="categories-table align-middle">
                        <thead>
                            <tr>
                                <th>CATEGORIA</th>
                                <th class="text-center">AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultado->num_rows > 0): ?>
                                <?php while ($categoria = $resultado->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($categoria['nomeCategoria']) ?></td>
                                        <td class="text-center">
                                           <div class="table-actions justify-content-center">
                                                <button type="button" class="action-icon-btn delete"
                                                    onclick='openDeleteCategoryModal(<?= (int)$categoria['idCategoria'] ?>, <?= json_encode($categoria['nomeCategoria'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                                    title="Excluir Categoria">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>

                                                <button type="button" class="action-icon-btn edit"
                                                    onclick='openEditCategoryModal(<?= (int)$categoria['idCategoria'] ?>, <?= json_encode($categoria['nomeCategoria'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                                    title="Editar Categoria">
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </button>
                                           </div> 
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Nenhuma categoria cadastrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if (!empty($mensagem) && $tipoStatus === 'sucesso'): ?>
            <div class="toast-success" id="toast-mensagem">
                <i class="fa-solid fa-circle-check toast-icon"></i>
                <span class="toast-text"><?= htmlspecialchars($mensagem) ?></span>
                <button type="button" class="bg-transparent border-0 ms-2 p-0 text-secondary" onclick="fecharToast()" title="Fechar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        <?php endif; ?>

    </main>

    <!-- ALTERAR CATEGORIA -->
    <div class="modal fade" id="modalEditarCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-card-category position-relative">
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <h3>ALTERAR CATEGORIA</h3>

                <form action="alteraCategoria.php" method="POST" class="product-form">
                    <input type="hidden" name="id_categoria" id="id_categoria_edit">

                    <div class="form-group">
                        <label for="novo_nome_cat">NOVO NOME</label>
                        <input type="text" id="novo_nome_cat" name="novo_nome_cat" required autocomplete="off">
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="fa-regular fa-floppy-disk"></i>
                        <span>SALVAR</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- EXCLUIR CATEGORIA -->
    <div class="modal fade" id="modalExcluirCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-card position-relative">
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <h3 class="modal-title">EXCLUIR CATEGORIA</h3>
                <p class="modal-text">
                    TEM CERTEZA QUE DESEJA EXCLUIR <span id="nome-categoria-excluir">CATEGORIA</span>? 
                    <strong>NÃO SERÁ POSSÍVEL RESTAURAR APÓS EXCLUSÃO E TODOS OS PRODUTOS VINCULADOS A ESSA CATEGORIA SERÃO EXCLUÍDOS PERMANENTEMENTE.</strong>
                </p>
                
                <form action="excluirCategoria.php" method="POST">
                    <input type="hidden" name="id_categoria" id="id_categoria_delete">
                    <button type="submit" class="btn-confirm-delete">
                        <i class="fa-regular fa-trash-can"></i>
                        <span>EXCLUIR</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer text-center">
        <p>© <?php echo date('Y'); ?> controlStoke — Sistema de Controle de Estoque</p>
        <p><a href="politica.php">Política de Privacidade</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let bsEditModal;
        let bsDeleteModal;

        const inputIdEdit = document.getElementById('id_categoria_edit');
        const inputNovoNome = document.getElementById('novo_nome_cat');

        const inputIdDelete = document.getElementById('id_categoria_delete');
        const spanNomeCategoriaExcluir = document.getElementById('nome-categoria-excluir');

        document.addEventListener('DOMContentLoaded', function() {
            bsEditModal = new bootstrap.Modal(document.getElementById('modalEditarCategoria'));
            bsDeleteModal = new bootstrap.Modal(document.getElementById('modalExcluirCategoria'));
        });

        function openEditCategoryModal(idCategoria, nomeCategoria) {
            inputIdEdit.value = idCategoria;
            inputNovoNome.value = nomeCategoria;
            bsEditModal.show();
        }

        function openDeleteCategoryModal(idCategoria, nomeCategoria) {
            inputIdDelete.value = idCategoria;
            spanNomeCategoriaExcluir.textContent = nomeCategoria;
            bsDeleteModal.show();
        }

        function fecharToast() {
            const toast = document.getElementById('toast-mensagem');
            if (toast) {
                toast.style.display = 'none';
            }
        }
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
