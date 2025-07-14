<?php
require_once "Connection.php";
require_once "./Produtos.php";

$produtos = new Produtos();

$filtro = $_GET['filtro'] ?? '';
$valor = $_GET['valor'] ?? '';

if (empty($filtro) || empty($valor)) {
    header("Location: index.php");
    exit;
}

$produtosFiltrados = $produtos->filtrarProdutos($filtro, $valor);
?>

<div class="product-area section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2>Produtos filtrados por <?= htmlspecialchars($filtro) ?>: "<?= htmlspecialchars($valor) ?>"</h2>
                <p><?= count($produtosFiltrados) ?> produtos encontrados</p>

                <?php if (empty($produtosFiltrados)): ?>
                    <div class="alert alert-info">
                        Nenhum produto encontrado com este filtro.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($produtosFiltrados as $produto): 
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