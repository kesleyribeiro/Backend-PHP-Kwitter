<?php
/**
 * Created by PhpStorm.
 * User: KesleyRibeiro
 * Date: 14/Mar/17
 * Time: 17:48
 */

// Passo 1 - Obter informação e passar para este arquivo
if (empty($_REQUEST["email"])) {
    
    $arrayRetorno["mensagem"] = "Falta alguma informação obrigatória.";
    echo json_encode($arrayRetorno);
    return;
}

$email = htmlentities($_REQUEST["email"]);

// Passo 2 - Criar conexão
// Caminho seguro para criar conexão
$arquivo = parse_ini_file("../../../Kwitter.ini");

// Variáveis com informações a partir do .ini
$host = trim($arquivo["hostBD"]);
$usuario = trim($arquivo["usuarioBD"]);
$senha = trim($arquivo["senhaBD"]);
$nome = trim($arquivo["nomeBD"]);

// Inclui o arquivo para chamar a função do arquivo
require("seguranca/acessar.php");
$acessar = new acessar($host, $usuario, $senha, $nome);
$acessar->conectar();

// Passo 3 - Verifica se existe o endereço de email no BD
// Guarda o resultada da função na var $usuario
$usuario = $acessar->selecionarUsuarioViaEmail($email);


// Verifica se temalguma informação na var $usuario
if (empty($usuario)) {

    $arrayRetorno["mensagem"] = "Email não encontrado";
    echo json_encode($arrayRetorno);
    return;
}

// Passo 4 - Enviar email
// Incluindo email.php
require ("seguranca/email.php");

// Cria uma variável com todas as classes de email
$email = new email();

// Gerar uma única string token no BD
$token = $email->gerarToken(20);

// Guarda o token no BD
$acessar->salvarToken("tokensSenha", $usuario["id"], $token);

// Preparar mensagem do email
$detalhes = array();
$detalhes["assunto"] = "Redefinir senha no Kwitter";
$detalhes["para"] = $usuario["email"];
$detalhes["nomeDestinatario"] = "Kesley Ribeiro";
$detalhes["emailDestinatario"] = "kesley002@gmail.com";

// Carrega o template html
$template = $email->templateRedefinirSenha();
$template = str_replace("{token}", $token, $template);
$detalhes["body"] = $template;

// Enviar email para usuário
$email->enviarEmail($detalhes);


// Passo 5 - Retornar mensagem ao usuário do App
$arrayRetorno["email"] = $usuario["email"];
$arrayRetorno["mensagem"] = "Nós te enviamos um email para redefir a sua senha.";
echo json_encode($arrayRetorno);


// Passo 6 - Fechar conexão
$acessar->desconectar();

?>