<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../conecta/conecta.php'; 

$idUsuario = (int)$_SESSION['idUsuario'];

// 2. Consulta dos dados do utilizador
$sql = "SELECT nomeUsuario, emailUsuario, cpfUsuario, telefoneUsuario FROM tb_usuario WHERE idUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

if ($usuario = $result->fetch_assoc()) {
    $nomeUsuario     = $usuario['nomeUsuario'];
    $emailUsuario    = $usuario['emailUsuario'];
    $cpfUsuario      = $usuario['cpfUsuario'];
    $telefoneUsuario = $usuario['telefoneUsuario'];
} else {
    $nomeUsuario = $emailUsuario = $cpfUsuario = $telefoneUsuario = '';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - controlStoke</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <main class="page-container d-block">
        
        <div class="settings-header">
            <i class="fa-solid fa-gear settings-header-icon"></i>
            <div class="settings-header-text">
                <h2>CONFIGURAÇÕES DA CONTA</h2>
                <span>CONFIGURAÇÕES DA CONTA</span>
            </div>
        </div>

       <?php if (isset($_GET['status'])): ?>
            <div class="alert-container w-100 mb-4" id="box-alerta">
                <?php
                    $status = $_GET['status'];
                    
                    if ($status === 'dados_sucesso') {
                        echo '
                        <div class="alert-custom alert-sucesso">
                            <div class="alert-content">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Dados pessoais atualizados com sucesso!</span>
                            </div>
                            <button type="button" class="alert-close-btn" onclick="document.getElementById(\'box-alerta\').remove()">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>';
                    } elseif ($status === 'senha_alterada') {
                        echo '
                        <div class="alert-custom alert-sucesso">
                            <div class="alert-content">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Sua senha foi alterada com sucesso!</span>
                            </div>
                            <button type="button" class="alert-close-btn" onclick="document.getElementById(\'box-alerta\').remove()">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>';
                    } elseif ($status === 'senha_incorreta') {
                        echo '
                        <div class="alert-custom alert-erro">
                            <div class="alert-content">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <span>A senha atual informada está incorreta.</span>
                            </div>
                            <button type="button" class="alert-close-btn" onclick="document.getElementById(\'box-alerta\').remove()">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>';
                    } elseif ($status === 'senhas_diferentes') {
                        echo '
                        <div class="alert-custom alert-erro">
                            <div class="alert-content">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>A nova senha e a confirmação não coincidem.</span>
                            </div>
                            <button type="button" class="alert-close-btn" onclick="document.getElementById(\'box-alerta\').remove()">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>';
                    } elseif ($status === 'senha_fraca') {
                        echo '
                        <div class="alert-custom alert-aviso">
                            <div class="alert-content">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>A senha deve conter no mínimo 8 caracteres, incluindo letra maiúscula, minúscula, número e caractere especial.</span>
                            </div>
                            <button type="button" class="alert-close-btn" onclick="document.getElementById(\'box-alerta\').remove()">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>';
                    } elseif ($status === 'campos_vazios') {
                        echo '
                        <div class="alert-custom alert-aviso">
                            <div class="alert-content">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>Por favor, preencha todos os campos obrigatórios.</span>
                            </div>
                            <button type="button" class="alert-close-btn" onclick="document.getElementById(\'box-alerta\').remove()">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>';
                    } elseif ($status === 'cpf_invalido') {
                        echo '
                        <div class="alert-custom alert-erro">
                            <div class="alert-content">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>O CPF informado é inválido. Por favor, insira um CPF com 11 dígitos válido.</span>
                            </div>
                            <button type="button" class="alert-close-btn" onclick="document.getElementById(\'box-alerta\').remove()">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>';
                    } elseif ($status === 'telefone_invalido') {
                        echo '
                        <div class="alert-custom alert-erro">
                            <div class="alert-content">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>O telefone deve conter no mínimo 10 dígitos (DDD + número).</span>
                            </div>
                            <button type="button" class="alert-close-btn" onclick="document.getElementById(\'box-alerta\').remove()">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>';
                    }
                ?>
            </div>
        <?php endif; ?>

        <div class="settings-grid">
            
            <!-- COLUNA 1: DADOS DA CONTA -->
            <div class="settings-card">
                <div class="settings-card-title">
                    <i class="fa-regular fa-user"></i>
                    <span>DADOS DA CONTA</span>
                </div>

                <form action="alteraDados.php" method="POST" id="form-dados-conta" class="product-form">
                    
                    <div class="form-group">
                        <label for="nome_completo">Nome Completo</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-regular fa-user input-icon"></i>
                            <input type="text" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($nomeUsuario); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cpf">CPF</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-regular fa-id-card input-icon"></i>
                            <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($cpfUsuario); ?>" maxlength="14" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-regular fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($emailUsuario); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-phone input-icon"></i>
                            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($telefoneUsuario); ?>" maxlength="15" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-dark-save">SALVAR ALTERAÇÕES</button>

                </form>
            </div>

            <!-- COLUNA 2: ALTERAR SENHA + EXCLUIR CONTA -->
            <div>
                <div class="settings-card">
                    <div class="settings-card-title">
                        <i class="fa-regular fa-user"></i>
                        <span>ALTERAR SENHA</span>
                    </div>

                    <form action="alteraSenha.php" method="POST" class="product-form">
                        
                        <div class="form-group">
                            <label for="senha_atual">Senha atual</label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-lock input-icon"></i>
                                <input type="password" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual" required>
                                <i class="fa-regular fa-eye toggle-password" onclick="togglePassword('senha_atual', this)"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nova_senha">Nova senha</label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-lock input-icon"></i>
                                <input type="password" id="nova_senha" name="nova_senha" placeholder="Digite sua nova senha" required>
                                <i class="fa-regular fa-eye toggle-password" onclick="togglePassword('nova_senha', this)"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirma_senha">Confirme a nova senha</label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-lock input-icon"></i>
                                <input type="password" id="confirma_senha" name="confirma_senha" placeholder="Confirme sua nova senha" required>
                                <i class="fa-regular fa-eye toggle-password" onclick="togglePassword('confirma_senha', this)"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn-cyan-save">ATUALIZAR SENHA</button>

                    </form>
                </div>

                <!-- CARD: EXCLUIR CONTA -->
                <div class="delete-account-card">
                    <div class="delete-account-header">
                        <div class="delete-account-title">
                            <i class="fa-regular fa-trash-can"></i>
                            <span>EXCLUIR CONTA</span>
                        </div>
                        <button type="button" class="btn-outline-delete" data-bs-toggle="modal" data-bs-target="#modalExcluirConta">EXCLUIR</button>
                    </div>
                    <p class="delete-account-desc">Essa ação é permanente e remove acesso a conta!</p>
                </div>
            </div>

        </div>

    </main>

    <!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO DE CONTA (BOOTSTRAP 5) -->
    <div class="modal fade" id="modalExcluirConta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-card position-relative text-center">
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <h3 class="modal-title">EXCLUIR CONTA</h3>
                <p class="modal-text">
                    TEM CERTEZA QUE DESEJA EXCLUIR SUA CONTA? <strong>TODOS OS DADOS E REGISTROS SERÃO REMOVIDOS PERMANENTEMENTE.</strong>
                </p>
                <button type="button" class="btn-confirm-delete mx-auto" onclick="confirmDeleteAccount()">
                    <i class="fa-regular fa-trash-can"></i>
                    <span>EXCLUIR CONTA</span>
                </button>
            </div>
        </div>
    </div>

    <footer class="footer text-center">
        <p>© <?php echo date('Y'); ?> controlStoke — Sistema de Controle de Estoque</p>
        <p><a href="politica.php">Política de Privacidade</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Mostrar e ocultar senhas
    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Confirmar exclusão da conta
    function confirmDeleteAccount() {
        window.location.href = 'excluirConta.php';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const cpfInput = document.getElementById('cpf');
        const telefoneInput = document.getElementById('telefone');
        const formDados = document.getElementById('form-dados-conta');

        // Formatar CPF
        function aplicarMascaraCPF(valor) {
            let v = valor.replace(/\D/g, '');

            if (v.length > 11) {
                v = v.slice(0, 11);
            }

            if (v.length > 3) {
                v = v.replace(/^(\d{3})(\d)/, '$1.$2');
            }

            if (v.length > 7) {
                v = v.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
            }

            if (v.length > 11) {
                v = v.replace(
                    /^(\d{3})\.(\d{3})\.(\d{3})(\d)/,
                    '$1.$2.$3-$4'
                );
            }

            return v;
        }

        // Formatar telefone
        function aplicarMascaraTelefone(valor) {
            let v = valor.replace(/\D/g, '');

            if (v.length > 11) {
                v = v.slice(0, 11);
            }

            if (v.length <= 2) {
                return v;
            }

            if (v.length <= 6) {
                return '(' + v.slice(0, 2) + ') ' + v.slice(2);
            }

            if (v.length <= 10) {
                return '(' + v.slice(0, 2) + ') ' +
                    v.slice(2, 6) + '-' + v.slice(6);
            }

            return '(' + v.slice(0, 2) + ') ' +
                v.slice(2, 7) + '-' + v.slice(7);
        }

        // Aplicar máscaras ao carregar os dados
        if (cpfInput && cpfInput.value) {
            cpfInput.value = aplicarMascaraCPF(cpfInput.value);
        }

        if (telefoneInput && telefoneInput.value) {
            telefoneInput.value =
                aplicarMascaraTelefone(telefoneInput.value);
        }

        // Aplicar máscaras enquanto o usuário digita
        if (cpfInput) {
            cpfInput.addEventListener('input', function (e) {
                e.target.value = aplicarMascaraCPF(e.target.value);
            });
        }

        if (telefoneInput) {
            telefoneInput.addEventListener('input', function (e) {
                e.target.value =
                    aplicarMascaraTelefone(e.target.value);
            });
        }

        // Validar os dados antes de enviar o formulário
        if (formDados) {
            formDados.addEventListener('submit', function (e) {
                const cpfApenasNumeros = cpfInput
                    ? cpfInput.value.replace(/\D/g, '')
                    : '';

                const telefoneApenasNumeros = telefoneInput
                    ? telefoneInput.value.replace(/\D/g, '')
                    : '';

                if (cpfApenasNumeros.length !== 11) {
                    alert('O CPF deve ter exatamente 11 dígitos.');
                    e.preventDefault();
                    return;
                }

                if (telefoneApenasNumeros.length < 10) {
                    alert(
                        'O telefone deve ter no mínimo 10 dígitos (DDD + número).'
                    );
                    e.preventDefault();
                    return;
                }

                // Enviar o CPF somente com números
                cpfInput.value = cpfApenasNumeros;
            });
        }
    });
</script>
</body>
</html>