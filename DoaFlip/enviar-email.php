<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Recebe os dados do formulário
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

$mail = new PHPMailer(true);

try {
    // Configurações do servidor SMTP do Gmail
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'emanuelhenrique05@gmail.com'; // Seu email do Gmail
    $mail->Password = 'xzwlugkjmccnblqv'; // Sua senha do Gmail ou senha de app
    $mail->SMTPSecure = 'tls'; // Usando string diretamente em vez da constante
    $mail->Port = 587;
    
    // Remetente e destinatário
    $mail->setFrom('emanuelhenrique05@gmail.com', 'Seu Nome');
    $mail->addAddress('destinatario@gmail.com', 'Nome do Destinatário');
    
    // Responder para o email do formulário
    $mail->addReplyTo($email, $name);

    // Conteúdo do email
    $mail->isHTML(true);
    $mail->Subject = $subject;
    
    $mail->Body = "
        <h2>Nova mensagem de contato</h2>
        <p><strong>Nome:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Assunto:</strong> {$subject}</p>
        <p><strong>Mensagem:</strong></p>
        <p>{$message}</p>
    ";
    
    $mail->AltBody = "Nome: {$name}\nEmail: {$email}\nAssunto: {$subject}\nMensagem:\n{$message}";

    $mail->send();
    header("Location: mail-send.html");
    exit();
} catch (Exception $e) {
    echo "Erro ao enviar mensagem. Por favor, tente novamente mais tarde.";
    // Para debug (remova em produção):
    // echo "Erro: " . $e->getMessage();
}
?>