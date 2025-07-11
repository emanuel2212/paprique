<?php
require './Produtos.php';

$id_marca = $_GET['id'] ?? 0;
$produtos = new Produtos();
$produtos_marca = $produtos->listPorMarca($id_marca);

// Obter nome da marca
$stmt = $produtos->connect()->prepare("SELECT nome_marca FROM marca WHERE id_marca = ?");
$stmt->execute([$id_marca]);
$marca = $stmt->fetch();
$nome_marca = $marca['nome_marca'] ?? 'Marca Desconhecida';
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="section-title">Produtos da marca: <?= htmlspecialchars($nome_marca) ?></h2>
        </div>
    </div>
    <div class="row">
        <?php if (!empty($produtos_marca)): ?>
            <?php foreach ($produtos_marca as $produto): ?>
                <?php
                $preco_formatado = number_format($produto['preco'], 2, ',', '.');
                $imagem = !empty($produto['link_imagem']) ? "../images/{$produto['link_imagem']}" : "https://via.placeholder.com/300x300";
                ?>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="single-product">
                        <div class="product-image">
                            <img src="<?= $imagem ?>" alt="<?= htmlspecialchars($produto['nome_produto']) ?>" style="height: 200px; object-fit: cover;">
                            <div class="button">
                                <a href="?page=ListProduto&id=<?= $produto['id_produto'] ?>" class="btn">
                                    <i class="ti-eye"></i> Ver Detalhes
                                </a>
                            </div>
                        </div>
                        <div class="product-info">
                            <h4 class="title">
                                <a href="?page=ListProduto&id=<?= $produto['id_produto'] ?>">
                                    <?= htmlspecialchars($produto['nome_produto']) ?>
                                </a>
                            </h4>
                            <div class="price">
                                <span>€<?= $preco_formatado ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p>Nenhum produto encontrado para esta marca.</p>
            </div>
        <?php endif; ?>
    </div>
</div>