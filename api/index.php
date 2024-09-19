<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autoload do Composer
require __DIR__ . '/../vendor/autoload.php';


// Carregar as variáveis de ambiente do arquivo .env manualmente (para ambiente local ou de desenvolvimento)
if (file_exists(__DIR__ . '/.env')) {
    // Função para carregar o arquivo .env manualmente
    function loadEnv($path) {
        if (!file_exists($path)) {
            throw new Exception(".env file not found");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }

    // Carregar o arquivo .env
    loadEnv(__DIR__ . '/.env');
}

// Variáveis de configuração (defina manualmente ou carregue do .env)
$gmailUsername = $_ENV['GMAIL_USERNAME'] ?? 'clanofcrows.contact@gmail.com'; // Defina manualmente ou no .env
$gmailPassword = $_ENV['GMAIL_PASSWORD'] ?? 'fgalojasnupiiber'; // Defina manualmente ou no .env

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Caminho do arquivo que armazenará os dados de contagem de e-mails
$arquivo = __DIR__ . '/contagem_emails.json';

// Função para carregar a contagem de e-mails do arquivo
function carregarContagem($arquivo) {
    if (file_exists($arquivo)) {
        $conteudo = file_get_contents($arquivo);
        return json_decode($conteudo, true);
    } else {
        // Se o arquivo não existir, começa a contagem
        return ['data' => date('Y-m-d'), 'contador' => 0];
    }
}

// Função para salvar a contagem de e-mails no arquivo
function salvarContagem($arquivo, $contagem) {
    $conteudo = json_encode($contagem);
    file_put_contents($arquivo, $conteudo);
}

// Carregar contagem atual
$contagem = carregarContagem($arquivo);

// Verificar se estamos no mesmo dia ou se a contagem precisa ser reiniciada
$dataAtual = date('Y-m-d');
if ($contagem['data'] !== $dataAtual) {
    // Se for um novo dia, reiniciar a contagem
    $contagem = ['data' => $dataAtual, 'contador' => 0];
}

// Verifica se já atingiu o limite de 40 e-mails
if ($contagem['contador'] < 40) {
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
            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $gmailUsername; // Carrega manualmente ou do .env
            $mail->Password   = $gmailPassword; // Carrega manualmente ou do .env
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Remetente e destinatário
            $mail->setFrom($email, $name);
            $mail->addAddress('clanofcrows.contact@gmail.com'); // Endereço do destinatário

            // Conteúdo do e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Nova mensagem de contato';
            $mail->Body    = "<h3>Nova mensagem de: $name</h3><p>$message</p><p>E-mail: $email</p>";
            $mail->AltBody = "Nova mensagem de: $name\n\n$message\n\nE-mail: $email";

            // Enviar o e-mail
            $mail->send();
            echo 'Mensagem enviada com sucesso!';

            // Atualizar contagem de e-mails e salvar no arquivo
            $contagem['contador'] += 1;
            salvarContagem($arquivo, $contagem);

        } catch (Exception $e) {
            echo "Erro ao enviar mensagem: {$mail->ErrorInfo}";
        }
    }
} else {
    // Se o limite diário foi atingido
    echo "Limite diário de 40 e-mails atingido. Tente novamente amanhã.";
}

?>