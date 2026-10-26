<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

$idUsuario = (int)$_SESSION['idUsuario'];

require_once '../conecta/conecta.php';

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">

    <!-- Bibliotecas para Geração de PDF (jsPDF e AutoTable) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
</head>
<body>

    <?php include 'menu.php'; ?>

    <main class="page-container d-block">
        
        <!-- BARRA DE PESQUISA, FILTRO E BOTÃO EXPORTAR PDF -->
        <div class="search-filter-container d-flex flex-wrap gap-2 align-items-center mb-3">
            <div class="search-box flex-grow-1">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="input-pesquisa" placeholder="pesquisar produto ou tipo de movimentação...">
            </div>

            <div class="filter-dropdown-container position-relative">
                <button class="btn-filter" id="btn-toggle-filter" type="button">
                    <i class="fa-solid fa-filter"></i>
                    <span>FILTRAR</span>
                </button>

                <div class="filter-dropdown-menu" id="filter-menu">
                    <div class="filter-dropdown-item active" data-filter="todos">
                        <i class="fa-solid fa-list"></i> Todos
                    </div>
                    <div class="filter-dropdown-item" data-filter="entradas">
                        <i class="fa-solid fa-arrow-up text-success"></i> Entradas
                    </div>
                    <div class="filter-dropdown-item" data-filter="saidas">
                        <i class="fa-solid fa-arrow-down text-danger"></i> Saídas
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

            <!-- BOTÃO EXPORTAR PDF -->
            <button class="btn btn-danger" id="btn-exportar-pdf" type="button">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </button>
        </div>

        <!-- CARD DE HISTÓRICO DE MOVIMENTAÇÕES -->
        <div class="movements-card mx-auto">
            <h2 class="movements-title">ENTRADAS E RETIRADAS DO ESTOQUE</h2>

            <div class="movement-list">
                <?php if (empty($movimentacoes)): ?>
                    <p class="no-results-msg text-center p-3 text-secondary">Nenhuma movimentação registrada.</p>
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
                <p id="mensagem-sem-resultados" class="text-center p-4 text-secondary fw-semibold d-none">
                    Nenhum produto ou movimentação encontrada.
                </p>
            </div>
        </div>

    </main>

    <footer class="footer text-center">
        <p>© <?php echo date('Y'); ?> controlStoke — Sistema de Controle de Estoque</p>
        <p><a href="politica.php">Política de Privacidade</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('input-pesquisa');
            const btnFilter = document.getElementById('btn-toggle-filter');
            const filterMenu = document.getElementById('filter-menu');
            const filterItems = document.querySelectorAll('.filter-dropdown-item');
            const movementList = document.querySelector('.movement-list');
            const msgSemResultados = document.getElementById('mensagem-sem-resultados');
            const btnExportarPdf = document.getElementById('btn-exportar-pdf');

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

                    const matchesSearch = productName.includes(query) || productDesc.includes(query);
                    let matchesType = true;

                    if (currentTypeFilter === 'entradas' && tipoMov !== 'entrada') matchesType = false;
                    if (currentTypeFilter === 'saidas' && tipoMov !== 'saida') matchesType = false;

                    if (matchesSearch && matchesType) {
                        item.classList.remove('d-none');
                        item.classList.add('d-flex');
                        visiveis++;
                    } else {
                        item.classList.remove('d-flex');
                        item.classList.add('d-none');
                    }
                });

                if (msgSemResultados) {
                    if (visiveis === 0 && items.length > 0) {
                        msgSemResultados.classList.remove('d-none');
                        msgSemResultados.classList.add('d-block');
                    } else {
                        msgSemResultados.classList.remove('d-block');
                        msgSemResultados.classList.add('d-none');
                    }
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

            searchInput.addEventListener('input', applyFilters);

            // FUNÇÃO PARA EXPORTAR O PDF COM OS DADOS FILTRADOS
            btnExportarPdf.addEventListener('click', function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                let tituloFiltro = 'Todas as Movimentações';
                if (currentTypeFilter === 'entradas') tituloFiltro = 'Relatório de Entradas';
                if (currentTypeFilter === 'saidas') tituloFiltro = 'Relatório de Saídas';

                // Cabeçalho do documento
                doc.setFontSize(16);
                doc.text('controlStoke - Relatório de Movimentações', 14, 20);
                doc.setFontSize(11);
                doc.setTextColor(100);
                doc.text(`Filtro: ${tituloFiltro}`, 14, 28);
                doc.text(`Data: ${new Date().toLocaleDateString('pt-BR')}`, 14, 34);

                // Captura apenas os elementos visíveis na página
                const visibleItems = document.querySelectorAll('.movement-item:not(.d-none)');
                const rows = [];

                visibleItems.forEach((item, index) => {
                    const nome = item.querySelector('h4') ? item.querySelector('h4').textContent : '';
                    const tipo = item.querySelector('p') ? item.querySelector('p').textContent : '';
                    const quantidade = item.querySelector('.movement-value') ? item.querySelector('.movement-value').textContent : '';
                    
                    rows.push([index + 1, nome, tipo, quantidade]);
                });

                if (rows.length === 0) {
                    alert('Nenhuma movimentação visível para exportar.');
                    return;
                }

                // Gerar Tabela no PDF
                doc.autoTable({
                    startY: 40,
                    head: [['#', 'Produto', 'Tipo / Descrição', 'Quantidade']],
                    body: rows,
                    theme: 'striped',
                    headStyles: { fillColor: [40, 116, 166] }
                });

                doc.save(`relatorio_estoque_${new Date().toISOString().slice(0, 10)}.pdf`);
            });
        });
    </script>
</body>
</html>
