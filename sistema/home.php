<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

$idUsuario = (int)$_SESSION['idUsuario'];

require_once '../conecta/conecta.php';

// CONSULTA NOME DO USUÁRIO
$nomeUsuario = "Usuário";
$sqlUser = "SELECT nomeUsuario FROM tb_usuario WHERE idUsuario = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("i", $idUsuario);
$stmtUser->execute();
$resUser = $stmtUser->get_result();
if ($rowUser = $resUser->fetch_assoc()) {
    $nomeUsuario = $rowUser['nomeUsuario'];
}
$stmtUser->close();

// MÉTRICAS DE PRODUTOS E ESTOQUE
$totalProdutos = 0;
$produtosEmEstoque = 0;
$estoqueBaixo = 0;
$estoqueZerado = 0;

$sqlMetricas = "SELECT estoqueProduto, estoqueMinimo FROM tb_produtos WHERE idUsuario = ?";
$stmtMetricas = $conn->prepare($sqlMetricas);
$stmtMetricas->bind_param("i", $idUsuario);
$stmtMetricas->execute();
$resMetricas = $stmtMetricas->get_result();

while ($row = $resMetricas->fetch_assoc()) {
    $totalProdutos++;
    $qtd = (int)$row['estoqueProduto'];
    $min = (int)$row['estoqueMinimo'];

    $produtosEmEstoque += $qtd;

    if ($qtd == 0) {
        $estoqueZerado++;
    } elseif ($qtd <= $min) {
        $estoqueBaixo++;
    }
}
$stmtMetricas->close();

// MOVIMENTAÇÕES RECENTES (ÚLTIMAS 3)
$sqlMov = "SELECT es.tipoEntradaSaida, es.quantidadeEntradaSaida, p.nomeProduto
           FROM tb_entradasaida es
           INNER JOIN tb_produtos p ON es.idProduto = p.idProduto
           WHERE es.idUsuario = ?
           ORDER BY es.isEntradaSaida DESC
           LIMIT 3";

$stmtMov = $conn->prepare($sqlMov);
$stmtMov->bind_param("i", $idUsuario);
$stmtMov->execute();
$resMov = $stmtMov->get_result();

$movimentacoesRecentes = [];
while ($rowMov = $resMov->fetch_assoc()) {
    $movimentacoesRecentes[] = $rowMov;
}
$stmtMov->close();

// 4. ITENS CRÍTICOS PARA REPOSIÇÃO (ZERADOS OU BAIXOS)
$sqlCriticos = "SELECT p.codigoBarras, p.nomeProduto, p.estoqueProduto, p.estoqueMinimo, c.nomeCategoria
                FROM tb_produtos p
                LEFT JOIN tb_categoria c ON p.idCategoria = c.idCategoria
                WHERE p.idUsuario = ? AND p.estoqueProduto <= p.estoqueMinimo
                ORDER BY p.estoqueProduto ASC
                LIMIT 5";

$stmtCriticos = $conn->prepare($sqlCriticos);
$stmtCriticos->bind_param("i", $idUsuario);
$stmtCriticos->execute();
$resCriticos = $stmtCriticos->get_result();

