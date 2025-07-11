<?php
// pages/ListProduto.php

require_once "Connection.php";



if (isset($_GET['subcategoria'])) {
    $subcategoria = $_GET['subcategoria'];
    $produtos = new Produtos();

    // Verifica se existem produtos nesta subcategoria
    $id_subcategoria = $produtos->getIdSubcategoriaByName($subcategoria);
    if ($id_subcategoria && !$produtos->checkProdutosBySubcategoria($id_subcategoria)) {
        echo '<div class="alert alert-warning">Nenhum produto disponível na subcategoria "' . htmlspecialchars($subcategoria) . '".</div>';
    }
}

class ListProduto
{
    private $conn;

    public function __construct()
    {
        $connection = new Connection();
        $this->conn = $connection->connect();
    }

    public function getProdutoById($id)
    {
        try {
            $query = "SELECT p.*, m.nome_marca as marca, 
                     c.nome_categoria as categoria, 
                     sc.nome_subcategoria as subcategoria
                     FROM produtos p
                     LEFT JOIN marca m ON p.id_marca = m.id_marca
                     LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                     LEFT JOIN subcategorias sc ON p.id_subcategoria = sc.id_subcategoria
                     WHERE p.id_produto = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $produto = $stmt->fetch();

            if ($produto) {
                $query_imagens = "SELECT link_imagem FROM imagens WHERE id_produto = :id";
                $stmt_imagens = $this->conn->prepare($query_imagens);
                $stmt_imagens->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt_imagens->execute();

                $produto['imagens'] = $stmt_imagens->fetchAll();
            }

            return $produto;
        } catch (PDOException $e) {
            die("Erro ao buscar produto: " . $e->getMessage());
        }
    }
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $produto_id = intval($_GET['id']);
    $listProduto = new ListProduto();
    $produto = $listProduto->getProdutoById($produto_id);

