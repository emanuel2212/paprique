<?php

require_once 'Connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    class DatabaseConnection extends Connection {}
    $dbConnection = new DatabaseConnection();
    $pdo = $dbConnection->connect();

    // Sanitização
    $username = trim($_POST["username"]); // Alterei de 'user' para 'username' para ficar mais claro
    $nome_completo = trim($_POST["nome_completo"]); // Novo campo
    $email = trim($_POST["email"]);
    $contacto = trim($_POST["contacto"]);
    $morada = trim($_POST["morada"]);
    $nif = trim($_POST["nif"]);
    $codigo_postal = trim($_POST["codigo_postal"]);
    $senha = trim($_POST["senha"]);
    $confirmar = trim($_POST["confirmar"]);

    // Inicializa erros
    $erros = [];

    // Validações
    if (empty($username)) {
        $erros[] = "Por favor, insira um nome de utilizador para login.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $erros[] = "O nome de utilizador só pode conter letras, números e underscores.";
    }

    if (empty($nome_completo)) {
        $erros[] = "Por favor, insira seu nome completo.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email inválido.";
    }

    if (!preg_match('/^\d{9}$/', $contacto)) {
        $erros[] = "O contacto deve ter exatamente 9 dígitos.";
    }

    if (!preg_match('/^\d{9}$/', $nif)) {
        $erros[] = "O NIF deve ter exatamente 9 dígitos.";
    }

    if (!preg_match('/^\d{4}-\d{3}$/', $codigo_postal)) {
        $erros[] = "O código postal deve estar no formato 1234-567.";
    }

    if (strlen($senha) < 8) {
        $erros[] = "A senha deve ter pelo menos 8 caracteres.";
    } elseif ($senha != $confirmar) {
        $erros[] = "As senhas não coincidem.";
    }

    // Verificar duplicidade de username
    $stmt = $pdo->prepare("SELECT id_utilizador FROM utilizador WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $erros[] = "Este nome de utilizador já está em uso.";
    }

    // Verificar duplicidade de email
    $stmt = $pdo->prepare("SELECT id_utilizador FROM utilizador WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $erros[] = "Este email já está registado.";
    }

    // Verificar duplicidade de NIF
    $stmt = $pdo->prepare("SELECT id_utilizador FROM utilizador WHERE nif = ?");
    $stmt->execute([$nif]);
    if ($stmt->rowCount() > 0) {
        $erros[] = "Este NIF já está registado.";
    }

    if (empty($erros)) {
        try {
            // Hash da senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $tipo = '3'; // Cliente

            // Inserir no banco
            $insert = $pdo->prepare("INSERT INTO utilizador 
    (id_tipo_utilizador, username, nome_completo, email, password, morada, telefone, nif, codigo_postal, foto_perfil) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'images/default-profile.png')");

            $success = $insert->execute([
                $tipo,
                $username,
                $nome_completo, // Usando o campo nome_completo
                $email,
                $senha_hash,
                $morada,
                $contacto,
                $nif,
                $codigo_postal
            ]);

            if ($success) {
                $_SESSION['register_success'] = "Registro realizado com sucesso! Por favor faça login.";
                header("Location: index.php?page=login");
                exit();
            } else {
                $erros[] = "Erro ao registrar. Por favor, tente novamente.";
            }
        } catch (PDOException $e) {
            $erros[] = "Erro no sistema. Por favor, tente novamente mais tarde.";
            error_log("Erro no registro: " . $e->getMessage());
        }
    }

    // Se houver erros, guarda na sessão e mantém os dados preenchidos
    $_SESSION['register_errors'] = $erros;
    $_SESSION['old_input'] = $_POST;
    header("Location: index.php?page=register");
    exit();
}
?>

