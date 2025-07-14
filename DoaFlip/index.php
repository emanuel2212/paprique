<?php
// Inicie a sessão apenas se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
require_once "pages/Connection.php";
require_once "Produtos.php";


$produtos = new Produtos();

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Processa adição ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_carrinho']) && isset($_SESSION['user'])) {
    $id_produto = intval($_POST['id_produto']);
    $quantidade = intval($_POST['quantidade']);

    if ($quantidade > 0) {
        if (isset($_SESSION['carrinho'][$id_produto])) {
            $_SESSION['carrinho'][$id_produto]['quantidade'] += $quantidade;
        } else {
            $_SESSION['carrinho'][$id_produto] = [
                'quantidade' => $quantidade,
                'adicionado_em' => time() // Adiciona timestamp para ordenação
            ];
        }

        $_SESSION['mensagem'] = "Produto adicionado ao carrinho!";
        header("Location: ?page=carrinho");
        exit();
    }
}
// Verifica se há mensagem de logout
if (isset($_SESSION['logout_message'])) {
    $logout_message = $_SESSION['logout_message'];
    unset($_SESSION['logout_message']); // Remove a mensagem após exibir
}

// Verifica se o usuário está logado
$user_logged_in = isset($_SESSION['user']);
$username = $user_logged_in ? $_SESSION['user']['username'] : '';

$page = '';
if (isset($_GET["page"])) {
    $page = $_GET["page"];
}

$subcategoria = isset($_GET['subcategoria']) ? $_GET['subcategoria'] : '';
$marca = isset($_GET['marca']) ? $_GET['marca'] : '';

// Paginação
$current_page = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$products_per_page = 10;

?>

<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <!-- Meta Tag -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name='copyright' content=''>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title Tag  -->
    <title>Do a Flip Skateshop.</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="images/favicon.png">
    <!-- Web Font -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- StyleSheet -->
    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="css/magnific-popup.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="css/font-awesome.css">
    <!-- Fancybox -->
    <link rel="stylesheet" href="css/jquery.fancybox.min.css">
    <!-- Themify Icons -->
    <link rel="stylesheet" href="css/themify-icons.css">
    <!-- Nice Select CSS -->
    <link rel="stylesheet" href="css/niceselect.css">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- Flex Slider CSS -->
    <link rel="stylesheet" href="css/flex-slider.min.css">
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="css/owl-carousel.css">
    <!-- Slicknav -->
    <link rel="stylesheet" href="css/slicknav.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@iconscout/unicons@3.0.6/css/line.css">
    <!-- Eshop StyleSheet -->
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/color/color5.css">
    <link rel="stylesheet" href="#" id="colors">
    
