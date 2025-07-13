<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_to'] = '?page=checkout';
    header("Location: index.php?page=login");
    exit();
}

// Verifica se o carrinho está vazio
if (empty($_SESSION['carrinho'])) {
    $_SESSION['mensagem'] = "Seu carrinho está vazio!";
    header("Location: ?page=carrinho");
    exit();
}

require_once "Connection.php";
require_once __DIR__ . "/../dashboard/admin/Utilizador/Utilizador.php";
require_once __DIR__ . "/../dashboard/bd/Encomendas.php";
require_once __DIR__ . "/../dashboard/bd/EncomendasProdutos.php";
require_once __DIR__ . "/../dashboard/bd/Produtos.php";
require_once __DIR__ . "/../dashboard/bd/MetodosPagamento.php";

$utilizador = new Utilizador();
$utilizador->setId($_SESSION['user']['id_utilizador']);
$userData = $utilizador->view();

$metodosPagamento = new MetodosPagamento();
$metodosDisponiveis = $metodosPagamento->getAll();

// Etapa atual do checkout (1 = Informações, 2 = Pagamento, 3 = Confirmação)
$etapa = $_GET['etapa'] ?? 1;
$etapa = max(1, min(3, (int)$etapa));

// Processar avanço entre etapas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['avancar_etapa'])) {
        // Validar dados da etapa atual
        $valido = validarEtapa($etapa, $_POST);

        if ($valido) {
            $_SESSION['dados_checkout'][$etapa] = $_POST;
            $etapa++;
            header("Location: ?page=checkout&etapa=$etapa");
            exit();
        }
    } elseif (isset($_POST['voltar_etapa'])) {
        $etapa--;
        header("Location: ?page=checkout&etapa=$etapa");
        exit();
    } elseif (isset($_POST['finalizar_compra'])) {
        // Processar finalização da compra
        $dadosCompletos = array_merge(
            $_SESSION['dados_checkout'][1] ?? [],
            $_SESSION['dados_checkout'][2] ?? []
        );

        $encomendas = new Encomendas();
        $encomendasProdutos = new EncomendasProdutos();
        $produtos_class = new Produtos();

        // Calcular total
        $total = 0;
        foreach ($_SESSION['carrinho'] as $id_prod => $item) {
            $produto = $produtos_class->getById($id_prod);
            if ($produto) {
                $total += $produto['preco'] * $item['quantidade'];
            }
        }

        // Criar encomenda
        $dadosEncomenda = [
            'id_utilizador' => $_SESSION['user']['id_utilizador'],
            'morada' => $dadosCompletos['morada'],
            'codigo_postal' => $dadosCompletos['codigo_postal'],
            'telefone' => $dadosCompletos['telefone'],
            'metodo_pagamento' => $dadosCompletos['metodo_pagamento'],
            'observacoes' => $dadosCompletos['observacoes'] ?? null
        ];

        $id_encomenda = $encomendas->create($dadosEncomenda, $total);

        if ($id_encomenda) {
            // Adicionar produtos
            foreach ($_SESSION['carrinho'] as $id_prod => $item) {
                $produto = $produtos_class->getById($id_prod);
                if ($produto) {
                    $encomendasProdutos->create([
                        'id_encomenda' => $id_encomenda,
                        'id_produto' => $id_prod,
                        'quantidade' => $item['quantidade'],
                        'preco_unitario' => $produto['preco']
                    ]);
                }
            }

            unset($_SESSION['carrinho'], $_SESSION['dados_checkout']);
            $_SESSION['mensagem'] = "Compra realizada com sucesso! Nº do pedido: #" . $id_encomenda;
            header("Location: ?page=profile");
            exit();
        } else {
            $erro = "Ocorreu um erro ao processar seu pedido. Por favor, tente novamente.";
        }
    }
}