<body class="js">
    <section class="shop login section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 col-12">
                    <div class="login-form">
                        <h2>Registar</h2>
                        <p>Por favor registe-se para finalizar a compra mais rapidamente</p>

                        <?php if (isset($_SESSION['register_errors'])): ?>
                            <div class="alert alert-danger" style="margin-bottom:15px;">
                                <?php foreach ($_SESSION['register_errors'] as $erro): ?>
                                    <?= htmlspecialchars($erro) ?><br>
                                <?php endforeach; ?>
                            </div>
                            <?php unset($_SESSION['register_errors']); ?>
                        <?php endif; ?>

                        <form class="form" method="POST" action="">
                            <div class="row">
                                <!-- Primeira Coluna -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Nome de Utilizador<span>*</span></label>
                                        <input type="text" name="username" required
                                            value="<?= isset($_SESSION['old_input']['username']) ? htmlspecialchars($_SESSION['old_input']['username']) : '' ?>">
                                        <small class="form-text text-muted">Letras, números e underscores apenas</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Nome Completo<span>*</span></label>
                                        <input type="text" name="nome_completo" id="nome_completo" required
                                            value="<?= isset($_SESSION['old_input']['nome_completo']) ? htmlspecialchars($_SESSION['old_input']['nome_completo']) : '' ?>">
                                        <small class="form-text text-muted">Apenas letras e espaços</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Email<span>*</span></label>
                                        <input type="email" name="email" required
                                            value="<?= isset($_SESSION['old_input']['email']) ? htmlspecialchars($_SESSION['old_input']['email']) : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Morada<span>*</span></label>
                                        <input type="text" name="morada" required
                                            value="<?= isset($_SESSION['old_input']['morada']) ? htmlspecialchars($_SESSION['old_input']['morada']) : '' ?>">
                                    </div>
                                </div>

                                <!-- Segunda Coluna -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Contacto<span>*</span></label>
                                        <input type="text" name="contacto" maxlength="9" required
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            value="<?= isset($_SESSION['old_input']['contacto']) ? htmlspecialchars($_SESSION['old_input']['contacto']) : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>NIF<span>*</span></label>
                                        <input type="text" name="nif" maxlength="9" required
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            value="<?= isset($_SESSION['old_input']['nif']) ? htmlspecialchars($_SESSION['old_input']['nif']) : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Código Postal<span>*</span></label>
                                        <input type="text" name="codigo_postal" id="codigo_postal" maxlength="8" required
                                            value="<?= isset($_SESSION['old_input']['codigo_postal']) ? htmlspecialchars($_SESSION['old_input']['codigo_postal']) : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Palavra-passe<span>*</span></label>
                                        <input type="password" name="senha" id="senha" required>
                                        <small class="form-text text-muted">Mínimo 8 caracteres</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Confirma Palavra-passe<span>*</span></label>
                                        <input type="password" name="confirmar" required>
                                    </div>
                                </div>

                                <!-- Botão -->
                                <div class="col-12">
                                    <div class="form-group login-btn">
                                        <button class="btn" type="submit">Registar</button>
                                        <a href="?page=login">Já tem uma conta? Clique aqui para fazer o Login</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
<script>
    // Validação do Código Postal
    document.getElementById('codigo_postal').addEventListener('input', function(e) {
        let v = this.value.replace(/\D/g, '');
        if (v.length > 4) {
            this.value = v.slice(0, 4) + '-' + v.slice(4, 7);
        } else {
            this.value = v;
        }
    });

    // Validação do username no frontend
    document.querySelector('input[name="username"]').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^a-zA-Z0-9_]/g, '');
    });

    // Validação do nome completo (não permite números nem caracteres especiais)
    document.getElementById('nome_completo').addEventListener('input', function(e) {
        // Permite letras, espaços e alguns caracteres especiais comuns em nomes (como ç, ã, é, etc.)
        this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
        
        // Remove múltiplos espaços consecutivos
        this.value = this.value.replace(/\s{2,}/g, ' ');
    });

    // Validação do nome completo no submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const nomeCompleto = document.getElementById('nome_completo').value;
        const regex = /^[a-zA-ZÀ-ÿ\s]+$/;
        
        if (!regex.test(nomeCompleto)) {
            alert('O nome completo deve conter apenas letras e espaços.');
            e.preventDefault();
        }
    });
</script>