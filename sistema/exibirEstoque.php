<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

$idUsuario = (int)$_SESSION['idUsuario'];

require_once '../conecta/conecta.php';

// Consulta SQL para listar produtos e nome da categoria vinculada
$sqlProdutos = "SELECT p.idProduto, p.nomeProduto, p.codigoBarras, p.estoqueProduto, 
                       p.precoProduto, p.estoqueMinimo, c.nomeCategoria
                FROM tb_produtos p
                LEFT JOIN tb_categoria c ON p.idCategoria = c.idCategoria
                WHERE p.idUsuario = ?
                ORDER BY p.nomeProduto ASC";

$stmt = $conn->prepare($sqlProdutos);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();

$produtos = [];
$totalProdutos = 0;
$estoqueBaixo = 0;
$estoqueZerado = 0;

// Processa as métricas e prepara a lista de produtos
while ($row = $resultado->fetch_assoc()) {
    $totalProdutos++;
    $qtd = (int)$row['estoqueProduto'];
    $min = (int)$row['estoqueMinimo'];

    // Lógica das cores e contadores de métricas
    if ($qtd == 0) {
        $corStatus = 'red';
        $textoStatus = 'Estoque Zerado';
        $estoqueZerado++;
    } elseif ($qtd <= $min) {
        $corStatus = 'orange';
        $textoStatus = 'Estoque Baixo';
        $estoqueBaixo++;
    } else {
        $corStatus = 'green';
        $textoStatus = 'Estoque Normal';
    }

    $row['corStatus'] = $corStatus;
    $row['textoStatus'] = $textoStatus;
    $produtos[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque - controlStoke</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <main class="page-container d-block">
        
        <div class="search-filter-container">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="input-pesquisa" placeholder="pesquisar produto...">
            </div>

            <div class="filter-dropdown-container">
                <button class="btn-filter" id="btn-toggle-filter" type="button">
                    <i class="fa-solid fa-filter"></i>
                    <span>FILTRAR</span>
                </button>

                <div class="filter-dropdown-menu" id="filter-menu">
                    <div class="filter-dropdown-item active" data-filter="todos">
                        <i class="fa-solid fa-list"></i> Todos
                    </div>
                    <div class="filter-dropdown-item" data-filter="green">
                        <i class="fa-solid fa-circle text-success"></i> Estoque Normal
                    </div>
                    <div class="filter-dropdown-item" data-filter="orange">
                        <i class="fa-solid fa-circle text-warning"></i> Estoque Baixo
                    </div>
                    <div class="filter-dropdown-item" data-filter="red">
                        <i class="fa-solid fa-circle text-danger"></i> Estoque Zerado
                    </div>
                    <div class="filter-dropdown-divider"></div>
                    <div class="filter-dropdown-item" data-sort="az">
                        <i class="fa-solid fa-arrow-down-a-z"></i> De A a Z
                    </div>
                    <div class="filter-dropdown-item" data-sort="za">
                        <i class="fa-solid fa-arrow-up-z-a"></i> De Z a A
                    </div>
                </div>
            </div>
        </div>

        <!-- CARDS DE MÉTRICAS DO ESTOQUE -->
        <div class="stock-metrics-grid">
            <div class="metric-card blue">
                <div class="icon-box">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div class="number"><?= $totalProdutos ?></div>
                <div class="label">PRODUTOS</div>
            </div>

            <div class="metric-card yellow">
                <div class="icon-box">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="number"><?= $estoqueBaixo ?></div>
                <div class="label">ESTOQUE BAIXO</div>
            </div>

            <div class="metric-card red">
                <div class="icon-box">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <div class="number"><?= $estoqueZerado ?></div>
                <div class="label">ESTOQUE ZERADO</div>
            </div>
        </div>

        <!-- TABELA DE PRODUTOS CADASTRADOS -->
        <div class="table-card">
            <h3 class="table-card-title">PRODUTOS CADASTRADOS</h3>
            <div class="divider"></div>

            <div class="table-responsive">
                <table class="stock-table align-middle">
                    <thead>
                        <tr>
                            <th>PRODUTO</th>
                            <th>CÓDIGO BARRAS</th>
                            <th>CATEGORIA</th>
                            <th>R$</th>
                            <th>QUANT.</th>
                            <th>ESTOQUE</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody id="stock-tbody">
                        <?php if (empty($produtos)): ?>
                            <tr>
                                <td colspan="7" class="text-center p-3">Nenhum produto cadastrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($produtos as $prod): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($prod['nomeProduto']) ?></strong></td>
                                    <td><?= htmlspecialchars($prod['codigoBarras']) ?></td>
                                    <td><?= htmlspecialchars($prod['nomeCategoria'] ?? 'Sem Categoria') ?></td>
                                    <td>R$ <?= number_format($prod['precoProduto'], 2, ',', '.') ?></td>
                                    <td><?= (int)$prod['estoqueProduto'] ?></td>
                                    <td>
                                        <span class="stock-status-dot <?= $prod['corStatus'] ?>" title="<?= $prod['textoStatus'] ?>"></span>
                                    </td>
                                    <td>
                                        <div class="table-actions d-flex align-items-center gap-2">
                                            <button type="button" class="action-icon-btn delete" 
                                                    onclick="openDeleteModal(<?= (int)$prod['idProduto'] ?>, '<?= htmlspecialchars($prod['nomeProduto'], ENT_QUOTES) ?>')" 
                                                    title="Excluir">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                            <a href="alteraProduto.php?id=<?= (int)$prod['idProduto'] ?>" class="action-icon-btn edit" title="Alterar Produto">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <a href="alteraEstoque.php?id=<?= (int)$prod['idProduto'] ?>" class="action-icon-btn box" title="Alterar/Entrada Estoque">
                                                <i class="fa-solid fa-box-open"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- MODAL DE EXCLUSÃO -->
    <div class="modal fade" id="modalExcluir" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-card position-relative">
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <h3 class="modal-title">EXCLUIR PRODUTO</h3>
                <p class="modal-text">
                    TEM CERTEZA QUE DESEJA EXCLUIR <span id="nome-produto-modal">PRODUTO</span>? NÃO SERÁ POSSÍVEL RESTAURAR APÓS EXCLUSÃO.
                </p>

                <form action="excluirProduto.php" method="POST">
                    <input type="hidden" name="id_produto" id="id_produto_delete">
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
        /* Instância do Modal Bootstrap */
        let bsDeleteModal;
        const nomeProdutoSpan = document.getElementById('nome-produto-modal');
        const inputIdDelete = document.getElementById('id_produto_delete');

        document.addEventListener('DOMContentLoaded', function() {
            bsDeleteModal = new bootstrap.Modal(document.getElementById('modalExcluir'));
        });

        function openDeleteModal(idProduto, nomeProduto) {
            inputIdDelete.value = idProduto;
            nomeProdutoSpan.textContent = nomeProduto;
            bsDeleteModal.show();
        }

        /* Filtro e Ordenação */
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('input-pesquisa');
            const btnFilter = document.getElementById('btn-toggle-filter');
            const filterMenu = document.getElementById('filter-menu');
            const filterItems = document.querySelectorAll('.filter-dropdown-item');
            const stockTbody = document.getElementById('stock-tbody');

            let currentStatusFilter = 'todos';

            // Toggle Dropdown
            btnFilter.addEventListener('click', function(e) {
                e.stopPropagation();
                filterMenu.classList.toggle('active');
            });

            // Fechar ao clicar fora
            document.addEventListener('click', function(e) {
                if (!filterMenu.contains(e.target) && !btnFilter.contains(e.target)) {
                    filterMenu.classList.remove('active');
                }
            });

            // Ações dos filtros
            filterItems.forEach(item => {
                item.addEventListener('click', function() {
                    const filterType = this.getAttribute('data-filter');
                    const sortType = this.getAttribute('data-sort');

                    if (filterType) {
                        document.querySelectorAll('[data-filter]').forEach(i => i.classList.remove('active'));
                        this.classList.add('active');
                        currentStatusFilter = filterType;
                        applyFilters();
                    }

                    if (sortType) {
                        sortTable(sortType);
                    }

                    filterMenu.classList.remove('active');
                });
            });

            function applyFilters() {
                const query = searchInput.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#stock-tbody tr');

                rows.forEach(row => {
                    if (row.cells.length < 7) return; // Ignora linha de mensagem vazia

                    const productName = row.cells[0].textContent.toLowerCase();
                    const barcode = row.cells[1].textContent.toLowerCase();
                    const category = row.cells[2].textContent.toLowerCase();
                    const dotSpan = row.querySelector('.stock-status-dot');

                    let matchesSearch = productName.includes(query) || barcode.includes(query) || category.includes(query);
                    let matchesStatus = true;

                    if (currentStatusFilter !== 'todos') {
                        matchesStatus = dotSpan && dotSpan.classList.contains(currentStatusFilter);
                    }

                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            function sortTable(direction) {
                const rows = Array.from(document.querySelectorAll('#stock-tbody tr'));

                if (rows.length === 1 && rows[0].cells.length < 7) return;

                rows.sort((a, b) => {
                    const nameA = a.cells[0].textContent.toLowerCase();
                    const nameB = b.cells[0].textContent.toLowerCase();

                    if (direction === 'az') return nameA.localeCompare(nameB);
                    if (direction === 'za') return nameB.localeCompare(nameA);
                    return 0;
                });

                rows.forEach(row => stockTbody.appendChild(row));
            }

            searchInput.addEventListener('input', applyFilters);
        });
    </script>
</body>
</html>
