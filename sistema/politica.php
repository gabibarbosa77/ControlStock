<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade - controlStoke</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <main class="dashboard-container d-flex justify-content-center">
        
        <article class="privacy-card mx-auto">
            <div class="privacy-header-wrapper d-flex align-items-start gap-3 mb-4">
                <a href="javascript:history.back()" class="privacy-btn-back" title="Voltar">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>

                <div class="privacy-header-content d-flex align-items-center gap-3">
                    <div class="privacy-header-icon d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="privacy-header-text">
                        <h1 class="m-0">POLÍTICA DE PRIVACIDADE</h1>
                        <p class="update-date m-0">Última atualização: 09 de outubro de 2026</p>
                    </div>
                </div>
            </div>

            <p class="privacy-intro">
                A controlStoke valoriza a privacidade dos seus usuários. Esta Política de Privacidade explica como
                as informações são coletadas, utilizadas e protegidas durante o uso do nosso sistema de controle
                de estoque.
            </p>

            <div class="privacy-item d-flex gap-3 align-items-start mb-4">
                <div class="privacy-item-icon">
                    <i class="fa-regular fa-circle-user"></i>
                </div>
                <div class="privacy-item-body">
                    <h2>1. Quais dados coletamos?</h2>
                    <p>Podemos coletar informações fornecidas durante o cadastro, como nome, e-mail e dados necessários para acessar o sistema. Também podem ser armazenadas informações sobre produtos cadastrados, categorias e movimentações de estoque.</p>
                </div>
            </div>

            <div class="privacy-item d-flex gap-3 align-items-start mb-4">
                <div class="privacy-item-icon">
                    <i class="fa-solid fa-gear"></i>
                </div>
                <div class="privacy-item-body">
                    <h2>2. Como utilizamos os dados?</h2>
                    <p>As informações são utilizadas para permitir o funcionamento do controlStoke, organizar os produtos, registrar entradas e saídas, gerenciar usuários e melhorar a experiência de utilização do sistema.</p>
                </div>
            </div>

            <div class="privacy-item d-flex gap-3 align-items-start mb-4">
                <div class="privacy-item-icon">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div class="privacy-item-body">
                    <h2>3. Armazenamento e segurança</h2>
                    <p>Adotamos medidas de segurança adequadas para proteger as informações contra acessos não autorizados, alterações indevidas e perda de dados. O acesso às informações deve ser limitado às pessoas autorizadas.</p>
                </div>
            </div>

            <div class="privacy-item d-flex gap-3 align-items-start mb-4">
                <div class="privacy-item-icon">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <div class="privacy-item-body">
                    <h2>4. Compartilhamento e direitos do usuário</h2>
                    <p>Os dados não devem ser compartilhados fora das finalidades necessárias ao serviço, salvo quando houver obrigação legal. O usuário pode solicitar informações sobre seus dados e exercer os direitos previstos na legislação aplicável.</p>
                </div>
            </div>

            <div class="privacy-item d-flex gap-3 align-items-start mb-4">
                <div class="privacy-item-icon">
                    <i class="fa-regular fa-envelope"></i>
                </div>
                <div class="privacy-item-body">
                    <h2>5. Contato e alterações nesta política</h2>
                    <p>Esta política poderá ser atualizada para refletir melhorias no sistema ou mudanças legais. Em caso de dúvidas sobre privacidade, entre em contato com o responsável pelo sistema controlStoke.</p>
                </div>
            </div>
        </article>
    </main>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> controlStoke — Sistema de Controle de Estoque</p>
        <p><a href="politica.php">Política de Privacidade</a></p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>