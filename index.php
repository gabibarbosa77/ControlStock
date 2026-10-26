<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>controlStoke - Sistema de Controle de Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- NAVBAR FIXA -->
    <header class="navbar">
        <a href="index.php" class="logo">
            <img src="imagem/logo.png" alt="controlStoke Logo" class="logo-img">
        </a>
        <div class="nav-buttons">
            <a href="login.php" class="btn-nav btn-login">LOGIN</a>
            <a href="cadastro.php" class="btn-nav btn-cadastro">CADASTRO</a>
        </div>
    </header>

    <main class="flex-grow-1">
        <!-- HERO SECTION: Afastada do menu com pt-5 mt-4 e centralizada com d-flex justify-content-center -->
        <section class="hero-section pt-5 mt-4 pb-4 d-flex justify-content-center">
            <div class="hero-card text-center mx-auto">
                <h1>Seu estoque sob controle!</h1>
                <p>Gerencie produtos, entradas, saídas e pedidos de forma simples e organizada.</p>
                <a href="login.php" class="btn-acessar">ACESSAR</a>
            </div>
        </section>

        <!-- SEÇÃO PRINCIPAL -->
        <section class="why-section my-5">
            <h2 class="section-title">POR QUE O CONTROL STOKE?</h2>
            <div class="divider"></div>

            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-header green">
                        <i class="fa-solid fa-box"></i>
                        <h3>Controle de estoque</h3>
                    </div>
                    <ul>
                        <li>Cadastre seus produtos em um só lugar!</li>
                        <li>Organize por código de barras.</li>
                    </ul>
                </div>

                <div class="feature-item">
                    <div class="feature-header orange">
                        <i class="fa-solid fa-warehouse"></i>
                        <h3>Acompanhamento do estoque</h3>
                    </div>
                    <ul>
                        <li>Acompanhe entradas e saídas dos produtos.</li>
                        <li>Histórico completo de movimentações recentes.</li>
                        <li>Registro do responsável por cada alteração.</li>
                    </ul>
                </div>

                <div class="feature-item">
                    <div class="feature-header red">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <h3>Alerta de baixo estoque</h3>
                    </div>
                    <ul>
                        <li>Saiba quais produtos precisam de reposição.</li>
                        <li>Evite perder vendas por falta de mercadoria.</li>
                    </ul>
                </div>

                <div class="feature-item">
                    <div class="feature-header purple">
                        <i class="fa-solid fa-tags"></i>
                        <h3>Categorização Inteligente</h3>
                    </div>
                    <ul>
                        <li>Separe produtos por categorias.</li>
                        <li>Filtros rápidos para encontrar itens em segundos.</li>
                    </ul>
                </div>

                <div class="feature-item">
                    <div class="feature-header blue">
                        <i class="fa-solid fa-chart-line"></i>
                        <h3>Relatórios e Métricas</h3>
                    </div>
                    <ul>
                        <li>Resumos visuais do valor total em estoque.</li>
                        <li>Exportação de relatórios rápidos.</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- COMO FUNCIONA -->
        <section class="steps-section my-5">
            <h2 class="section-title">COMO FUNCIONA?</h2>
            <div class="divider"></div>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">Passo 01</div>
                    <h4>Cadastre os Produtos</h4>
                    <p>Insira nome, código e quantidade inicial do seu produto no sistema.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">Passo 02</div>
                    <h4>Lance Movimentações</h4>
                    <p>Registre entradas de novas compras e saídas de produtos vendidas.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">Passo 03</div>
                    <h4>Acompanhe os Alertas</h4>
                    <p>Receba avisos automáticos de itens com estoque zerado ou baixo.</p>
                </div>
            </div>
        </section>

        <!-- DICAS -->
        <section class="tips-section my-5">
            <div class="tips-box">
                <h3><i class="fa-solid fa-lightbulb"></i> Dicas para uma boa gestão de estoque</h3>
                <ul class="tips-list">
                    <li><i class="fa-solid fa-circle-check"></i> Faça a contagem física periodicamente.</li>
                    <li><i class="fa-solid fa-circle-check"></i> Defina um limite mínimo de segurança para cada item.</li>
                    <li><i class="fa-solid fa-circle-check"></i> Registre toda entrada e saída no mesmo dia.</li>
                    <li><i class="fa-solid fa-circle-check"></i> Mantenha suas categorias bem organizadas.</li>
                </ul>
            </div>
        </section>
    </main>

    <footer class="footer mt-auto">
        <p>© <?php echo date('Y'); ?> controlStoke — Sistema de Controle de Estoque</p>
        <p><a href="sistema/politica.php">Política de Privacidade</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>