</head>
<style>
    /* Estilos para o menu de navegação */
    .navbar-nav .dropdown-menu {
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(255, 255, 255, 1);
        border: none;
        min-width: 300px;
    }

    .dropdown-marcas-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
    }

    .navbar-nav .dropdown-menu li a {
        padding: 8px 12px;
        border-radius: 4px;
        color: #333;
        transition: all 0.3s ease;
        display: block;
    }

    .navbar-nav .dropdown-menu li a:hover {
        background-color: #f8f9fa;
        color: #0d6efd;
        transform: translateX(3px);
    }

    .navbar-nav .dropdown-menu h5 {
        font-size: 1rem;
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
    }

    .no-products {
        opacity: 0.6;
    }

    .no-products span {
        font-size: 0.8em;
        color: #777;
    }

    .dropdown:hover .dropdown-menu {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Novos estilos para promoções */
    .promo-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #ff5722;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
        z-index: 1;
    }

    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(252, 251, 251, 0.1);
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 30px;
    }

    .pagination a {
        color: #333;
        padding: 8px 16px;
        text-decoration: none;
        border: 1px solid #ddd;
        margin: 0 4px;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .pagination a.active {
        background-color: #0d6efd;
        color: white;
        border: 1px solid #0d6efd;
    }

    .pagination a:hover:not(.active) {
        background-color: #ddd;
    }

    .section-title {
        position: relative;
        margin-bottom: 30px;
    }

    .section-title h2 {
        display: inline-block;
        padding-bottom: 10px;
        position: relative;
    }

    .section-title h2:after {
        content: '';
        position: absolute;
        width: 50%;
        height: 3px;
        bottom: 0;
        left: 0;
        background-color: #0d6efd;
    }

    .menu-area {
        width: 100%;
    }

    .navbar-nav {
        display: flex;
        flex-direction: row;
        gap: 15px;
        align-items: center;
    }

    .nav-item {
        position: relative;
        list-style: none;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        color: #fbfcfdff;
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        display: none;
        min-width: 250px;
        padding: 10px 0;
        margin: 2px 0 0;
        font-size: 14px;
        text-align: left;
        background-color: #fff;
        border: 1px solid rgba(248, 244, 244, 0.92);
        border-radius: 4px;
        box-shadow: 0 6px 12px rgba(249, 245, 245, 0.99);
    }

    .dropdown:hover .dropdown-menu {
        display: block;
    }
</style>

<body class="js">
    <!-- Header -->
    <header class="header shop">
        <!-- Topbar -->
        <div class="topbar">
            <div class="container">
                <?php if (!empty($logout_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($logout_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-lg-4 col-md-12 col-12">
                        <!-- Top Left -->
                        <div class="top-left">
                            <ul class="list-main">
                                <?php if ($user_logged_in): ?>
                                    <a href="">Olá, <?php echo htmlspecialchars($username); ?>! Seja Bem vindo(a)!!</a>
                                <?php else: ?>
                                    <a href=""></a>Olá, Seja Bem vindo(a)!!
                                <?php endif; ?>
                            </ul>
                        </div>
                        <!--/ End Top Left -->
                    </div>
                    <div class="col-lg-8 col-md-12 col-12">
                        <!-- Top Right -->
                        <div class="right-content">
                            <ul class="list-main">
                                <?php if ($user_logged_in): ?>
                                    <li><i class="ti-power-off"></i><a href="./dashboard/bd/logout.php">Logout</a></li>
                                <?php else: ?>
                                    <li><i class="ti-user"></i> <a href="?page=register">Registar</a></li>
                                    <li><i class="ti-power-off"></i><a href="?page=login">Login</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <!-- End Top Right -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Topbar -->
        <div class="middle-inner">
            <div class="container">
                <div class="row">
                    <div class="col-lg-2 col-md-2 col-12">
                        <!-- Logo -->
                        <div class="logo">
                            <a href="index.php"><img src="images/logo2.png" alt="logo"></a>
                        </div>
                        <!--/ End Logo -->
                    </div>
                    <div class="col-lg-8 col-md-7 col-12">
                        <div class="search-bar-top">
                            <div class="search-bar">
                                <form>
                                    <input name="search" placeholder="Procure produtos aqui. . ." type="search" autocomplete="off">
                                    <button class="btnn"><i class="ti-search"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-12">
                        <div class="right-bar">
                            <div class="sinlge-bar">
                                <a href="#" class="single-icon"><i class="fa fa-heart-o" aria-hidden="true"></i></a>
                            </div>
                            <div class="sinlge-bar">
                                <a href="?page=profile" class="single-icon"><i class="fa fa-user-circle-o" aria-hidden="true"></i></a>
                            </div>
                            <div class="sinlge-bar shopping">
                                <a href="?page=carrinho" class="single-icon">
                                    <i class="ti-bag"></i>
                                    <?php if (!empty($_SESSION['carrinho']) && count($_SESSION['carrinho']) > 0): ?>
                                        <span class="total-count"><?php echo array_sum(array_column($_SESSION['carrinho'], 'quantidade')); ?></span>
                                    <?php endif; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <!-- Header Inner -->
        <div class="header-inner">
            <div class="container">
                <div class="cat-nav-head">
                    <div class="row">
                        <div class="col-lg-12 col-12">
                            <div class="menu-area">
                                <!-- Main Menu -->
                                <nav class="navbar navbar-expand-lg">
                                    <ul class="navbar-nav" style="display: flex; flex-direction: row; gap: 15px;">
                                        <!-- Menu Marcas -->
                                        <li class="dropdown nav-item">
                                            <a href="?page=produtos&filtro=categoria&valor=Marcas" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                                                <i class="fas fa-tags me-1"></i> Marcas
                                            </a>
                                            <ul class="dropdown-menu">
                                                <h5><i class="fas fa-tags me-2"></i> Nossas Marcas</h5>
                                                <?php
                                                $marcas = $produtos->listMarcas();
                                                if (!empty($marcas)) {
                                                    echo '<div class="dropdown-marcas-container">';
                                                    foreach ($marcas as $marca) {
                                                        echo '<li>
                                                    <a href="?page=produtos&filtro=marca&valor=' . urlencode($marca['nome_marca']) . '">
                                                        <i class="fas fa-caret-right me-2"></i> ' . htmlspecialchars($marca['nome_marca']) . '
                                                    </a>
                                                </li>';
                                                    }
                                                    echo '</div>';
                                                } else {
                                                    echo '<li><a href="#"><i class="fas fa-info-circle me-2"></i> Nenhuma marca disponível</a></li>';
                                                }
                                                ?>
                                            </ul>
                                        </li>

                                        <!-- Menu Skate -->
                                        <li class="dropdown nav-item">
                                            <a href="?page=produtos&filtro=categoria&valor=Skate" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                                                <i class="fas fa-skating me-1"></i> Skates
                                            </a>
                                            <ul class="dropdown-menu">
                                                <h5><i class="fas fa-list-ul me-2"></i> Categorias de Skate</h5>
                                                <?php
                                                $subcategoriasSkate = $produtos->listSubcategoriasByCategoria(18);
                                                if (!empty($subcategoriasSkate)) {
                                                    foreach ($subcategoriasSkate as $sub) {
                                                        $produtosCount = $produtos->countProdutosBySubcategoria($sub['id_subcategoria']);
                                                        if ($produtosCount > 0) {
                                                            echo '<li>
                                                        <a href="?page=produtos&filtro=subcategoria&valor=' . urlencode($sub['nome_subcategoria']) . '">
                                                            <i class="fas fa-chevron-right me-2"></i> ' . htmlspecialchars($sub['nome_subcategoria']) . '
                                                        </a>
                                                    </li>';
                                                        } else {
                                                            echo '<li>
                                                        <a href="#" class="no-products">
                                                            <i class="far fa-circle me-2"></i> ' . htmlspecialchars($sub['nome_subcategoria']) . ' <span>(em breve)</span>
                                                        </a>
                                                    </li>';
                                                        }
                                                    }
                                                } else {
                                                    echo '<li><a href="#"><i class="fas fa-info-circle me-2"></i> Nenhuma subcategoria disponível</a></li>';
                                                }
                                                ?>
                                            </ul>
                                        </li>

                                        <!-- Menu Proteção -->
                                        <li class="dropdown nav-item">
                                            <a href="?page=produtos&filtro=categoria&valor=Proteções" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                                                <i class="fas fa-shield-alt me-1"></i> Proteções
                                            </a>
                                            <ul class="dropdown-menu">
                                                <h5><i class="fas fa-list-ul me-2"></i> Tipos de Proteção</h5>
                                                <?php
                                                $subcategoriasProtecao = $produtos->listSubcategoriasByCategoria(19);
                                                if (!empty($subcategoriasProtecao)) {
                                                    foreach ($subcategoriasProtecao as $sub) {
                                                        $produtosCount = $produtos->countProdutosBySubcategoria($sub['id_subcategoria']);
                                                        if ($produtosCount > 0) {
                                                            echo '<li>
                                                        <a href="?page=produtos&filtro=subcategoria&valor=' . urlencode($sub['nome_subcategoria']) . '">
                                                            <i class="fas fa-chevron-right me-2"></i> ' . htmlspecialchars($sub['nome_subcategoria']) . '
                                                        </a>
                                                    </li>';
                                                        } else {
                                                            echo '<li>
                                                        <a href="#" class="no-products">
                                                            <i class="far fa-circle me-2"></i> ' . htmlspecialchars($sub['nome_subcategoria']) . ' <span>(em breve)</span>
                                                        </a>
                                                    </li>';
                                                        }
                                                    }
                                                } else {
                                                    echo '<li><a href="#"><i class="fas fa-info-circle me-2"></i> Nenhuma subcategoria disponível</a></li>';
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                                <!--/ End Main Menu -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ End Header Inner -->

        <?php if ($page === 'login' || $page === 'register' || $page === 'ListProduto' || $page === 'carrinho' || $page === 'profile' || $page === 'search' || $page === 'checkout' || $page === 'produtos'): ?>
            <!-- Mostra apenas o formulário correspondente -->
            <div class="product-area section">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <?php
                            if ($page === 'login' && file_exists("./pages/login.php")) {
                                include("./pages/login.php");
                            } elseif ($page === 'register' && file_exists("./pages/register.php")) {
                                include("./pages/register.php");
                            } elseif ($page === 'ListProduto' && file_exists("./pages/ListProduto.php")) {
                                include("./pages/ListProduto.php");
                            } elseif ($page === 'carrinho' && file_exists("./pages/carrinho.php")) {
                                include("./pages/carrinho.php");
                            } elseif ($page === 'profile' && file_exists("./pages/profile.php")) {
                                include("./pages/profile.php");
                            } elseif ($page === 'search' && file_exists("./pages/search_results.php")) {
                                include("./pages/search_results.php");
                            } elseif ($page === 'checkout' && file_exists("./pages/checkout.php")) {
                                include("./pages/checkout.php");
                            } elseif ($page === 'produtos' && file_exists("./pages/produtos.php")) {
                                include("./pages/produtos.php");
                            }

                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Mostra os produtos em destaque e promoções -->
            <section class="featured-products section">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="section-title">
                                <h2><i class="fas fa-star"></i> Promoções Especiais</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        $produtos = new Produtos();
                        $todosProdutos = $produtos->list();

                        // Filtra produtos com preço menor que 50
                        $promocoes = array_filter($todosProdutos, function ($produto) {
                            return $produto['preco'] < 50;
                        });

                        // Limita a 4 produtos
                        $promocoes = array_slice($promocoes, 0, 4);

                        if (!empty($promocoes)) {
                            foreach ($promocoes as $produto) {
                                $preco_formatado = number_format($produto['preco'], 2, ',', '.');
                                $imagem = !empty($produto['link_imagem']) ? "images/{$produto['link_imagem']}" : "https://via.placeholder.com/300x300";

                                echo '
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="single-product product-card">
                                    <div class="product-image">
                                        <span class="promo-badge">PROMOÇÃO</span>
                                        <img src="' . $imagem . '" alt="" style="height: 200px; object-fit: cover;">
                                        <div class="button">
                                            <a href="index.php?page=ListProduto&id=' . $produto['id_produto'] . '" class="btn"><i class="ti-eye"></i> Ver Detalhes</a>                                
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <span class="category">' . htmlspecialchars($produto['marca'] ?? 'Sem marca') . '</span>
                                        <h4 class="title">
                                            <a href="?page=ListProduto&id=' . $produto['id_produto'] . '">' . htmlspecialchars($produto['nome_produto']) . '</a>
                                        </h4>
                                        <div class="price">
                                            <span class="text-danger"><strong>€' . $preco_formatado . '</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                            }
                        } else {
                            echo '<div class="col-12 text-center"><p>Nenhuma promoção disponível no momento.</p></div>';
                        }
                        ?>
                    </div>
                </div>
            </section>

            <!-- Mostra os produtos normais com paginação -->
            <section class="featured-products section" style="padding-top: 0;">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="section-title">
                                <h2><i class="fas fa-bolt"></i> Nossos Produtos</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        // Filtra produtos que não estão em promoção
                        $produtosNormais = array_filter($todosProdutos, function ($produto) {
                            return $produto['preco'] >= 0;
                        });

                        // Paginação
                        $total_produtos = count($produtosNormais);
                        $total_paginas = ceil($total_produtos / $products_per_page);
                        $offset = ($current_page - 1) * $products_per_page;
                        $produtos_pagina = array_slice($produtosNormais, $offset, $products_per_page);

                        if (!empty($produtos_pagina)) {
                            foreach ($produtos_pagina as $produto) {
                                $preco_formatado = number_format($produto['preco'], 2, ',', '.');
                                $imagem = !empty($produto['link_imagem']) ? "images/{$produto['link_imagem']}" : "https://via.placeholder.com/300x300";

                                echo '
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="single-product product-card">
                                    <div class="product-image">
                                        <img src="' . $imagem . '" alt="" style="height: 200px; object-fit: cover;">
                                        <div class="button">
                                            <a href="index.php?page=ListProduto&id=' . $produto['id_produto'] . '" class="btn"><i class="ti-eye"></i> Ver Detalhes</a>                                
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <span class="category">' . htmlspecialchars($produto['marca'] ?? 'Sem marca') . '</span>
                                        <h4 class="title">
                                            <a href="?page=ListProduto&id=' . $produto['id_produto'] . '">' . htmlspecialchars($produto['nome_produto']) . '</a>
                                        </h4>
                                        <div class="price">
                                            <span>€' . $preco_formatado . '</span>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                            }
                        } else {
                            echo '<div class="col-12 text-center"><p>Nenhum produto encontrado.</p></div>';
                        }
                        ?>
                    </div>

                    <!-- Paginação -->
                    <?php if ($total_paginas > 1): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="pagination">
                                    <?php if ($current_page > 1): ?>
                                        <a href="?pagina=<?php echo $current_page - 1; ?>">&laquo; Anterior</a>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                        <a href="?pagina=<?php echo $i; ?>" <?php echo ($i == $current_page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                                    <?php endfor; ?>

                                    <?php if ($current_page < $total_paginas): ?>
                                        <a href="?pagina=<?php echo $current_page + 1; ?>">Próxima &raquo;</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Start Footer Area -->
        <footer class="footer">
            <!-- Footer Top -->
            <div class="footer-top section">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-5 col-md-6 col-12">
                            <!-- Single Widget -->
                            <div class="single-footer about">
                                <div class="logo">
                                    <a href="index.php"><img src="images/logo2.png" alt=""></a>
                                </div>
                            </div>
                            <!-- End Single Widget -->
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <!-- Single Widget -->
                            <div class="single-footer links">
                                <h4>Informações</h4>
                                <ul>
                                    <li><a href="about-us.html">Sobre nós</a></li>
                                    <li><a href="#">Termos e Condições</a></li>
                                    <li><a href="contact.html">Contate-nos</a></li>
                                    <li><a href="#">Ajuda</a></li>
                                </ul>
                            </div>
                            <!-- End Single Widget -->
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <!-- Single Widget -->
                            <div class="single-footer links">
                                <h4>Atendimento ao Cliente</h4>
                                <ul>
                                    <li><a href="#">Métodos de Pagamento</a></li>
                                    <li><a href="#">Dinheiro de volta</a></li>
                                    <li><a href="#">Devoluções</a></li>
                                    <li><a href="#">Envio</a></li>
                                    <li><a href="#">Política de Privacidade</a></li>
                                </ul>
                            </div>
                            <!-- End Single Widget -->
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <!-- Single Widget -->
                            <div class="single-footer social">
                                <h4>Entre em contacto</h4>
                                <!-- Single Widget -->
                                <div class="contact">
                                    <ul>
                                        <li>Rua das Flores, nº 58 - 2º Esq.</li>
                                        <li>1200-195 Lisboa, Portugal</li>
                                        <li>info@eshop.com</li>
                                        <li>+351 912 345 678</li>
                                    </ul>
                                </div>
                                <!-- End Single Widget -->
                                <ul>
                                    <li><a href="#"><i class="ti-facebook"></i></a></li>
                                    <li><a href="#"><i class="ti-twitter"></i></a></li>
                                    <li><a href="#"><i class="ti-flickr"></i></a></li>
                                    <li><a href="#"><i class="ti-instagram"></i></a></li>
                                </ul>
                            </div>
                            <!-- End Single Widget -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Footer Top -->
            <div class="copyright">
                <div class="container">
                    <div class="inner">
                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="left">
                                    <p>Copyright © 2025 <a href="http://www.wpthemesgrid.com" target="_blank">Wpthemesgrid</a> - All Rights Reserved.</p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="right">
                                    <img src="images/payments.png" alt="#">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- /End Footer Area -->

        <!-- Jquery -->
        <script src="js/jquery.min.js"></script>
        <script src="js/jquery-migrate-3.0.0.js"></script>
        <script src="js/jquery-ui.min.js"></script>
        <!-- Popper JS -->
        <script src="js/popper.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="js/bootstrap.min.js"></script>
        <!-- Color JS -->
        <script src="js/colors.js"></script>
        <!-- Slicknav JS -->
        <script src="js/slicknav.min.js"></script>
        <!-- Owl Carousel JS -->
        <script src="js/owl-carousel.js"></script>
        <!-- Magnific Popup JS -->
        <script src="js/magnific-popup.js"></script>
        <!-- Fancybox JS -->
        <script src="js/facnybox.min.js"></script>
        <!-- Waypoints JS -->
        <script src="js/waypoints.min.js"></script>
        <!-- Countdown JS -->
        <script src="js/finalcountdown.min.js"></script>
        <!-- Nice Select JS -->
        <script src="js/nicesellect.js"></script>
        <!-- Ytplayer JS -->
        <script src="js/ytplayer.min.js"></script>
        <!-- Flex Slider JS -->
        <script src="js/flex-slider.js"></script>
        <!-- ScrollUp JS -->
        <script src="js/scrollup.js"></script>
        <!-- Onepage Nav JS -->
        <script src="js/onepage-nav.min.js"></script>
        <!-- Easing JS -->
        <script src="js/easing.js"></script>
        <!-- Active JS -->
        <script src="js/active.js"></script>
        <script>
            $(document).ready(function() {
                // Suaviza a transição entre páginas
                $('body').css('opacity', '0').animate({
                    opacity: '1'
                }, 300);

                // Intercepta cliques nos links de login/register
                $('a[href*="page=login"], a[href*="page=register"]').click(function(e) {
                    e.preventDefault();
                    $('body').animate({
                        opacity: '0'
                    }, 300, function() {
                        window.location = $(e.target).attr('href');
                    });
                });
            });

            $(document).ready(function() {
                // Elementos da interface
                const searchInput = $('input[name="search"]');
                const searchResultsContainer = $('<div class="search-results-container"></div>').insertAfter(searchInput.parent());
                const searchForm = $('form').has(searchInput);

                // Prevenir envio padrão do formulário
                searchForm.on('submit', function(e) {
                    e.preventDefault();
                    performSearch(searchInput.val());
                });

                // Evento de digitação
                searchInput.on('input', function() {
                    const query = $(this).val().trim();

                    if (query.length >= 2) {
                        fetchSearchSuggestions(query);
                    } else {
                        searchResultsContainer.empty();
                    }
                });

                // Fechar resultados ao clicar fora
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.search-results-container, .search-bar').length) {
                        searchResultsContainer.empty();
                    }
                });

                // Buscar sugestões via AJAX
                function fetchSearchSuggestions(query) {
                    $.ajax({
                        url: 'pages/search.php',
                        method: 'GET',
                        data: {
                            query: query,
                            action: 'suggestions'
                        },
                        dataType: 'json',
                        success: function(response) {
                            displaySearchResults(response, query);
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro ao buscar sugestões:', error);
                        }
                    });
                }

                // Buscar produtos completos
                function performSearch(query) {
                    if (query.length < 2) return;

                    $.ajax({
                        url: 'pages/search.php',
                        method: 'GET',
                        data: {
                            query: query,
                            action: 'products'
                        },
                        success: function(response) {
                            // Redirecionar para página de resultados ou mostrar em modal
                            window.location.href = '?page=search&q=' + encodeURIComponent(query);
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro ao buscar produtos:', error);
                        }
                    });
                }

                // Exibir resultados na interface
                function displaySearchResults(data, query) {
                    searchResultsContainer.empty();

                    if (data.suggestions && data.suggestions.length > 0) {
                        const suggestionsSection = $('<div class="search-section"><h3>SUGESTÕES</h3><ul class="suggestions-list"></ul></div>');

                        data.suggestions.forEach(suggestion => {
                            suggestionsSection.find('ul').append(
                                `<li><a href="?page=search&q=${encodeURIComponent(suggestion)}">${highlightMatch(suggestion, query)}</a></li>`
                            );
                        });

                        searchResultsContainer.append(suggestionsSection);
                    }

                    if (data.products && data.products.length > 0) {
                        const productsSection = $('<div class="search-section"><h3>PRODUTOS</h3><div class="search-products-grid"></div></div>');

                        data.products.slice(0, 4).forEach(product => {
                            const price = parseFloat(product.preco).toFixed(2).replace('.', ',');
                            productsSection.find('.search-products-grid').append(`
                    <div class="search-product-card">
                        <a href="?page=ListProduto&id=${product.id_produto}">
                            <img src="${product.link_imagem || 'https://via.placeholder.com/100'}" alt="${product.nome_produto}">
                            <h4>${highlightMatch(product.nome_produto, query)}</h4>
                            <span class="price">€${price}</span>
                        </a>
                    </div>
                `);
                        });

                        if (data.products.length > 4) {
                            productsSection.append(
                                `<div class="view-all"><a href="?page=search&q=${encodeURIComponent(query)}">Ver todos os ${data.products.length} produtos</a></div>`
                            );
                        }

                        searchResultsContainer.append(productsSection);
                    }
                }

                // Destacar correspondências no texto
                function highlightMatch(text, query) {
                    if (!query) return text;
                    const regex = new RegExp(query, 'gi');
                    return text.replace(regex, match => `<span class="highlight">${match}</span>`);
                }
            });
        </script>
</body>

</html>