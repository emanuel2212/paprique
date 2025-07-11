<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'vendor/autoload.php'; // Carrega o autoload do Composer

$mail = new PHPMailer(true);

try {
    // Configurações do servidor SMTP (ex: Gmail, SendGrid, seu servidor)
    $mail->isSMTP();
    $mail->Host = 'smtp.seudominio.com'; // Servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'seuemail@seudominio.com'; // Seu e-mail
    $mail->Password = 'suasenha'; // Sua senha
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS
    $mail->Port = 587; // Porta SMTP (587 para TLS)

    // Remetente e Destinatário
    $mail->setFrom('seuemail@seudominio.com', 'Seu Nome');
    $mail->addAddress('xpto@gmail.com', 'Nome do Cliente'); // Cliente

    // Conteúdo do E-mail
    $mail->isHTML(true);
    $mail->Subject = 'Assunto do E-mail';
    $mail->Body = '
        <p>Prezado(a) Cliente,</p>
        <p>Este é um e-mail de exemplo enviado via <b>PHPMailer</b>.</p>
        <p>Atenciosamente,<br>Equipe XYZ</p>
    ';
    $mail->AltBody = 'Mensagem em texto puro para clientes sem suporte a HTML';

    $mail->send();
    echo 'E-mail enviado com sucesso!';
} catch (Exception $e) {
    echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
}
?>