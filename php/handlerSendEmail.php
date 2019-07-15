<?php
//Validações dos dados de entrada
if(!isset($_POST['submit']))
{
  echo "<p>Erro de permissão, essa página não pode ser acessada diretamente.</p>";
  exit;
}
// Função para validar contra mail injection
function IsInjected($str)
{
  $injections = array('(\n+)',
    '(\r+)',
    '(\t+)',
    '(%0A+)',
    '(%0D+)',
    '(%08+)',
    '(%09+)'
  );
  $inject = join('|', $injections);
  $inject = "/$inject/i";
  if(preg_match($inject,$str))
  {
    return true;
  }
  else
  {
    return false;
  }
}


//Recebe os dados do POST
$name = $_POST['nome'];
$visitor_email = $_POST['email'];
$phone = $_POST['telefone'];
$subject = $_POST['assunto'];
$message = $_POST['mensagem'];
$origin = $_POST['origem'];
$msgRetorno = [];

//Valida os campos obrigatórios do formulário
if(empty($name)||empty($visitor_email)||empty($phone)||empty($subject)||empty($message))
{
  $msgRetorno = [
    'titulo'=>"Campos obrigatórios não foram preenchidos",
    'mensagem'=>"Confira se digitou seu Nome, Email, Telefone e Mensagem",
    'tipo'=>"erro"
  ];
  echo json_encode($msgRetorno);
  exit;
}


require_once ($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php') ;
//require_once ('/var/www/html/galileosoft.com.br/site-maonaroda/vendor/autoload.php') ;
//require_once ('/var/www/html/site-mcfr/vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;


$mailer = new PHPMailer();
//Login no servidor SMTP
$mailer->IsSMTP();
$mailer->Host       = "smtp.galileosoft.com.br";
$mailer->SMTPAuth   = true;
$mailer->Port = 587;
$mailer->SMTPSecure = false;
$mailer->SMTPAutoTLS = false;
$mailer->Username   = 'site@mcfr.com.br';
$mailer->Password   = 'SMTP!01';
//Pessoa que ENVIA o email
$mailer->Sender = "site@mcfr.com.br"; //Email que envia
$mailer->From = "site@mcfr.com.br"; //Email que aparece pra quem recebe
$mailer->FromName = "Site - MCFR"; //Nome que aparece pra quem recebe
//Pessoa que RECEBE o email
//if ($origin)
$mailer->addAddress('contato@galileosoft.com.br');
//Escreve o Email
$mailer->CharSet = 'UTF-8';
$mailer->isHTML(true);
$mailer->Subject = "Novo Contato Site - Assunto: ".$subject;
$mailer->Body = "<p style='text-align: center;font-size: 12px;text-transform: uppercase;'><strong>Novo Contato Realizado no Site</strong></p>
                    <hr>
                    <p style='text-align: center'><strong>Dados Pessoais</strong></p>
                    <p><strong>Nome: </strong>".$name."</p>
                    <p><strong>Telefone: </strong>".$phone."</p>
                    <p><strong>Email: </strong>".$visitor_email."</p>
                    <p><strong>Assunto: </strong>".$subject."</p>
                    <p><strong>Mensagem: </strong>".$message."</p>
                    <p><strong>Origem: </strong>".$origin."</p>
                    <hr>";

$enviado = $mailer->send();
$mailer->ClearAllRecipients();


if (!$enviado){
  $msgRetorno = ['titulo'=>"Pedimos desculpa, mas correu um erro durante o envio do seu contato, por favor tente novamente!",
                 'mensagem'=>"Caso o problema persista, entre em contato via WhatsApp ou Ligação e estaremos de prontidão para lhe ajudar",
                 'tipo'=>"erro"];
  echo json_encode($msgRetorno);
  exit;
}else{
  $msgRetorno = ['titulo'=>"Contato enviado com Sucesso!",
                 'mensagem'=>"A equipe MCFR agradece pelo seu contato, responderemos sua solicitação em breve",
                 'tipo'=>"sucesso"];
  echo json_encode($msgRetorno);
  exit;
}

//Debug
//$mailer->SMTPDebug = 4;
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);