function validarEtapa(int $etapa, array $dados): bool
{
    switch ($etapa) {
        case 1: // Informações
            return !empty($dados['morada']) &&
                !empty($dados['codigo_postal']) &&
                !empty($dados['telefone']) &&
                preg_match('/^[0-9]{9}$/', $dados['telefone']); // Validação de telefone

        case 2: // Pagamento
            if (empty($dados['metodo_pagamento'])) {
                return false;
            }

            // Validar campos específicos do método de pagamento
            switch ($dados['metodo_pagamento']) {
                case 'MBWay':
                    return !empty($dados['mbway_numero']) &&
                        preg_match('/^9[0-9]{8}$/', $dados['mbway_numero']);

                case 'Cartão de Crédito':
                    // Validação do número do cartão (Luhn algorithm)
                    $cartaoValido = !empty($dados['cartao_numero']) &&
                        preg_match('/^[0-9]{13,16}$/', str_replace(' ', '', $dados['cartao_numero']));

                    // Validação da data (MM/AA)
                    $validadeValida = !empty($dados['cartao_validade']) &&
                        preg_match('/^(0[1-9]|1[0-2])\/?([0-9]{2})$/', $dados['cartao_validade']);

                    // Validação do CVV
                    $cvvValido = !empty($dados['cartao_cvv']) &&
                        preg_match('/^[0-9]{3,4}$/', $dados['cartao_cvv']);

                    // Validação do nome
                    $nomeValido = !empty($dados['cartao_titular']);

                    return $cartaoValido && $validadeValida && $cvvValido && $nomeValido;

                default:
                    return true;
            }

        default:
            return true;
    }
}

function formatField($value, $default = '')
{
    return !empty($value) ? htmlspecialchars($value) : $default;
}
?>

