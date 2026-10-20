<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

$idUsuario = (int)$_SESSION['idUsuario'];

require_once '../conecta/conecta.php';

// Consulta para buscar todas as categorias cadastradas do utilizador logado
$sql = "SELECT idCategoria, nomeCategoria
        FROM tb_categoria
        WHERE idUsuario = ?
        ORDER BY nomeCategoria";

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
        $mensagem = "INFORME UM NOME VÁLIDO PARA LA CATEGORIA.";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <main class="page-container" style="display: block;">
        
        <div class="form-card" style="max-width: 520px; margin: 0 auto;">
            <h2>CADASTRAR CATEGORIAS</h2>

            <!-- Mensagem de erro utilizando alert-box do style.css -->
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
            <div class="divider" style="margin-bottom: 15px;"></div>

            <div class="categories-table-card">
                <table class="categories-table">
                    <thead>
                        <tr>
                            <th>CATEGORIA</th>
                            <th style="width: 100px; text-align: center;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado->num_rows > 0): ?>
                            <?php while ($categoria = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($categoria['nomeCategoria']) ?></td>
                                    <td style="text-align: center;">
                                       <div class="table-actions" style="justify-content: center;">
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
                                <td colspan="2" style="text-align: center; color: #777;">Nenhuma categoria cadastrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Toast de Sucesso com Botão X para fechar -->
        <?php if (!empty($mensagem) && $tipoStatus === 'sucesso'): ?>
            <div class="toast-success" id="toast-mensagem">
                <i class="fa-solid fa-circle-check toast-icon"></i>
                <span class="toast-text"><?= htmlspecialchars($mensagem) ?></span>
                <button type="button" onclick="fecharToast()" style="background: none; border: none; cursor: pointer; color: #555; margin-left: 10px; font-size: 14px;" title="Fechar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        <?php endif; ?>

    </main>

    <!-- MODAL 1: ALTERAR CATEGORIA -->
    <div class="modal-overlay" id="modal-editar-categoria">
        <div class="modal-card-category">
            <button type="button" class="modal-close-btn" onclick="closeEditCategoryModal()"><i class="fa-solid fa-xmark"></i></button>
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

    <!-- MODAL 2: EXCLUIR CATEGORIA -->
    <div class="modal-overlay" id="modal-excluir-categoria">
        <div class="modal-card">
            <button type="button" class="modal-close-btn" onclick="closeDeleteCategoryModal()"><i class="fa-solid fa-xmark"></i></button>
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

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> controlStoke — Sistema de Controle de Estoque</p>
        <p><a href="politica.php">Política de Privacidade</a></p>
    </footer>

    <script>
        const modalEdit = document.getElementById('modal-editar-categoria');
        const inputIdEdit = document.getElementById('id_categoria_edit');
        const inputNovoNome = document.getElementById('novo_nome_cat');

        const modalDelete = document.getElementById('modal-excluir-categoria');
        const inputIdDelete = document.getElementById('id_categoria_delete');
        const spanNomeCategoriaExcluir = document.getElementById('nome-categoria-excluir');

        function openEditCategoryModal(idCategoria, nomeCategoria) {
            inputIdEdit.value = idCategoria;
            inputNovoNome.value = nomeCategoria;
            modalEdit.classList.add('active');
        }

        function closeEditCategoryModal() {
            modalEdit.classList.remove('active');
        }

        function openDeleteCategoryModal(idCategoria, nomeCategoria) {
            inputIdDelete.value = idCategoria;
            spanNomeCategoriaExcluir.textContent = nomeCategoria;
            modalDelete.classList.add('active');
        }

        function closeDeleteCategoryModal() {
            modalDelete.classList.remove('active');
        }

        function fecharToast() {
            const toast = document.getElementById('toast-mensagem');
            if (toast) {
                toast.style.display = 'none';
            }
        }

        window.addEventListener('click', function(e) {
            if (e.target === modalEdit) closeEditCategoryModal();
            if (e.target === modalDelete) closeDeleteCategoryModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditCategoryModal();
                closeDeleteCategoryModal();
            }
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>