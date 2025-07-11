<?php
session_start();
ob_start();

$page = '';
if (isset($_GET["page"]))
  $page = $_GET["page"];

$page_file = "";
?>

<!doctype html>
<html lang="pt-PT">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Painel Administrativo</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    :root {
      --primary-color: #5d87ff;
      --secondary-color: #49beff;
      --success-color: #13deb9;
      --warning-color: #ffae1f;
      --danger-color: #fa896b;
    }
    
  
   

    .nav-small-cap {
      color: rgba(255, 255, 255, 0.5);
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      padding: 12px 15px;
    }
    
    
    
    .app-header {
      background: #ffffff;
      box-shadow: 0 1px 15px rgba(0, 0, 0, 0.05);
      border-bottom: 1px solid #e5e9f2;
    }
    
    .body-wrapper-inner {
      background-color: #f5f7fb;
      min-height: calc(100vh - 70px);
    }
    
    .menu-icon {
      width: 20px;
      text-align: center;
      margin-right: 8px;
      color: rgba(207, 25, 25, 0.7);
    }
    
    .submenu-icon {
      font-size: 0.7rem;
      color: rgba(255, 255, 255, 0.5);
    }
    
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }
    
    .badge-notification {
      position: absolute;
      top: -5px;
      right: -5px;
    }
    /* Novo estilo para corrigir o problema */
    .body-wrapper {
      margin-top: 70px; /* Altura da navbar */
    }
    
    .app-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      height: 70px;
      background: #ffffff;
      box-shadow: 0 1px 15px rgba(0, 0, 0, 0.05);
      border-bottom: 1px solid #e5e9f2;
    }
    
    .body-wrapper-inner {
      padding-top: 20px; /* Espaçamento adicional */
    }

    /* Ajuste para quando a sidebar estiver recolhida */
    @media (min-width: 768px) {
      .body-wrapper {
        margin-left: 250px; /* Largura da sidebar */
      }
      
      .app-header {
        left: 250px; /* Compensar pela sidebar */
      }
    }
     .page-wrapper {
      padding-top: 70px; /* Altura da navbar */
    }
    
    .left-sidebar {
      top: 70px; /* Começa abaixo da navbar */
      height: calc(100vh - 70px); /* Altura total menos navbar */
    }
    
    
    .navbar {
      width: 100%;
      padding: 0 20px;
    }
    
   
    
    .body-wrapper {
      margin-left: 0; /* Reset para mobile */
      padding-top: 0; /* Já temos padding no page-wrapper */
    }
    
    @media (min-width: 768px) {
      .body-wrapper {
        margin-left: 250px; /* Largura da sidebar */
      }
      
      .left-sidebar {
        left: 0;
        width: 250px;
      }
      
      .app-header {
        left: 250px; /* Compensar pela sidebar */
        width: calc(100% - 250px);
      }
    }
    
    
    
  </style>
</head>

