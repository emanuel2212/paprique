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
                            <!-- Search Form -->
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
                                    <div class="navbar-collapse">
                                        <div class="nav-inner">
                                            <ul class="nav main-menu menu navbar-nav">
                                                <!-- Menu Marcas -->
                                                <li class="dropdown">
                                                    <a href="?page=marcas">Marcas</a>
                                                    <ul class="dropdown-menu">
                                                        <?php
                                                        $marcas = $produtos->listMarcas();
                                                        if (!empty($marcas)) {
                                                            echo '<div class="dropdown-marcas-container">';
                                                            $count = 0;
                                                            foreach ($marcas as $marca) {
                                                                if ($count % 5 == 0 && $count != 0) {
                                                                    echo '</div><div class="dropdown-marcas-container">';
                                                                }
                                                                echo '<li><a href="?page=marcas&marca=' . urlencode($marca['nome_marca']) . '">' . htmlspecialchars($marca['nome_marca']) . '</a></li>';
                                                                $count++;
                                                            }
                                                            echo '</div>';
                                                        } else {
                                                            echo '<li><a href="#">Nenhuma marca disponível</a></li>';
                                                        }
                                                        ?>
                                                    </ul>
                                                </li>

                                                <!-- Menu Skate -->
                                                <li class="dropdown">
                                                    <a href="?page=skates">Skates</a>
                                                    <ul class="dropdown-menu">
                                                        <?php
                                                        $subcategoriasSkate = $produtos->listSubcategoriasByCategoria(18);
                                                        if (!empty($subcategoriasSkate)) {
                                                            foreach ($subcategoriasSkate as $sub) {
                                                                $produtosCount = $produtos->countProdutosBySubcategoria($sub['id_subcategoria']);
                                                                if ($produtosCount > 0) {
                                                                    echo '<li><a href="?page=skates&subcategoria=' . urlencode($sub['nome_subcategoria']) . '">' . htmlspecialchars($sub['nome_subcategoria']) . '</a></li>';
                                                                } else {
                                                                    echo '<li><a href="#" class="no-products">' . htmlspecialchars($sub['nome_subcategoria']) . ' <span>(sem produtos)</span></a></li>';
                                                                }
                                                            }
                                                        } else {
                                                            echo '<li><a href="#">Nenhuma subcategoria disponível</a></li>';
                                                        }
                                                        ?>
                                                    </ul>
                                                </li>

                                                <!-- Menu Proteção -->
                                                <li class="dropdown">
                                                    <a href="?page=protecoes">Proteções</a>
                                                    <ul class="dropdown-menu">
                                                        <?php
                                                        $subcategoriasProtecao = $produtos->listSubcategoriasByCategoria(19);
                                                        if (!empty($subcategoriasProtecao)) {
                                                            foreach ($subcategoriasProtecao as $sub) {
                                                                $produtosCount = $produtos->countProdutosBySubcategoria($sub['id_subcategoria']);
                                                                if ($produtosCount > 0) {
                                                                    echo '<li><a href="?page=protecoes&subcategoria=' . urlencode($sub['nome_subcategoria']) . '">' . htmlspecialchars($sub['nome_subcategoria']) . '</a></li>';
                                                                } else {
                                                                    echo '<li><a href="#" class="no-products">' . htmlspecialchars($sub['nome_subcategoria']) . ' <span>(sem produtos)</span></a></li>';
                                                                }
                                                            }
                                                        } else {
                                                            echo '<li><a href="#">Nenhuma subcategoria disponível</a></li>';
                                                        }
                                                        ?>
                                                    </ul>
                                                </li>


                                            </ul>
                                        </div>
                                    </div>
                                </nav>
                                <!--/ End Main Menu -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!--/ End Header -->

    <?php if ($page === 'login' || $page === 'register' || $page === 'ListProduto' || $page === 'carrinho' || $page === 'profile' || $page === 'search' || $page === 'checkout'): ?>
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
                        }

                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Mostra os produtos em destaque -->
        <section class="featured-products section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h2>Produtos em Destaque</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php
                    $produtos = new Produtos();
                    $destaques = $produtos->list();

                    if (!empty($destaques)) {
                        foreach ($destaques as $produto) {
                            $preco_formatado = number_format($produto['preco'], 2, ',', '.');
                            $imagem = !empty($produto['link_imagem']) ? "images/{$produto['link_imagem']}" : "https://via.placeholder.com/300x300";

                            echo '
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="single-product">
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