<section class="shop checkout section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12">
                <div class="checkout-form">
                    <!-- Progresso do Checkout -->
                    <div class="checkout-progress mb-4">
                        <?php for ($i = 1; $i <= 3; $i++): ?>
                            <div class="step <?= $i == $etapa ? 'active' : ($i < $etapa ? 'completed' : '') ?>">
                                <span><?= $i ?></span>
                                <p><?= ['Informações', 'Pagamento', 'Confirmação'][$i - 1] ?></p>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <h2 class="mb-4"><i class="ti-credit-card"></i> Finalizar Compra</h2>

                    <?php if (isset($erro)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
                    <?php endif; ?>

                    <form method="post" id="checkout-form">
                        <?php if ($etapa == 1): ?>
                            <!-- Etapa 1: Informações -->
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h4 class="section-title"><i class="ti-truck"></i> Informações de Entrega</h4>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nome" class="form-label">Nome Completo</label>
                                    <input type="text" class="form-control" id="nome"
                                        value="<?php echo formatField($userData['nome_completo'], 'Não informado'); ?>" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email"
                                        value="<?php echo formatField($userData['email'], 'Não informado'); ?>" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="telefone" class="form-label">Telefone*</label>
                                    <input type="text" class="form-control" id="telefone" name="telefone"
                                        value="<?php echo formatField($userData['telefone']); ?>" required>
                                    <div class="invalid-feedback">Por favor, insira um telefone válido.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nif" class="form-label">NIF</label>
                                    <input type="text" class="form-control" id="nif"
                                        value="<?php echo formatField($userData['nif'], 'Não informado'); ?>" readonly>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="morada" class="form-label">Morada*</label>
                                    <input type="text" class="form-control" id="morada" name="morada"
                                        value="<?php echo formatField($userData['morada']); ?>" required>
                                    <div class="invalid-feedback">Por favor, insira sua morada.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="codigo_postal" class="form-label">Código Postal*</label>
                                    <input type="text" class="form-control" id="codigo_postal" name="codigo_postal"
                                        value="<?php echo formatField($userData['codigo_postal']); ?>" required>
                                    <div class="invalid-feedback">Por favor, insira um código postal válido.</div>
                                </div>

                                <div class="col-12 mb-4">
                                    <label for="observacoes" class="form-label">Observações (opcional)</label>
                                    <textarea class="form-control" id="observacoes" name="observacoes" rows="2" placeholder="Instruções especiais para entrega..."></textarea>
                                </div>

                                <div class="col-12">
                                    <button type="submit" name="avancar_etapa" class="btn btn-primary btn-block btn-lg">
                                        Continuar para Pagamento <i class="ti-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                        <?php elseif ($etapa == 2): ?>
                            <!-- Etapa 2: Pagamento -->

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h4 class="section-title"><i class="ti-credit-card"></i> Método de Pagamento</h4>
                                    <p class="text-muted">Selecione como deseja pagar</p>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fas fa-lock"></i> Todos os dados de pagamento são criptografados e processados com segurança.
                                </div>

                                <div class="col-12 mb-4">
                                    <div class="payment-methods">
                                        <div class="payment-option">
                                            <input class="form-check-input" type="radio" name="metodo_pagamento"
                                                id="mbway"
                                                value="MBWay"
                                                <?= (!isset($_SESSION['dados_checkout'][2]['metodo_pagamento']) || (isset($_SESSION['dados_checkout'][2]['metodo_pagamento']) && $_SESSION['dados_checkout'][2]['metodo_pagamento'] === 'MBWay')) ? 'checked' : '' ?>
                                                data-metodo="mbway">
                                            <label class="form-check-label" for="mbway">
                                                <div class="payment-method-icon">
                                                    <i class="fas fa-phone-alt"></i> <!-- Ícone de telefone corrigido -->
                                                </div>
                                                <div class="payment-method-info">
                                                    <h5>MBWay</h5>
                                                    <p>Pague com o seu telemóvel</p>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="payment-option">
                                            <input class="form-check-input" type="radio" name="metodo_pagamento"
                                                id="cartao"
                                                value="Cartão de Crédito"
                                                <?= (isset($_SESSION['dados_checkout'][2]['metodo_pagamento']) && $_SESSION['dados_checkout'][2]['metodo_pagamento'] === 'Cartão de Crédito') ? 'checked' : '' ?>
                                                data-metodo="cartao">
                                            <label class="form-check-label" for="cartao">
                                                <div class="payment-method-icon">
                                                    <i class="far fa-credit-card"></i>
                                                </div>
                                                <div class="payment-method-info">
                                                    <h5>Cartão de Crédito</h5>
                                                    <p>Pague com cartão Visa, Mastercard, etc.</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Campos dinâmicos para cada método de pagamento -->
                                <div id="campos-pagamento" class="col-12 mb-4">
                                    <!-- MBWay -->
                                    <div class="metodo-campos mbway-campos" style="display: block;">
                                        <h5 class="mb-3"><i class="fas fa-mobile-alt mr-2"></i> Dados MBWay</h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="mbway_numero" class="form-label">Número de Telemóvel*</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                    <input type="tel" class="form-control" id="mbway_numero" name="mbway_numero"
                                                        placeholder="912345678" pattern="[9][0-9]{8}"
                                                        value="<?= formatField($_SESSION['dados_checkout'][2]['mbway_numero'] ?? '') ?>">
                                                </div>
                                                <small class="text-muted">Formato: 912345678</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cartão de Crédito -->
                                    <div class="metodo-campos cartao-campos" style="display: none;">
                                        <h5 class="mb-3"><i class="fas fa-credit-card mr-2"></i> Dados do Cartão</h5>
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label for="cartao_numero" class="form-label">Número do Cartão*</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="far fa-credit-card"></i></span>
                                                    <input type="text" class="form-control" id="cartao_numero" name="cartao_numero" maxlength="12"
                                                        placeholder="1234 5678 9012 3456" pattern="[0-9\s]{13,19}"
                                                        value="<?= formatField($_SESSION['dados_checkout'][2]['cartao_numero'] ?? '') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="cartao_validade" class="form-label">Validade (MM/AA)*</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                    <input type="text" class="form-control" id="cartao_validade" name="cartao_validade"
                                                        placeholder="12/25" pattern="(0[1-9]|1[0-2])\/[0-9]{2}"
                                                        value="<?= formatField($_SESSION['dados_checkout'][2]['cartao_validade'] ?? '') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="cartao_cvv" class="form-label">CVV*</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                    <input type="text" class="form-control" id="cartao_cvv" name="cartao_cvv" maxlength="3"
                                                        placeholder="123" pattern="[0-9]{3,4}"
                                                        value="<?= formatField($_SESSION['dados_checkout'][2]['cartao_cvv'] ?? '') ?>">
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label for="cartao_titular" class="form-label">Nome do Titular*</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="far fa-user"></i></span>
                                                    <input type="text" class="form-control" id="cartao_titular" name="cartao_titular"
                                                        value="<?= formatField($_SESSION['dados_checkout'][2]['cartao_titular'] ?? $userData['nome_completo']) ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-between">
                                    <button type="submit" name="voltar_etapa" class="btn btn-secondary">
                                        <i class="ti-arrow-left"></i> Voltar
                                    </button>
                                    <button type="submit" name="avancar_etapa" class="btn btn-primary">
                                        Continuar para Confirmação <i class="ti-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                        <?php elseif ($etapa == 3): ?>
                            <!-- Etapa 3: Confirmação -->
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="alert alert-success">
                                        <h4><i class="ti-check"></i> Revise seu pedido</h4>
                                        <p class="mb-0">Por favor, confira todas as informações antes de finalizar sua compra.</p>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <h4 class="section-title"><i class="ti-truck"></i> Informações de Entrega</h4>
                                    <div class="card card-body bg-light">
                                        <?php
                                        $dadosEtapa1 = $_SESSION['dados_checkout'][1] ?? [];
                                        ?>
                                        <p><strong>Nome:</strong> <?= $userData['nome_completo'] ?></p>
                                        <p><strong>Telefone:</strong> <?= $dadosEtapa1['telefone'] ?? $userData['telefone'] ?></p>
                                        <p><strong>Morada:</strong> <?= $dadosEtapa1['morada'] ?? $userData['morada'] ?></p>
                                        <p><strong>Código Postal:</strong> <?= $dadosEtapa1['codigo_postal'] ?? $userData['codigo_postal'] ?></p>
                                        <?php if (!empty($dadosEtapa1['observacoes'])): ?>
                                            <p><strong>Observações:</strong> <?= $dadosEtapa1['observacoes'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <h4 class="section-title"><i class="ti-credit-card"></i> Método de Pagamento</h4>
                                    <div class="card card-body bg-light">
                                        <?php
                                        $dadosEtapa2 = $_SESSION['dados_checkout'][2] ?? [];
                                        $metodoPagamento = $dadosEtapa2['metodo_pagamento'] ?? 'MBWay';
                                        ?>
                                        <p><i class="fas fa-<?= strtolower($metodoPagamento) == 'mbway' ? 'mobile-alt' : 'credit-card' ?>"></i>
                                            <strong><?= $metodoPagamento ?></strong>
                                        </p>
                                        <?php if ($metodoPagamento == 'MBWay'): ?>
                                            <p><strong>Número:</strong> <?= $dadosEtapa2['mbway_numero'] ?? '' ?></p>
                                        <?php elseif ($metodoPagamento == 'Cartão de Crédito'): ?>
                                            <p><strong>Termina em:</strong> **** **** **** <?= substr($dadosEtapa2['cartao_numero'] ?? '', -4) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-between">
                                    <button type="submit" name="voltar_etapa" class="btn btn-secondary">
                                        <i class="ti-arrow-left"></i> Voltar
                                    </button>
                                    <button type="submit" name="finalizar_compra" class="btn btn-success">
                                        <i class="ti-check-box"></i> Confirmar Pedido
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="col-lg-4 col-12">
                <div class="order-details sticky-top">
                    <div class="order-summary">
                        <h3 class="mb-4"><i class="ti-shopping-cart"></i> Resumo do Pedido</h3>

                        <div class="order-items">
                            <?php
                            $total = 0;
                            $produtos_class = new Produtos();

                            foreach ($_SESSION['carrinho'] as $id_prod => $item):
                                $produto = $produtos_class->getById($id_prod);
                                if ($produto):
                                    $subtotal = $produto['preco'] * $item['quantidade'];
                                    $total += $subtotal;
                            ?>
                                    <div class="order-item">
                                        <div class="item-image">
                                            <img src="./images/<?php echo htmlspecialchars($produto['link_imagem'] ?? 'default-product.jpg'); ?>"
                                                alt="<?php echo htmlspecialchars($produto['nome_produto']); ?>">
                                        </div>
                                        <div class="item-details">
                                            <h6><?php echo htmlspecialchars($produto['nome_produto']); ?></h6>
                                            <p><?php echo $item['quantidade']; ?> × €<?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                                        </div>
                                        <div class="item-total">
                                            €<?php echo number_format($subtotal, 2, ',', '.'); ?>
                                        </div>
                                    </div>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>

                        <div class="order-totals">
                            <div class="total-row">
                                <span>Subtotal</span>
                                <span>€<?php echo number_format($total, 2, ',', '.'); ?></span>
                            </div>
                            <div class="total-row">
                                <span>Envio</span>
                                <span>Grátis</span>
                            </div>
                            <div class="total-row grand-total">
                                <span>Total</span>
                                <span>€<?php echo number_format($total, 2, ',', '.'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Estilos gerais */
    .checkout-form {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        color: #333;
        font-size: 1.25rem;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    /* Progresso do checkout */
    .checkout-progress {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        position: relative;
    }

    .checkout-progress:before {
        content: '';
        position: absolute;
        top: 15px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e0e0e0;
        z-index: 1;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .step span {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .step p {
        font-size: 0.8rem;
        color: #999;
        margin: 0;
    }

    .step.active span {
        background: #007bff;
    }

    .step.active p {
        color: #007bff;
        font-weight: bold;
    }

    .step.completed span {
        background: #28a745;
    }

    .step.completed p {
        color: #28a745;
    }

    /* Métodos de pagamento */
    .payment-methods {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
    }

    .payment-option {
        padding: 15px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        transition: background-color 0.2s;
    }

    .payment-option:last-child {
        border-bottom: none;
    }

    .payment-option:hover {
        background-color: #f8f9fa;
    }

    .payment-option label {
        display: flex;
        align-items: center;
        margin-left: 10px;
        cursor: pointer;
        width: 100%;
    }

    .payment-method-icon {
        width: 40px;
        height: 40px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: #007bff;
    }

    .payment-method-info h5 {
        margin: 0;
        font-size: 1rem;
    }

    .payment-method-info p {
        margin: 0;
        font-size: 0.8rem;
        color: #6c757d;
    }

    /* Resumo do pedido */
    .order-details {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .order-items {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 20px;
    }

    .order-item {
        display: flex;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .item-image {
        width: 60px;
        height: 60px;
        margin-right: 15px;
    }

    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 5px;
    }

    .item-details {
        flex: 1;
    }

    .item-details h6 {
        margin: 0;
        font-size: 0.9rem;
    }

    .item-details p {
        margin: 5px 0 0;
        font-size: 0.8rem;
        color: #666;
    }

    .item-total {
        font-weight: bold;
    }

    .order-totals {
        border-top: 2px solid #f0f0f0;
        padding-top: 15px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .grand-total {
        font-weight: bold;
        font-size: 1.1rem;
        color: #333;
    }

    /* Botões */
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        padding: 12px;
        font-weight: bold;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #0069d9;
        border-color: #0062cc;
        transform: translateY(-2px);
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        padding: 12px;
        font-weight: bold;
        transition: all 0.3s;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        padding: 12px;
        font-weight: bold;
        transition: all 0.3s;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
        transform: translateY(-2px);
    }

    /* Campos de pagamento */
    .metodo-campos {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        margin-top: 15px;
        border: 1px solid #e0e0e0;
    }

    .metodo-campos h5 {
        color: #333;
        font-size: 1.1rem;
        margin-bottom: 15px;
    }

    .input-group-text {
        background-color: #f8f9fa;
    }

    /* Responsividade */
    @media (max-width: 991px) {
        .order-details {
            margin-top: 30px;
        }
    }

    @media (max-width: 767px) {
        .checkout-progress {
            flex-wrap: wrap;
        }

        .step {
            width: 33%;
            margin-bottom: 15px;
        }

        .payment-method-info h5 {
            font-size: 0.9rem;
        }

        .payment-method-info p {
            font-size: 0.7rem;
        }
    }

    .form-check-input.is-invalid {
        border-color: #dc3545;
    }

    .form-check-input.is-invalid~.form-check-label {
        color: #dc3545;
    }
</style>

<script>
    // Mostrar campos específicos para cada método de pagamento
    document.querySelectorAll('input[name="metodo_pagamento"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Esconder todos os campos primeiro
            document.querySelectorAll('.metodo-campos').forEach(div => {
                div.style.display = 'none';
            });

            // Mostrar apenas os campos do método selecionado
            const metodo = this.getAttribute('data-metodo');
            document.querySelector(`.${metodo}-campos`).style.display = 'block';
        });
    });

    // Validação do formulário na etapa de pagamento
    document.getElementById('checkout-form').addEventListener('submit', function(event) {
        <?php if ($etapa == 2): ?>
            const metodoPagamento = document.querySelector('input[name="metodo_pagamento"]:checked').value;
            let isValid = true;

            // Resetar erros
            this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            // Validar campos específicos do método selecionado
            if (metodoPagamento === 'MBWay') {
                const mbwayNumero = document.getElementById('mbway_numero');
                if (!mbwayNumero.value.trim() || !/^9[0-9]{8}$/.test(mbwayNumero.value)) {
                    mbwayNumero.classList.add('is-invalid');
                    isValid = false;
                }
            } else if (metodoPagamento === 'Cartão de Crédito') {
                const camposCartao = [{
                        id: 'cartao_numero',
                        regex: /^[0-9]{13,16}$/
                    },
                    {
                        id: 'cartao_validade',
                        regex: /^(0[1-9]|1[0-2])\/?([0-9]{2})$/
                    },
                    {
                        id: 'cartao_cvv',
                        regex: /^[0-9]{3,4}$/
                    },
                    {
                        id: 'cartao_titular',
                        regex: /.+/
                    }
                ];

                camposCartao.forEach(campo => {
                    const elemento = document.getElementById(campo.id);
                    const valor = campo.id === 'cartao_numero' ?
                        elemento.value.replace(/\s/g, '') : elemento.value;

                    if (!valor.trim() || !campo.regex.test(valor)) {
                        elemento.classList.add('is-invalid');
                        isValid = false;
                    }
                });
            }

            if (!isValid) {
                event.preventDefault();
                event.stopPropagation();

                // Rolar até o primeiro erro
                const firstInvalid = this.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    // Mostrar mensagem de erro
                    alert('Por favor, preencha corretamente os campos destacados em vermelho.');
                }
            }
        <?php endif; ?>
    });
</script>