<body>
  <!-- Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="./" class="text-nowrap logo-img d-flex align-items-center">
            <img src="assets/images/logos/logo.png" width="100px" height="100px" alt="Logo" class="me-2" />
            <span class="text fw-bold">Admin Panel</span>
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-6"></i>
          </div>
        </div>
        
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap">
             
            </li>

            <!-- Menu Principal -->
            <li class="sidebar-item">
              <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)" aria-expanded="false">
                <div class="d-flex align-items-center gap-3">
                  <span class="d-flex">
                    <i class="fas fa-cog menu-icon"></i>
                  </span>
                  <span class="hide-menu">Gestão</span>
                </div>
                <i class="fas fa-chevron-down submenu-icon"></i>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <!-- Categorias -->
                <li class="sidebar-item">
                  <a class="sidebar-link justify-content-between <?= $page == 'viewCategoria' ? 'active' : '' ?>" 
                    href="?page=viewCategoria">
                    <div class="d-flex align-items-center gap-3">
                      <i class="fas fa-tags submenu-icon"></i>
                      <span class="hide-menu">Categorias</span>
                    </div>
                  </a>
                </li>

                <!-- Sub-Categorias -->
                <li class="sidebar-item">
                  <a class="sidebar-link justify-content-between <?= $page == 'viewSubCategoria' ? 'active' : '' ?>" 
                    href="?page=viewSubCategoria">
                    <div class="d-flex align-items-center gap-3">
                      <i class="fas fa-tag submenu-icon"></i>
                      <span class="hide-menu">Sub-Categorias</span>
                    </div>
                  </a>
                </li>

                <!-- Produtos -->
                <li class="sidebar-item">
                  <a class="sidebar-link justify-content-between <?= $page == 'viewProduto' ? 'active' : '' ?>" 
                    href="?page=viewProduto">
                    <div class="d-flex align-items-center gap-3">
                      <i class="fas fa-box submenu-icon"></i>
                      <span class="hide-menu">Produtos</span>
                    </div>
                  </a>
                </li>

                <!-- Imagens -->
                <li class="sidebar-item">
                  <a class="sidebar-link justify-content-between <?= $page == 'viewImagem' ? 'active' : '' ?>" 
                    href="?page=viewImagem">
                    <div class="d-flex align-items-center gap-3">
                      <i class="fas fa-image submenu-icon"></i>
                      <span class="hide-menu">Imagens</span>
                    </div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Vendas -->
            <li class="sidebar-item">
              <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)" aria-expanded="false">
                <div class="d-flex align-items-center gap-3">
                  <span class="d-flex">
                    <i class="fas fa-shopping-cart menu-icon"></i>
                  </span>
                  <span class="hide-menu">Vendas</span>
                </div>
                <i class="fas fa-chevron-down submenu-icon"></i>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <!-- Encomendas -->
                <li class="sidebar-item">
                  <a class="sidebar-link justify-content-between <?= $page == 'viewEncomenda' ? 'active' : '' ?>" 
                    href="?page=viewEncomenda">
                    <div class="d-flex align-items-center gap-3">
                      <i class="fas fa-clipboard-list submenu-icon"></i>
                      <span class="hide-menu">Encomendas</span>
                    </div>
                  </a>
                </li>

                <!-- Status Encomendas -->
                <li class="sidebar-item">
                  <a class="sidebar-link justify-content-between <?= $page == 'viewStatusEncomenda' ? 'active' : '' ?>" 
                    href="?page=viewStatusEncomenda">
                    <div class="d-flex align-items-center gap-3">
                      <i class="fas fa-truck submenu-icon"></i>
                      <span class="hide-menu">Status Encomendas</span>
                    </div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Utilizadores -->
            <li class="sidebar-item">
              <a class="sidebar-link justify-content-between <?= $page == 'viewUtilizador' ? 'active' : '' ?>" 
                href="?page=viewUtilizador">
                <div class="d-flex align-items-center gap-3">
                  <i class="fas fa-users menu-icon"></i>
                  <span class="hide-menu">Utilizadores</span>
                </div>
              </a>
            </li>
          </ul>
        </nav>
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
      
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav">
              <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                    <img src="./assets/images/profile/user-1.jpg" alt="User" class="user-avatar me-2">
                    <span class="fw-medium"><?= $_SESSION['user']['username'] ?></span>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item text-danger" href="./bd/logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i> Sair
                      </a>
                    </li>
                  </ul>
                </li>
              <?php endif; ?>
            </ul>
          </div>
        </nav>
      </header>
      <!--  Header End -->
      
      <div class="body-wrapper-inner">
        <div class="container-fluid py-4">
          <?php
          switch ($page) {
            // Categoria
            case 'viewCategoria':
              $page_file = "./admin/Categorias/viewCategoria.php";
              break;
            case 'createCategoria':
              $page_file = "./admin/Categorias/createCategoria.php";
              break;
            case 'editCategoria':
              $page_file = "./admin/Categorias/editCategoria.php";
              break;
            case 'deleteCategoria':
              $page_file = "./admin/Categorias/deleteCategoria.php";
              break;

            // SubCategoria
            case 'viewSubCategoria':
              $page_file = "./admin/SubCategoria/viewSubCategoria.php";
              break;
            case 'createSubCategoria':
              $page_file = "./admin/SubCategoria/createSubCategoria.php";
              break;
            case 'editSubCategoria':
              $page_file = "./admin/SubCategoria/editSubCategoria.php";
              break;
            case 'deleteSubCategoria':
              $page_file = "./admin/SubCategoria/deleteSubCategoria.php";
              break;

            // Imagem
            case 'viewImagem':
              $page_file = "./admin/Imagem/viewImagem.php";
              break;
            case 'createImagem':
              $page_file = "./admin/Imagem/createImagem.php";
              break;
            case 'editImagem':
              $page_file = "./admin/Imagem/editImagem.php";
              break;
            case 'deleteImagem':
              $page_file = "./admin/Imagem/deleteImagem.php";
              break;
            
            // Status Encomendas
            case 'viewStatusEncomenda':
              $page_file = "./admin/StatusEncomendas/viewStatusEncomenda.php";
              break;
            case 'createStatusEncomenda':
              $page_file = "./admin/StatusEncomendas/createStatusEncomenda.php";
              break;
            case 'editStatusEncomenda':
              $page_file = "./admin/StatusEncomendas/editStatusEncomenda.php";
              break;
            case 'deleteStatusEncomenda':
              $page_file = "./admin/StatusEncomendas/deleteStatusEncomenda.php";
              break;
              
            // Encomendas
            case 'viewEncomenda':
              $page_file = "./admin/Encomendas/viewEncomenda.php";
              break;
            case 'createEncomenda':
              $page_file = "./admin/Encomendas/createEncomenda.php";
              break;
            case 'ListEncomenda':
              $page_file = "./admin/Encomendas/ListEncomenda.php";
              break;
            
            // Utilizador
            case 'viewUtilizador':
              $page_file = "./admin/Utilizador/viewUtilizador.php";
              break;
            case 'ListUtilizador':
              $page_file = "./admin/Utilizador/listUtilizador.php";
              break;
            case 'createUtilizador':
              $page_file = "./admin/Utilizador/createUtilizador.php";
              break;
            case 'editUtilizador':
              $page_file = "./admin/Utilizador/editUtilizador.php";
              break;
            case 'deleteUtilizador':
              $page_file = "./admin/Utilizador/deleteUtilizador.php";
              break;

            // Produtos
            case 'viewProduto':
              $page_file = "./admin/Produtos/viewProduto.php";
              break;
            case 'ListProduto':
              $page_file = "./admin/Produtos/listProduto.php";
              break;
            case 'createProduto':
              $page_file = "./admin/Produtos/createProduto.php";
              break;
            case 'editProduto':
              $page_file = "./admin/Produtos/editProduto.php";
              break;
            case 'deleteProduto':
              $page_file = "./admin/Produtos/deleteProduto.php";
              break;

            case 'logout':
              $page_file = "./bd/logout.php";
              break;
              
            default:
              $page_file = "./admin/404.php";
              break;
          }
          
          if (!empty($page_file) && file_exists($page_file)) {
            include($page_file);
          } else {
            include("./admin/404.html");
          }
          ?>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script>
    // Ativar menu ativo
    $(document).ready(function() {
      $('.sidebar-item').removeClass('active');
      $('.sidebar-link.active').parent().addClass('active');
    });
  </script>
</body>

</html>