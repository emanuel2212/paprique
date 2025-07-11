<?php
if (!isset($_GET['q'])) {
    header("Location: index.php");
    exit;
}

$query = trim($_GET['q']);
$produtos = new Produtos();

$results = $produtos->searchProducts($query);


?>


<div class="product-area section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2>Resultados para: "<?= htmlspecialchars($query) ?>"</h2>
                <p><?= count($results) ?> produtos encontrados</p>

                <?php if (empty($results)): ?>
                    <div class="alert alert-info">
                        Nenhum produto encontrado. Tente outros termos de busca.
                    </div>

                    <div class="suggested-searches">
                        <h4>Talvez você queira buscar por:</h4>
                        <ul>
                            <li><a href="?page=search&q=skate">Skate</a></li>
                            <li><a href="?page=search&q=truck">Truck</a></li>
                            <li><a href="?page=search&q=roda">Roda</a></li>
                            <li><a href="?page=search&q=proteção">Proteção</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($results as $produto):
                            $preco_formatado = number_format($produto['preco'], 2, ',', '.');
                            $imagem = !empty($produto['link_imagem']) ? "images/{$produto['link_imagem']}" : "https://via.placeholder.com/300x300";
                        ?>
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="single-product">
                                    <div class="product-image">
                                        <img src="<?= $imagem ?>" alt="" style="height: 200px; object-fit: cover;">
                                        <div class="button">
                                            <a href="index.php?page=ListProduto&id=<?= $produto['id_produto'] ?>" class="btn"><i class="ti-eye"></i> Ver Detalhes</a>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <span class="category"><?= htmlspecialchars($produto['marca'] ?? 'Sem marca') ?></span>
                                        <h4 class="title">
                                            <a href="?page=ListProduto&id=<?= $produto['id_produto'] ?>"><?= htmlspecialchars($produto['nome_produto']) ?></a>
                                        </h4>
                                        <div class="price">
                                            <span>€<?= $preco_formatado ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>