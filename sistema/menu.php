<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ControlStoke</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .control-topbar {
            background-color: #0d3b66;
            height: 65px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .control-logo-img {
            height: 45px;
            width: auto;
            object-fit: contain;
        }

        .hamburger-btn {
            background: none;
            border: none;
            color: #ffffff;
            font-size: 26px;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .hamburger-btn:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .hamburger-btn:active {
            transform: scale(0.95);
        }

        .offcanvas-sidebar {
            width: 320px !important;
            background-color: #92c9e8 !important;
            box-shadow: -4px 0 15px rgba(0,0,0,0.2);
            border-left: none !important;
        }

        .sidebar-header-custom {
            height: 65px;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .close-btn-custom {
            background: none;
            border: none;
            color: #0b2545;
            font-size: 26px;
            padding: 5px;
            transition: color 0.2s ease;
        }

        .close-btn-custom:hover {
            color: #d90429;
        }

        .sidebar-menu-list a {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 25px;
            color: #0b2545;
            text-decoration: none;
            font-weight: 800;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .sidebar-menu-list a:hover {
            background-color: #7ab8de;
            color: #000000;
        }

        .sidebar-menu-list a i {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        .sidebar-bottom-custom {
            border-top: 1px solid rgba(11, 37, 69, 0.15);
            padding-bottom: 20px;
            padding-top: 10px;
        }

        /* Modal do Logout */
        .logout-modal-card {
            border: 1.5px solid #e71d36;
            border-radius: 16px;
            padding: 25px 30px;
            max-width: 420px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .logout-modal-card .logout-close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 20px;
            color: #555;
            cursor: pointer;
        }

        .logout-modal-card h3 {
            color: #e71d36;
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 12px;
            margin-top: 5px;
        }

        .logout-modal-card p {
            color: #333;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 22px;
        }

        .btn-cancel-logout {
            background-color: #e2e8f0;
            color: #333333;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 13px;
            transition: background-color 0.2s ease;
        }

        .btn-cancel-logout:hover {
            background-color: #cbd5e1;
        }

        .btn-confirm-logout {
            background-color: #e71d36;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .btn-confirm-logout:hover {
            background-color: #c9182e;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <header class="control-topbar fixed-top d-flex align-items-center justify-content-between px-3">
        <a href="home.php" class="d-flex align-items-center text-decoration-none">
            <img src="../imagem/logo.png" alt="controlStoke" class="control-logo-img">
        </a>

        <button class="hamburger-btn d-flex align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" title="Abrir Menu">
            <i class="fa-solid fa-bars"></i>
        </button>
    </header>

    <div class="offcanvas offcanvas-end offcanvas-sidebar d-flex flex-column justify-content-between p-0" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
        <div>
            <div class="sidebar-header-custom d-flex align-items-center justify-content-end px-3">
                <button type="button" class="close-btn-custom" data-bs-dismiss="offcanvas" aria-label="Close" title="Fechar Menu">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <nav class="sidebar-nav-custom pt-3">
                <ul class="list-unstyled p-0 m-0 sidebar-menu-list">
                    <li>
                        <a href="home.php"><i class="fa-solid fa-house"></i> HOME</a>
                    </li>
                    <li>
                        <a href="cadastrarProduto.php"><i class="fa-regular fa-file-lines"></i> CADASTRAR PRODUTO</a>
                    </li>
                    <li>
                        <a href="exibirEstoque.php"><i class="fa-regular fa-calendar-check"></i> ESTOQUE</a>
                    </li>
                    <li>
                        <a href="cadastrarCategoria.php"><i class="fa-solid fa-tag"></i> CADASTRAR CATEGORIA</a>
                    </li>
                    <li>
                        <a href="entradaSaida.php"><i class="fa-solid fa-right-left"></i> ENTRADA E SAÍDA ESTOQUE</a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="sidebar-bottom-custom">
            <ul class="list-unstyled p-0 m-0 sidebar-menu-list">
                <li>
                    <a href="configuracao.php"><i class="fa-solid fa-gear"></i> CONFIGURAÇÕES</a>
                </li>
                <li>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalLogout"><i class="fa-solid fa-right-from-bracket"></i> SAIR</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- MODAL DE CONFIRMAÇÃO DE SAÍDA (MODAL NATIVO DO BOOTSTRAP) -->
    <div class="modal fade" id="modalLogout" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content logout-modal-card text-center position-relative mx-auto">
                <button type="button" class="logout-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                
                <h3>SAIR DO SISTEMA</h3>
                <p>TEM CERTEZA QUE DESEJA SAIR DO CONTROLSTOKE?</p>
                
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn-cancel-logout" data-bs-dismiss="modal">CANCELAR</button>
                    <a href="sair.php" class="btn-confirm-logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>SAIR</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
