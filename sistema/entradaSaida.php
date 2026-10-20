<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

$idUsuario = (int)$_SESSION['idUsuario'];

require_once '../conecta/conecta.php';

// Consulta para buscar as movimentações do usuário e o nome do produto vinculado
$sql = "SELECT es.isEntradaSaida, es.tipoEntradaSaida, es.quantidadeEntradaSaida, p.nomeProduto
        FROM tb_entradasaida es
        INNER JOIN tb_produtos p ON es.idProduto = p.idProduto
        WHERE es.idUsuario = ?
        ORDER BY es.isEntradaSaida DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();

$movimentacoes = [];
while ($row = $resultado->fetch_assoc()) {
    $movimentacoes[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entradas e Saídas - controlStoke</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <main class="page-container" style="display: block;">
        
        <!-- BARRA DE PESQUISA E FILTRO COM DROPDOWN -->
        <div class="search-filter-container">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="input-pesquisa" placeholder="pesquisar produto ou tipo de movimentação...">
            </div>

            <div class="filter-dropdown-container">
                <button class="btn-filter" id="btn-toggle-filter">
                    <i class="fa-solid fa-filter"></i>
                    <span>FILTRAR</span>
                </button>

                <div class="filter-dropdown-menu" id="filter-menu">
                    <div class="filter-dropdown-item active" data-filter="todos">
                        <i class="fa-solid fa-list"></i> Todos
                    </div>
                    <div class="filter-dropdown-item" data-filter="entradas">
                        <i class="fa-solid fa-arrow-up" style="color: #2ec4b6;"></i> Entradas
                    </div>
                    <div class="filter-dropdown-item" data-filter="saidas">
                        <i class="fa-solid fa-arrow-down" style="color: #e71d36;"></i> Saídas
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

        <!-- CARD DE HISTÓRICO DE MOVIMENTAÇÕES -->
        <div class="movements-card">
            <h2 class="movements-title">ENTRADAS E RETIRADAS DO ESTOQUE</h2>

            <div class="movement-list">
                <?php if (empty($movimentacoes)): ?>
                    <p class="no-results-msg" style="text-align: center; padding: 20px; color: #666;">Nenhuma movimentação registrada.</p>
                <?php else: ?>
                    <?php foreach ($movimentacoes as $mov): ?>
                        <?php 
                            $tipo = $mov['tipoEntradaSaida'];
                            $qtd = (int)$mov['quantidadeEntradaSaida'];

                            if ($tipo === 'adicionar') {
                                $catFiltro = 'entrada';
                                $classe = 'entry';
                                $icone = 'fa-arrow-up';
                                $descricao = 'Entrada de produtos';
                                $valorFormatado = '+' . $qtd;
                            } elseif ($tipo === 'retirada') {
                                $catFiltro = 'saida';
                                $classe = 'exit';
                                $icone = 'fa-arrow-down';
                                $descricao = 'Saída de produtos';
                                $valorFormatado = '-' . $qtd;
                            } else {
                                $catFiltro = 'entrada';
                                $classe = 'entry';
                                $icone = 'fa-sliders';
                                $descricao = 'Ajustado para ' . $qtd;
                                $valorFormatado = $qtd;
                            }
                        ?>
                        <div class="movement-item <?= $classe ?>" data-tipo="<?= $catFiltro ?>">
                            <div class="movement-info">
                                <div class="movement-icon">
                                    <i class="fa-solid <?= $icone ?>"></i>
                                </div>
                                <div class="movement-details">
                                    <h4><?= htmlspecialchars($mov['nomeProduto']) ?></h4>
                                    <p><?= htmlspecialchars($descricao) ?></p>
                                </div>
                            </div>
                            <div class="movement-value"><?= $valorFormatado ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Mensagem dinâmica caso a pesquisa não encontre correspondências -->
                <p id="mensagem-sem-resultados" style="text-align: center; padding: 25px; color: #777; font-weight: 600; display: none;">
                    Nenhum produto ou movimentação encontrada.
                </p>
            </div>
        </div>

    </main>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> controlStoke — Sistema de Controle de Estoque</p>
        <p><a href="politica.php">Política de Privacidade</a></p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('input-pesquisa');
            const btnFilter = document.getElementById('btn-toggle-filter');
            const filterMenu = document.getElementById('filter-menu');
            const filterItems = document.querySelectorAll('.filter-dropdown-item');
            const movementList = document.querySelector('.movement-list');
            const msgSemResultados = document.getElementById('mensagem-sem-resultados');

            let currentTypeFilter = 'todos';

            // Menu Dropdown de Filtro
            btnFilter.addEventListener('click', function(e) {
                e.stopPropagation();
                filterMenu.classList.toggle('active');
            });

            document.addEventListener('click', function(e) {
                if (!filterMenu.contains(e.target) && !btnFilter.contains(e.target)) {
                    filterMenu.classList.remove('active');
                }
            });

            filterItems.forEach(item => {
                item.addEventListener('click', function() {
                    const filterType = this.getAttribute('data-filter');
                    const sortType = this.getAttribute('data-sort');

                    if (filterType) {
                        document.querySelectorAll('[data-filter]').forEach(i => i.classList.remove('active'));
                        this.classList.add('active');
                        currentTypeFilter = filterType;
                        applyFilters();
                    }

                    if (sortType) {
                        sortList(sortType);
                    }

                    filterMenu.classList.remove('active');
                });
            });

            // Aplica os filtros de texto e tipo
            function applyFilters() {
                const query = searchInput.value.toLowerCase().trim();
                const items = document.querySelectorAll('.movement-item');
                let visiveis = 0;

                items.forEach(item => {
                    const productName = item.querySelector('h4') ? item.querySelector('h4').textContent.toLowerCase() : '';
                    const productDesc = item.querySelector('p') ? item.querySelector('p').textContent.toLowerCase() : '';
                    const tipoMov = item.getAttribute('data-tipo');

                    // Procura tanto no NOME do produto quanto na DESCRIÇÃO
                    const matchesSearch = productName.includes(query) || productDesc.includes(query);
                    let matchesType = true;

                    if (currentTypeFilter === 'entradas' && tipoMov !== 'entrada') matchesType = false;
                    if (currentTypeFilter === 'saidas' && tipoMov !== 'saida') matchesType = false;

                    if (matchesSearch && matchesType) {
                        item.style.display = 'flex';
                        visiveis++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Exibe a mensagem de aviso caso nenhum item seja visível
                if (msgSemResultados) {
                    msgSemResultados.style.display = (visiveis === 0 && items.length > 0) ? 'block' : 'none';
                }
            }

            // Ordenação A-Z / Z-A
            function sortList(direction) {
                const items = Array.from(document.querySelectorAll('.movement-item'));

                items.sort((a, b) => {
                    const nameA = a.querySelector('h4').textContent.toLowerCase();
                    const nameB = b.querySelector('h4').textContent.toLowerCase();

                    if (direction === 'az') return nameA.localeCompare(nameB);
                    if (direction === 'za') return nameB.localeCompare(nameA);
                    return 0;
                });

                items.forEach(item => {
                    movementList.insertBefore(item, msgSemResultados);
                });
            }

            // Evento para filtrar em tempo real ao digitar
            searchInput.addEventListener('input', applyFilters);
        });
    </script>
</body>
</html>