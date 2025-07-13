<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/paprique/DoaFlip/dashboard/admin/Utilizador/Utilizador.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_to'] = '?page=profile';
    header("Location: ?page=login");
    exit();
}

// Inicializa a classe Utilizador
$user = new Utilizador();
$user->setId($_SESSION['user']['id_utilizador']);
$userData = $user->view();

// Processar atualização do perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Filtra os dados do POST
    $formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Adiciona o ID do utilizador aos dados do formulário
    $formData['id_utilizador'] = $_SESSION['user']['id_utilizador'];

    // Atualizar dados básicos
    if (isset($_POST['update_profile'])) {
        // Validação dos campos obrigatórios
        $requiredFields = ['username', 'nome_completo', 'email'];
        $isValid = true;

        foreach ($requiredFields as $field) {
            if (empty($formData[$field])) {
                $erro = "O campo " . ucfirst(str_replace('_', ' ', $field)) . " é obrigatório!";
                $isValid = false;
                break;
            }
        }

        if ($isValid) {
            $user->setFormData($formData);
            if ($user->edit()) {
                // Atualiza os dados na sessão
                $_SESSION['user']['username'] = $formData['username'];
                $_SESSION['user']['nome_completo'] = $formData['nome_completo'];
                $_SESSION['user']['email'] = $formData['email'];
                $_SESSION['user']['morada'] = $formData['morada'] ?? null;
                $_SESSION['user']['telefone'] = $formData['telefone'] ?? null;
                $_SESSION['user']['codigo_postal'] = $formData['codigo_postal'] ?? null;
                $_SESSION['user']['nif'] = $formData['nif'] ?? null;

                $_SESSION['mensagem'] = "Perfil atualizado com sucesso!";
                header("Location: ?page=profile");
                exit();
            } else {
                $erro = "Erro ao atualizar perfil. Tente novamente.";
            }
        }
    }

    // Atualizar senha (mantendo SHA1)
    if (isset($_POST['update_password'])) {
        $current_password = sha1($formData['current_password']);
        $new_password = $formData['new_password'];
        $confirm_password = $formData['confirm_password'];

        if ($current_password === $_SESSION['user']['password']) {
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 8) {
                    $hashedPassword = sha1($new_password);
                    if ($user->updatePassword($_SESSION['user']['id_utilizador'], $hashedPassword)) {
                        $_SESSION['user']['password'] = $hashedPassword;
                        $_SESSION['mensagem'] = "Senha atualizada com sucesso!";
                        header("Location: ?page=profile");
                        exit();
                    } else {
                        $erro = "Erro ao atualizar senha no banco de dados.";
                    }
                } else {
                    $erro = "A nova senha deve ter pelo menos 8 caracteres.";
                }
            } else {
                $erro = "As novas senhas não coincidem.";
            }
        } else {
            $erro = "Senha atual incorreta.";
        }
    }

    // Atualizar foto de perfil
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'images/profiles/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Valida o tipo e tamanho do arquivo (máx 2MB)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (
            in_array($_FILES['foto_perfil']['type'], $allowedTypes) &&
            $_FILES['foto_perfil']['size'] <= $maxSize
        ) {

            $ext = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $_SESSION['user']['id_utilizador'] . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $targetPath)) {
                if ($user->updateProfilePicture($_SESSION['user']['id_utilizador'], $targetPath)) {
                    // Remove a foto antiga se não for a padrão
                    if (
                        $_SESSION['user']['foto_perfil'] !== 'images/default-profile.png' &&
                        file_exists($_SESSION['user']['foto_perfil'])
                    ) {
                        unlink($_SESSION['user']['foto_perfil']);
                    }

                    $_SESSION['user']['foto_perfil'] = $targetPath;
                    $_SESSION['mensagem'] = "Foto de perfil atualizada com sucesso!";
                    header("Location: ?page=profile");
                    exit();
                } else {
                    $erro = "Erro ao atualizar foto no banco de dados.";
                    unlink($targetPath); // Remove o arquivo se falhar no BD
                }
            } else {
                $erro = "Erro ao fazer upload da imagem.";
            }
        } else {
            $erro = "A imagem deve ser JPG, PNG ou GIF com no máximo 2MB.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Do a Flip Skateshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .accordion-button:not(.collapsed) {
            background-color: rgba(13, 110, 253, 0.05);
            box-shadow: none;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0, 0, 0, .125);
        }

        .accordion-item {
            border-radius: 8px !important;
            overflow: hidden;
        }

        .order-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-processando {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-enviada {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-entregue {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelada {
            background-color: #f8d7da;
            color: #721c24;
        }

        .profile-container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 40px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .profile-picture-container {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }

        .profile-picture {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 6px solid #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .profile-picture:hover {
            transform: scale(1.03);
        }

        .change-photo-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #0d6efd;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .change-photo-btn:hover {
            background: #0b5ed7;
            transform: scale(1.1);
        }

        .profile-nav .nav-link {
            color: #6c757d;
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 8px 8px 0 0;
            margin-right: 5px;
            transition: all 0.3s ease;
            border: none;
            background: transparent;
        }

        .profile-nav .nav-link.active {
            color: #0d6efd;
            background: rgba(13, 110, 253, 0.1);
            border-bottom: 3px solid #0d6efd;
        }

        .profile-nav .nav-link:hover:not(.active) {
            background: rgba(108, 117, 125, 0.1);
        }

        .tab-content {
            padding: 30px 0;
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-color: #86b7fe;
        }

        .btn-primary {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }

        .alert {
            border-radius: 8px;
            padding: 15px 20px;
        }

        .profile-section-title {
            font-size: 1.2rem;
            color: #495057;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }

        .nav-tabs {
            border-bottom: 1px solid #eee;
        }

        .badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="container profile-container">
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['mensagem'];
                unset($_SESSION['mensagem']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($erro)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $erro; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="profile-header">
            <div class="profile-picture-container">
                <img src="<?php echo !empty($userData['foto_perfil']) ? htmlspecialchars($userData['foto_perfil']) : 'images/default-profile.png'; ?>"
                    class="profile-picture"
                    alt="Foto de perfil">
                <button class="change-photo-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#changePhotoModal">
                    <i class="fas fa-camera"></i>
                </button>
            </div>
            <h2 class="mt-3 mb-1"><?php echo htmlspecialchars($userData['nome_completo']); ?></h2>
            <p class="text-muted mb-2">@<?php echo htmlspecialchars($userData['username']); ?></p>
            <div class="d-flex justify-content-center gap-2">
                <span class="badge bg-primary bg-gradient">
                    <i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($userData['email']); ?>
                </span>
                <?php if (!empty($userData['telefone'])): ?>
                    <span class="badge bg-secondary bg-gradient">
                        <i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($userData['telefone']); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <ul class="nav nav-tabs profile-nav" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                    <i class="fas fa-user-circle me-2"></i> Informações Pessoais
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                    <i class="fas fa-key me-2"></i> Segurança
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                    <i class="fas fa-shopping-bag me-2"></i> Histórico de Compras
                </button>
            </li>
        </ul>

        <div class="tab-content" id="profileTabsContent">
            <!-- Informações do perfil -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <h5 class="profile-section-title">
                    <i class="fas fa-user-edit me-2"></i> Editar Informações
                </h5>
                <form method="POST" action="">
                    <input type="hidden" name="id_utilizador" value="<?php echo $_SESSION['user']['id_utilizador']; ?>">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?php echo htmlspecialchars($userData['username']); ?>" required>
                                <label for="username">Nome de Utilizador</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nome_completo" name="nome_completo"
                                    value="<?php echo htmlspecialchars($userData['nome_completo']); ?>" required>
                                <label for="nome_completo">Nome Completo</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                                <label for="email">Endereço de Email</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="tel" class="form-control" id="telefone" name="telefone"
                                    value="<?php echo htmlspecialchars($userData['telefone']); ?>">
                                <label for="telefone">Telefone</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="morada" name="morada"
                                    value="<?php echo htmlspecialchars($userData['morada']); ?>">
                                <label for="morada">Morada</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="codigo_postal" name="codigo_postal"
                                    value="<?php echo htmlspecialchars($userData['codigo_postal']); ?>">
                                <label for="codigo_postal">Código Postal</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nif" name="nif"
                                    value="<?php echo htmlspecialchars($userData['nif']); ?>">
                                <label for="nif">NIF</label>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" name="update_profile" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Guardar Alterações
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Alterar senha -->
            <div class="tab-pane fade" id="password" role="tabpanel">
                <h5 class="profile-section-title">
                    <i class="fas fa-shield-alt me-2"></i> Segurança da Conta
                </h5>
                <form method="POST" action="">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                <label for="current_password">Senha Atual</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <label for="new_password">Nova Senha</label>
                                <div class="form-text">Mínimo de 8 caracteres</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <label for="confirm_password">Confirmar Nova Senha</label>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" name="update_password" class="btn btn-primary px-4">
                                <i class="fas fa-key me-2"></i>Alterar Senha
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Histórico de compras -->
           
            <div class="tab-pane fade" id="orders" role="tabpanel">
                <h5 class="profile-section-title">
                    <i class="fas fa-history me-2"></i> Minhas Compras
                </h5>

                <?php
                require_once $_SERVER['DOCUMENT_ROOT'] . '/paprique/DoaFlip/dashboard/bd/Encomendas.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . '/paprique/DoaFlip/dashboard/bd/EncomendasProdutos.php';

                $encomendas = new Encomendas();
                $userOrders = $encomendas->getByUser($_SESSION['user']['id_utilizador']);

                if (empty($userOrders)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Nenhuma encomenda encontrada.
                    </div>
                <?php else: ?>
                    <div class="accordion" id="ordersAccordion">
                        <?php foreach ($userOrders as $order):
                            $encomendasProdutos = new EncomendasProdutos();
                            $orderItems = $encomendasProdutos->getByEncomenda($order['id_encomenda']);
                            $orderDate = new DateTime($order['data_encomenda']);
                        ?>
                            <div class="accordion-item mb-3 border-0 shadow-sm">
                                <h2 class="accordion-header" id="heading<?php echo $order['id_encomenda']; ?>">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?php echo $order['id_encomenda']; ?>"
                                        aria-expanded="false"
                                        aria-controls="collapse<?php echo $order['id_encomenda']; ?>">
                                        <div class="d-flex justify-content-between w-100">
                                            <div>
                                                <span class="fw-bold me-3">Encomenda #<?php echo $order['id_encomenda']; ?></span>
                                                <span class="text-muted"><?php echo $orderDate->format('d/m/Y H:i'); ?></span>
                                            </div>
                                            <div>
                                                <span class="badge bg-<?php
                                                                        switch ($order['status']) {
                                                                            case 'Pendente':
                                                                                echo 'warning';
                                                                                break;
                                                                            case 'Processando':
                                                                                echo 'info';
                                                                                break;
                                                                            case 'Enviada':
                                                                                echo 'primary';
                                                                                break;
                                                                            case 'Entregue':
                                                                                echo 'success';
                                                                                break;
                                                                            case 'Cancelada':
                                                                                echo 'danger';
                                                                                break;
                                                                            default:
                                                                                echo 'secondary';
                                                                        }
                                                                        ?>">
                                                    <?php echo htmlspecialchars($order['status']); ?>
                                                </span>
                                                <span class="ms-3 fw-bold">€<?php echo number_format($order['valor_total'], 2, ',', '.'); ?></span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse<?php echo $order['id_encomenda']; ?>"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="heading<?php echo $order['id_encomenda']; ?>"
                                    data-bs-parent="#ordersAccordion">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive">
                                            <table class="table mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Produto</th>
                                                        <th class="text-end">Preço Unitário</th>
                                                        <th class="text-center">Quantidade</th>
                                                        <th class="text-end">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($orderItems as $item): ?>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <?php if (!empty($item['link_imagem'])): ?>
                                                                        <img src="images/<?php echo htmlspecialchars($item['link_imagem']); ?>"
                                                                            class="img-thumbnail me-3"
                                                                            style="width: 60px; height: 60px; object-fit: cover;"
                                                                            alt="<?php echo htmlspecialchars($item['nome_produto']); ?>">
                                                                    <?php endif; ?>
                                                                    <div>
                                                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['nome_produto']); ?></h6>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-end">€<?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                                                            <td class="text-center"><?php echo $item['quantidade']; ?></td>
                                                            <td class="text-end">€<?php echo number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.'); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    <tr class="table-light">
                                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                                        <td class="text-end fw-bold">€<?php echo number_format($order['valor_total'], 2, ',', '.'); ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="p-3 border-top">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="mb-2">Informações de Envio</h6>
                                                    <p class="mb-1">
                                                        <i class="fas fa-user me-2"></i>
                                                        <?php echo htmlspecialchars($userData['nome_completo']); ?>
                                                    </p>
                                                    <p class="mb-1">
                                                        <i class="fas fa-map-marker-alt me-2"></i>
                                                        <?php echo htmlspecialchars($userData['morada']); ?>
                                                    </p>
                                                    <p class="mb-1">
                                                        <i class="fas fa-mail-bulk me-2"></i>
                                                        <?php echo htmlspecialchars($userData['codigo_postal']); ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-6 text-md-end">
                                                    <h6 class="mb-2">Método de Pagamento</h6>
                                                    <p class="mb-1">
                                                        <i class="fas fa-credit-card me-2"></i>
                                                        <?php echo htmlspecialchars($order['metodo_pagamento'] ?? 'Não especificado'); ?>
                                                    </p>
                                                    <?php if (!empty($order['observacoes'])): ?>
                                                        <h6 class="mt-3 mb-2">Observações</h6>
                                                        <p><?php echo htmlspecialchars($order['observacoes']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
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

    <!-- Modal para alterar foto de perfil -->
    <div class="modal fade" id="changePhotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="fas fa-camera me-2"></i> Alterar Foto de Perfil
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <img id="imagePreview" src="<?php echo !empty($userData['foto_perfil']) ? htmlspecialchars($userData['foto_perfil']) : 'images/default-profile.png'; ?>"
                                class="img-thumbnail rounded-circle"
                                style="width: 150px; height: 150px; object-fit: cover;"
                                alt="Pré-visualização">
                        </div>
                        <div class="mb-3">
                            <label for="foto_perfil" class="form-label">Selecione uma nova imagem</label>
                            <input class="form-control" type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
                            <div class="form-text">Formatos suportados: JPG, PNG, GIF. Tamanho máximo: 2MB.</div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar preview da imagem selecionada
        document.getElementById('foto_perfil').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = this.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('shadow-sm');
                }

                reader.readAsDataURL(file);
            }
        });

        // Animar abas ao trocar
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>

</html>