$itensCriticos = [];
while ($rowCritico = $resCriticos->fetch_assoc()) {
    $itensCriticos[] = $rowCritico;
}
$stmtCriticos->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Principal - controlStoke</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <main class="dashboard-container">
        
        <div class="dashboard-header text-center">
            <h1>Seja bem-vindo(a), <?= htmlspecialchars($nomeUsuario) ?>!</h1>
            <p>Confira um resumo do estoque atual</p>
        </div>

        <div class="dashboard-main-grid">
            
            <div class="dashboard-sidebar-column">
                
                <?php if ($estoqueZerado > 0): ?>
                    <div class="alert-card-danger">
                        <h3>ATENÇÃO</h3>
                        <p>EXISTEM <?= $estoqueZerado ?> PRODUTO(S) COM O ESTOQUE ZERADO!!</p>
                    </div>
                <?php endif; ?>

                <div class="recent-movements-card">
                    <h4>Movimentações Recentes</h4>
                    <ul class="movement-list p-0 m-0">
                        <?php if (empty($movimentacoesRecentes)): ?>
                            <li class="movement-item p-2 text-center text-muted">Sem movimentações recentes.</li>
                        <?php else: ?>
                            <?php foreach ($movimentacoesRecentes as $mov): ?>
                                <?php
                                    $tipo = $mov['tipoEntradaSaida'];
                                    $qtd = (int)$mov['quantidadeEntradaSaida'];

                                    if ($tipo === 'adicionar') {
                                        $classeIcone = 'in';
                                        $icone = 'fa-arrow-up';
                                        $descricao = 'Entrada de produtos';
                                        $valorClasse = 'positive';
                                        $valorFormatado = '+' . $qtd;
                                    } elseif ($tipo === 'retirada') {
                                        $classeIcone = 'out';
                                        $icone = 'fa-arrow-down';
                                        $descricao = 'Saída de produtos';
                                        $valorClasse = 'negative';
                                        $valorFormatado = '-' . $qtd;
                                    } else {
                                        $classeIcone = 'in';
                                        $icone = 'fa-sliders';
                                        $descricao = 'Ajustado para ' . $qtd;
                                        $valorClasse = '';
                                        $valorFormatado = $qtd;
                                    }
                                ?>
                                <li class="movement-item">
                                    <div class="movement-info">
                                        <div class="movement-icon <?= $classeIcone ?>">
                                            <i class="fa-solid <?= $icone ?>"></i>
                                        </div>
                                        <div class="movement-details d-flex flex-column align-items-start">
                                            <strong class="d-block"><?= htmlspecialchars($mov['nomeProduto']) ?></strong>
                                            <small class="text-muted d-block"><?= $descricao ?></small>
                                        </div>
                                    </div>
                                    <span class="movement-value <?= $valorClasse ?>"><?= $valorFormatado ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="metrics-grid">
                
                <div class="metric-card blue">
                    <div class="icon-box">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div class="number"><?= $totalProdutos ?></div>
                    <div class="label">Total de Produtos</div>
                </div>

                <div class="metric-card green">
                    <div class="icon-box">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div class="number"><?= $produtosEmEstoque ?></div>
                    <div class="label">Produtos em Estoque</div>
                </div>

                <div class="metric-card yellow">
                    <div class="icon-box">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="number"><?= $estoqueBaixo ?></div>
                    <div class="label">Estoque Baixo</div>
                </div>

                <div class="metric-card red">
                    <div class="icon-box">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>
                    <div class="number"><?= $estoqueZerado ?></div>
                    <div class="label">Estoque Zerado</div>
                </div>

            </div>
        </div>

        <div class="section-subtitle-bar">
            <h3>Ações Rápidas</h3>
            <div class="line"></div>
        </div>

        <div class="quick-actions-grid">
            <a href="cadastrarProduto.php" class="action-btn-card">
                <i class="fa-regular fa-square-plus"></i>
                <span>Novo Produto</span>
            </a>
            <a href="entradaSaida.php" class="action-btn-card">
                <i class="fa-solid fa-right-left"></i>
                <span>Registros Entrada/Saída</span>
            </a>
            <a href="exibirEstoque.php" class="action-btn-card">
                <i class="fa-regular fa-calendar-check"></i>
                <span>Consultar Estoque</span>
            </a>
            <a href="cadastrarCategoria.php" class="action-btn-card">
                <i class="fa-solid fa-tag"></i>
                <span>Nova Categoria</span>
            </a>
        </div>

        <div class="section-subtitle-bar">
            <h3>Itens Críticos para Reposição</h3>
            <div class="line"></div>
        </div>

        <div class="table-container table-responsive">
            <table class="custom-table align-middle">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Qtd. Atual</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($itensCriticos)): ?>
                        <tr>
                            <td colspan="5" class="text-center p-3">
                                Nenhum item em estado crítico no momento.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($itensCriticos as $item): ?>
                            <?php 
                                $qtdItem = (int)$item['estoqueProduto'];
                                if ($qtdItem == 0) {
                                    $badgeCor = 'red';
                                    $badgeTexto = 'Zerado';
                                } else {
                                    $badgeCor = 'yellow';
                                    $badgeTexto = 'Estoque Baixo';
                                }
                            ?>
                            <tr>
                                <td>#<?= htmlspecialchars($item['codigoBarras']) ?></td>
                                <td><strong><?= htmlspecialchars($item['nomeProduto']) ?></strong></td>
                                <td><?= htmlspecialchars($item['nomeCategoria'] ?? 'Sem Categoria') ?></td>
                                <td><?= $qtdItem ?> Unid.</td>
                                <td><span class="status-badge <?= $badgeCor ?>"><?= $badgeTexto ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>

    <footer class="footer text-center">
        <p>© <?php echo date('Y'); ?> controlStoke — Sistema de Controle de Estoque</p>
        <p><a href="politica.php">Política de Privacidade</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
