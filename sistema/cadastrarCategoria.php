<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

$idUsuario = (int)$_SESSION['idUsuario'];

//  Abrir conexão ao banco de dados
require_once '../conecta/conecta.php';

$sql = "SELECT idCategoria, nomeCategoria
            FROM tb_categoria
            WHERE idUsuario = ?
            ORDER BY nomeCategoria";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$resultado = $stmt->get_result();
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

            
            <?php if (isset($_GET['status'])): ?>
            
                <?php if ($_GET['status'] === 'sucesso'): ?>
                    <p>Categoria cadastrada com sucesso!</p>
                
                <?php elseif ($_GET['status'] === 'nome_invalido'): ?>
                    <p>Informe um nome válido para a categoria.</p>
                
                <?php elseif ($_GET['status'] === 'edicao_concluida'): ?>
                    <p>Categoria alterada com sucesso!</p>
                
                <?php elseif ($_GET['status'] === 'edicao_invalida'): ?>
                    <p>Os dados informados para a edição são inválidos.</p>
                
                <?php elseif ($_GET['status'] === 'erro_edicao'): ?>
                    <p>Não foi possível alterar a categoria.</p>

                <?php elseif ($_GET['status'] === 'exclusao_concluida'): ?>
                    <p>Categoria excluída com sucesso!</p>
                
                <?php elseif ($_GET['status'] === 'categoria_em_uso'): ?>
                    <p>Não é possível excluir esta categoria porque existem produtos vinculados a ela.</p>
                
                <?php elseif ($_GET['status'] === 'categoria_nao_encontrada'): ?>
                    <p>Categoria não encontrada ou não pertence ao usuário conectado.</p>
                
                <?php elseif ($_GET['status'] === 'exclusao_invalida'): ?>
                    <p>O ID da categoria informado é inválido.</p>
                
                <?php elseif ($_GET['status'] === 'erro_exclusao'): ?>
                    <p>Não foi possível excluir a categoria.</p>

                <?php else: ?>
                    <p>Não foi possível realizar a operação.</p>
                
                <?php endif; ?>
            <?php endif; ?>


            <form action="processa_adicionar_categoria.php" 
            method="POST" 
            class="product-form" 
            id="form-cadastrar-categoria">
            
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
                            <th style="width: 80px;">ID</th>
                            <th>CATEGORIA</th>
                            <th style="width: 100px; text-align: center;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($categoria = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= (int)$categoria['idCategoria'] ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($categoria['nomeCategoria']) ?>
                                </td>

                                <td style="text-align: center;">
                                   <div class="table-actions"
                                        style="justify-content: center;">

                                <button
                                    type="button"
                                    class="action-icon-btn delete"
                                    onclick="openDeleteCategoryModal(
                                        <?= (int)$categoria['idCategoria'] ?>,
                                        <?= htmlspecialchars(
                                            json_encode($categoria['nomeCategoria']),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    )"
                                    title="Excluir Categoria">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>

                                <button
                                    type="button"
                                    class="action-icon-btn edit"
                                    onclick="openEditCategoryModal(
                                        <?= (int)$categoria['idCategoria'] ?>,
                                        <?= htmlspecialchars(
                                            json_encode($categoria['nomeCategoria']),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    )"
                                    title="Editar Categoria">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                                
                                   </div> 
                                </td>
                            </tr>
                            <?php endwhile; ?>

                            <!-- Rhuan: Comentei essa parte para testar o código que criei encima, ele é um laço de 
                             repetição para mostrar as categorias que forem criando, para descomentar 
                                    e só selecionar todas as linhas e usar: CTRL + U -->


                        <!-- <tr> -->
                            <!-- <td><strong>01</strong></td> -->
                            <!-- <td>BEBIDA C. ALCOOL</td> -->
                            <!-- <td style="text-align: center;"> -->
                                <!-- <div class="table-actions" style="justify-content: center;"> -->
                                 <!--   Botão Excluir que abre o Modal de Exclusão --> 
                                    <!-- <button class="action-icon-btn delete" onclick="openDeleteCategoryModal('BEBIDA C. ALCOOL')" title="Excluir Categoria"> -->
                                        <!-- <i class="fa-regular fa-trash-can"></i> -->
                                    <!-- </button> -->
                                 <!--  Botão Editar que abre o Modal de Alteração --> 
                                    <!-- <button class="action-icon-btn edit" onclick="openEditCategoryModal('BEBIDA C. ALCOOL')" title="Editar Categoria"> -->
                                        <!-- <i class="fa-regular fa-pen-to-square"></i> -->
                                    <!-- </button> -->
                                <!-- </div> -->
                            <!-- </td> -->
                        <!-- </tr> -->
                        <!-- <tr> -->
                            <!-- <td><strong>02</strong></td> -->
                            <!-- <td>BEBIDA S. ALCOOL</td> -->
                            <!-- <td style="text-align: center;"> -->
                                <!-- <div class="table-actions" style="justify-content: center;"> -->
                                    <!-- <button class="action-icon-btn delete" onclick="openDeleteCategoryModal('BEBIDA S. ALCOOL')" title="Excluir Categoria"> -->
                                        <!-- <i class="fa-regular fa-trash-can"></i> -->
                                    <!-- </button> -->
                                    <!-- <button class="action-icon-btn edit" onclick="openEditCategoryModal('BEBIDA S. ALCOOL')" title="Editar Categoria"> -->
                                        <!-- <i class="fa-regular fa-pen-to-square"></i> -->
                                    <!-- </button> -->
                                <!-- </div> -->
                            <!-- </td> -->
                        <!-- </tr> -->
                        <!-- <tr> -->
                            <!-- <td><strong>03</strong></td> -->
                            <!-- <td>DOCE</td> -->
                            <!-- <td style="text-align: center;"> -->
                                <!-- <div class="table-actions" style="justify-content: center;"> -->
                                    <!-- <button class="action-icon-btn delete" onclick="openDeleteCategoryModal('DOCE')" title="Excluir Categoria"> -->
                                        <!-- <i class="fa-regular fa-trash-can"></i> -->
                                    <!-- </button> -->
                                    <!-- <button class="action-icon-btn edit" onclick="openEditCategoryModal('DOCE')" title="Editar Categoria"> -->
                                        <!-- <i class="fa-regular fa-pen-to-square"></i> -->
                                    <!-- </button> -->
                                <!-- </div> -->
                            <!-- </td> -->
                        <!-- </tr> -->
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- MODAL 1: ALTERAR CATEGORIA -->
    <div class="modal-overlay" id="modal-editar-categoria">
        <div class="modal-card-category">
            <button class="modal-close-btn" onclick="closeEditCategoryModal()"><i class="fa-solid fa-xmark"></i></button>
            <h3>ALTERAR CATEGORIA</h3>

            <form action="processa_altera_categoria.php" method="POST" class="product-form">

                 <input type="hidden"
                    id="idCategoria_editar"
                    name="idCategoria">
                    
                <div class="form-group">
                    <label for="nome_atual_cat">NOME ATUAL</label>
                    <input type="text" id="nome_atual_cat" name="nome_atual_cat" value="" readonly>
                </div>

                <hr class="form-divider" style="margin: 15px 0;">

                <div class="form-group">
                    <label for="novo_nome_cat">NOVO NOME</label>
                    <input type="text" id="novo_nome_cat" name="novo_nome_cat" required maxlength="500">
                </div>

                <button type="submit" class="btn-save">
                    <i class="fa-regular fa-floppy-disk"></i>
                    <span>SALVAR</span>
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL 2: EXCLUIR CATEGORIA (IMAGEM ENVIADA) -->
    <div class="modal-overlay" id="modal-excluir-categoria">
        <div class="modal-card">
            <button class="modal-close-btn" type="button" onclick="closeDeleteCategoryModal()"><i class="fa-solid fa-xmark"></i></button>

            <h3 class="modal-title">EXCLUIR CATEGORIA</h3>

            <p class="modal-text">
                TEM CERTEZA QUE DESEJA EXCLUIR <span id="nome-categoria-excluir">CATEGORIA</span>? 
                <strong>NÃO SERÁ POSSÍVEL RESTAURAR APÓS EXCLUSÃO E TODOS OS PRODUTOS VINCULADOS A ESSA CATEGORIA SERÃO EXCLUÍDOS PERMANENTEMENTE.</strong>
            </p>

            <form action="processa_excluir_categoria.php" method="POST">
                <input type="hidden"
                       id="idCategoria_excluir"
                       name="idCategoria">

                <button type="submit" class="btn-confirm-delete">
                    <i class="fa-regular fa-trash-can"></i>
                    <span>EXCLUIR</span>
                </button>

            </form>
        </div>
    </div>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> controlStoke — Sistema de Controle de Estoque[cite: 2]</p>
        <p><a href="politica.php">Política de Privacidade</a></p>
    </footer>

    <script>
        // Elementos Modal Editar
        const modalEdit = document.getElementById('modal-editar-categoria');
        const inputNomeAtual = document.getElementById('nome_atual_cat');
        const inputNovoNome = document.getElementById('novo_nome_cat');

        // Elementos Modal Excluir
        const modalDelete = document.getElementById('modal-excluir-categoria');
        const spanNomeCategoriaExcluir = document.getElementById('nome-categoria-excluir');

        // Funções Modal Editar
        function openEditCategoryModal(idCategoria, nomeCategoria) {
            document.getElementById('idCategoria_editar').value = idCategoria;

            document.getElementById('nome_atual_cat').value = nomeCategoria;
            document.getElementById('novo_nome_cat').value = nomeCategoria;
                                
            modalEdit.classList.add('active');
        }

        function closeEditCategoryModal() {
            modalEdit.classList.remove('active');
        }

        // Funções Modal Excluir
        function openDeleteCategoryModal(idCategoria, nomeCategoria) {
            document.getElementById('idCategoria_excluir').value = idCategoria;

            spanNomeCategoriaExcluir.textContent = nomeCategoria;
            modalDelete.classList.add('active');
        }

        function closeDeleteCategoryModal() {
            modalDelete.classList.remove('active');
        }

        function confirmDeleteCategory() {
            alert('Ação de exclusão executada com sucesso!');
            closeDeleteCategoryModal();
        }

        // Fechar ao clicar fora do modal
        window.addEventListener('click', function(e) {
            if (e.target === modalEdit) closeEditCategoryModal();
            if (e.target === modalDelete) closeDeleteCategoryModal();
        });

        // Fechar ao pressionar a tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditCategoryModal();
                closeDeleteCategoryModal();
            }
        });
    </script>
</body>
</html>