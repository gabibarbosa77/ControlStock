<?php
$mensagem = '';
$tipoMensagem = '';

if (isset($_GET['erro'])) {
    $tipoMensagem = 'erro';
    switch ($_GET['erro']) {
        case 'dados_invalidos':
            $mensagem = 'Usuário ou senha incorretos.';
            break;
        case 'campos_vazios':
            $mensagem = 'Preencha todos os campos para entrar.';
            break;
        case 'nao_autenticado':
            $mensagem = 'Você precisa fazer login para acessar essa página.';
            break;
        default:
            $mensagem = 'Ocorreu um erro ao tentar fazer o login.';
            break;
    }
} elseif (isset($_GET['sucesso'])) {
    $tipoMensagem = 'sucesso';
    if ($_GET['sucesso'] === 'cadastrado') {
        $mensagem = 'Cadastro realizado com sucesso! Faça seu login abaixo.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - controlStoke</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-body">

    <div class="login-card mx-auto">
        
        <!-- CABEÇALHO COM LINHAS DOURADAS PERFEITAMENTE SIMÉTRICAS -->
        <div class="login-header">
            <span class="line"></span>
            <h2>LOGIN</h2>
            <span class="line"></span>
        </div>
        <p class="login-subtitle">Faça seu login</p>

        <!-- MENSAGEM DE ERRO/SUCESSO -->
        <?php if (!empty($mensagem)): ?>
            <div class="alert-box <?php echo $tipoMensagem; ?>">
                <i class="fa-solid <?php echo $tipoMensagem === 'erro' ? 'fa-triangle-exclamation' : 'fa-circle-check'; ?>"></i>
                <span><?php echo htmlspecialchars($mensagem); ?></span>
            </div>
        <?php endif; ?>

        <form action="processaLogin.php" method="POST">
            <div class="form-group mb-3">
                <label for="usuario">USUÁRIO</label>
                <div class="input-container">
                    <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Digite seu usuário ou e-mail" required autocomplete="off">
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="senha">SENHA</label>
                <div class="input-container">
                    <input type="password" id="senha" name="senha" class="form-control" placeholder="Digite sua senha" required>
                    <i class="fa-regular fa-eye toggle-password" id="btn-toggle-password" title="Mostrar/Ocultar Senha"></i>
                </div>
            </div>

            <p class="login-register-text my-3 text-center">
                Não possui cadastro? <a href="cadastro.php">Clique aqui!</a>
            </p>

            <div class="text-center">
                <button type="submit" class="btn-login-submit">ENTRAR</button>
            </div>

            <p class="login-register-text mt-3 mb-0 text-center">
                Voltar ao <a href="index.php">início!</a>
            </p>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('btn-toggle-password');
            const passwordInput = document.getElementById('senha');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>