    if (!$produto) {
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>

<!-- Adicione isso no head do seu template ou antes do conteúdo -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Breadcrumbs corrigido -->
<div class="py-3 bg-light mb-4">
    <div class="container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../index.php"><i class="fas fa-home fa-fw"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="?page=<?php echo strtolower($produto['categoria']); ?>"><i class="fas fa-tag fa-fw"></i> <?php echo htmlspecialchars($produto['categoria']); ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-info-circle fa-fw"></i> <?php echo htmlspecialchars($produto['nome_produto']); ?></li>
            </ol>
        </nav>
    </div>
</div>

<!-- Product Details - Versão simplificada e funcional -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Product Images -->
            <div class="col-lg-6">
                <div class="bg-white p-3 shadow-sm h-100">
                    <div class="mb-3" style="height: 400px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                        <img id="main-product-image" src="./images/<?php echo htmlspecialchars($produto['imagens'][0]['link_imagem'] ?? 'default-product.jpg'); ?>"
                            alt="<?php echo htmlspecialchars($produto['nome_produto']); ?>"
                            class="img-fluid" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                    </div>

                    <?php if (!empty($produto['imagens']) && count($produto['imagens']) > 1): ?>
                        <div class="row g-2">
                            <?php foreach ($produto['imagens'] as $index => $imagem): ?>
                                <div class="col-3">
                                    <div style="cursor: pointer; border: <?php echo $index === 0 ? '2px solid #0d6efd' : '1px solid #dee2e6' ?>; padding: 2px; border-radius: 5px;">
                                        <img src="./images/<?php echo htmlspecialchars($imagem['link_imagem']); ?>"
                                            alt="<?php echo htmlspecialchars($produto['nome_produto']); ?>"
                                            class="img-fluid w-100" style="aspect-ratio: 1/1; object-fit: cover;">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Info - Versão ajustada -->
            <div class="col-lg-6">
                <div class="bg-white p-4 shadow-sm h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h1 class="h2 mb-0 fw-bold"><?php echo htmlspecialchars($produto['nome_produto']); ?></h1>
                        <button type="button" class="btn btn-outline-danger btn-sm">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-tag text-muted me-1"></i> <?php echo htmlspecialchars($produto['categoria']); ?>
                        </span>
                        <?php if (!empty($produto['subcategoria'])): ?>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-layer-group text-muted me-1"></i> <?php echo htmlspecialchars($produto['subcategoria']); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($produto['marca'])): ?>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-industry text-muted me-1"></i> <?php echo htmlspecialchars($produto['marca']); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Preço com destaque melhorado -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="h3 fw-bold text-primary">€<?php echo number_format($produto['preco'], 2, ',', '.'); ?></span>
                                <?php if (isset($produto['preco_original']) && $produto['preco_original'] > $produto['preco']): ?>
                                    <small class="text-muted text-decoration-line-through ms-2">€<?php echo number_format($produto['preco_original'], 2, ',', '.'); ?></small>
                                <?php endif; ?>
                            </div>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i> Em estoque
                            </span>
                        </div>
                    </div>

                    <!-- Ações do produto com layout melhorado -->
                    <div class="product-actions mb-4">
                        <?php if (isset($_SESSION['user'])): ?>
                            <form method="post" action="?page=carrinho">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-3 col-4">
                                        <div class="input-group border rounded">
                                            <button type="button" class="btn btn-outline-secondary border-0" onclick="this.parentNode.querySelector('input[type=number]').stepDown()">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" name="quantidade" class="form-control text-center border-0" value="1" min="1" style="background: transparent;">
                                            <button type="button" class="btn btn-outline-secondary border-0" onclick="this.parentNode.querySelector('input[type=number]').stepUp()">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-8">
                                        <button type="submit" name="adicionar_carrinho" class="btn btn-primary w-100 py-2">
                                            <i class="fas fa-shopping-cart me-2"></i> Adicionar ao Carrinho
                                        </button>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <a href="?page=carrinho&favoritar=<?php echo $produto['id_produto']; ?>" class="btn btn-outline-danger w-100 py-2">
                                            <i class="<?php echo (isset($_SESSION['favoritos']) && in_array($produto['id_produto'], $_SESSION['favoritos']) ? 'fas' : 'far'); ?> fa-heart"></i> Favorito
                                        </a>
                                    </div>
                                </div>
                                <input type="hidden" name="id_produto" value="<?php echo $produto['id_produto']; ?>">
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <a href="?page=login" class="alert-link">Faça login</a> para adicionar produtos ao carrinho ou favoritos.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Entrega e garantia -->
                    <div class="delivery-info p-3 bg-light rounded mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-truck text-primary me-2"></i>
                            <span>Entrega em 2-3 dias úteis</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt text-primary me-2"></i>
                            <span>Garantia do fabricante</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="bg-white p-0 shadow-sm">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#description">
                                <i class="fas fa-info-circle me-2"></i>Descrição
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#reviews">
                                <i class="fas fa-star me-2"></i>Avaliações
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-4 border border-top-0">
                        <div class="tab-pane fade show active" id="description">
                            <h4 class="mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Descrição do Produto</h4>
                            <p><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
                        </div>

                        <div class="tab-pane fade" id="reviews">
                            <div class="text-center py-5 bg-light rounded">
                                <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Este produto ainda não possui avaliações</h5>
                                <p class="text-muted mb-4">Seja o primeiro a compartilhar sua opinião</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="h3 text-center mb-0"><i class="fas fa-random me-2"></i>Produtos Relacionados</h2>
                <p class="text-center text-muted mb-0">Você também pode gostar destes produtos</p>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center py-4">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Em breve - produtos relacionados</p>
            </div>
        </div>
    </div>
</section>

<!-- Adicione esses scripts no final do body -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Troca a imagem principal quando clica nas miniaturas
        document.querySelectorAll('.thumb-container img').forEach(function(thumb) {
            thumb.addEventListener('click', function() {
                const src = this.src;
                document.getElementById('main-product-image').src = src;

                // Remove active class from all thumbnails
                document.querySelectorAll('.thumb-container').forEach(function(container) {
                    container.style.border = '1px solid #dee2e6';
                });

                // Add active class to clicked thumbnail
                this.closest('.thumb-container').style.border = '2px solid #0d6efd';
            });
        });

        // Botão de favoritos
        document.querySelector('.btn-outline-danger').addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-danger');
            } else {
                icon.classList.remove('fas', 'text-danger');
                icon.classList.add('far');
            }
        });
    });
</script>