<?php
require 'vendor/autoload.php';

// Carregar as variáveis de ambiente do arquivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']); // Sanitizar a entrada
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Sanitizar a entrada
    $message = htmlspecialchars($_POST['message']); // Sanitizar a entrada

    // Verifica se o email é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "E-mail inválido!";
        exit;
    }

    $mail = new PHPMailer(true);
    try {
        // Configurações do servidor SMTP (onde você deve inserir essas linhas)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['GMAIL_USERNAME']; // Carrega do arquivo .env
        $mail->Password   = $_ENV['GMAIL_PASSWORD']; // Carrega do arquivo .env
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Remetente e destinatário
        $mail->setFrom($email, $name);
        $mail->addAddress('seuemail@gmail.com'); // Endereço do destinatário

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Nova mensagem de contato';
        $mail->Body    = "<h3>Nova mensagem de: $name</h3><p>$message</p><p>E-mail: $email</p>";
        $mail->AltBody = "Nova mensagem de: $name\n\n$message\n\nE-mail: $email";

        // Enviar o e-mail
        $mail->send();
        echo 'Mensagem enviada com sucesso!';
    } catch (Exception $e) {
        echo "Erro ao enviar mensagem: {$mail->ErrorInfo}";
    }
}
