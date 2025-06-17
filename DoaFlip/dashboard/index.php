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
  <title> Template do administrador </title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
</head>

<body>


  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="../index.html" class="text-nowrap logo-img">
            <img src="assets/images/logos/logo.svg" alt="" />
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-6"></i>
          </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
              <span class="hide-menu">Home</span>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)" aria-expanded="false">
                <div class="d-flex align-items-center gap-3">
                  <span class="d-flex">
                    <i class="ti ti-layout-grid"></i>
                  </span>
                  <span class="hide-menu">Páginas</span>
                </div>

              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <a class="sidebar-link justify-content-between"
                    href="?page=viewCategoria">
                    <div class="d-flex align-items-center gap-3">
                      <div class="round-16 d-flex align-items-center justify-content-center">
                        <i class="ti ti-circle"></i>
                      </div>
                      <span class="hide-menu">Categorias</span>
                    </div>
                  </a>
                </li>

                <li class="sidebar-item">
                  <a class="sidebar-link justify-content-between"
                    href="?page=viewSubCategoria">
                    <div class="d-flex align-items-center gap-3">
                      <div class="round-16 d-flex align-items-center justify-content-center">
                        <i class="ti ti-circle"></i>
                      </div>
                      <span class="hide-menu">Sub-Categorias</span>
                    </div>
                  </a>
                </li>

            </li>
            <li class="sidebar-item">
              <a class="sidebar-link justify-content-between"
                href="?page=viewImagem">
                <div class="d-flex align-items-center gap-3">
                  <div class="round-16 d-flex align-items-center justify-content-center">
                    <i class="ti ti-circle"></i>
                  </div>
                  <span class="hide-menu">Imagem</span>
                </div>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link justify-content-between"
                href="?page=viewStatusEncomenda">
                <div class="d-flex align-items-center gap-3">
                  <div class="round-16 d-flex align-items-center justify-content-center">
                    <i class="ti ti-circle"></i>
                  </div>
                  <span class="hide-menu">Status de Encomenda</span>
                </div>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link justify-content-between"
                href="?page=viewEncomenda">
                <div class="d-flex align-items-center gap-3">
                  <div class="round-16 d-flex align-items-center justify-content-center">
                    <i class="ti ti-circle"></i>
                  </div>
                  <span class="hide-menu">Encomendas</span>
                </div>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link justify-content-between"
                href="?page=viewUtilizador">
                <div class="d-flex align-items-center gap-3">
                  <div class="round-16 d-flex align-items-center justify-content-center">
                    <i class="ti ti-circle"></i>
                  </div>
                  <span class="hide-menu">Utilizador</span>
                </div>

              </a>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link justify-content-between"
                href="#">
                <div class="d-flex align-items-center gap-3">
                  <div class="round-16 d-flex align-items-center justify-content-center">
                    <i class="ti ti-circle"></i>
                  </div>
                  <span class="hide-menu">Pricing</span>
                </div>

              </a>
            </li>
          </ul>
          </li>
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
            <?php if (isset($_SESSION['user'])): ?>
              <a href="./bd/logout.php" class="btn btn-outline-danger">Logout</a>
            <?php endif; ?>
          </div>
        </nav>
      </header>
      <!--  Header End -->
      <div class="body-wrapper-inner">
        <div class="container-fluid">
          <?php
          switch ($page) {

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
              
            case 'viewEncomenda':
              $page_file = "./admin/Encomenda/viewEncomenda.php";
              break;
            case 'createEncomenda':
              $page_file = "./admin/Encomenda/createEncomenda.php";
              break;
            case 'editEncomenda':
              $page_file = "./admin/Encomenda/editEncomenda.php";
              break;
            case 'deleteEncomenda':
              $page_file = "./admin/Encomenda/deleteEncomenda.php";
              break;
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

            default:
              $page_file = "./admin/404.php";
              break;
          }
          if (!empty($page_file) && file_exists($page_file)) {
            include($page_file);
          } else {
            include("../dashboard/admin/404.html");
          }

          ?>

        </div>
      </div>
    </div>
  </div>
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>
  <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="./assets/js/dashboard.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>