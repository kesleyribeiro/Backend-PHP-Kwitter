<?php
/**
 * Created by PhpStorm.
 * User: KesleyRibeiro
 * Date: 12/Mar/17
 * Time: 17:23
 */


// Passo 1 - Declaração das informações do usuário
// Se GET ou POST estiverem vazios
if (empty($_REQUEST["nomeUsuario"]) || empty($_REQUEST["senha"]) || empty($_REQUEST["email"]) || empty($_REQUEST["nomeCompleto"])) {

    $arrayRetorno["status"] = "400";
    $arrayRetorno["mensagem"] = "Falta alguma informação obrigatória";
    echo json_encode($arrayRetorno);
    return;
}

// Variáveis com informação segura
$nomeUsuario = htmlentities($_REQUEST["nomeUsuario"]);
$senha = htmlentities($_REQUEST["senha"]);
$email = htmlentities($_REQUEST["email"]);
$nomeCompleto = htmlentities($_REQUEST["nomeCompleto"]);

// Senha segura
$salt = openssl_random_pseudo_bytes(20);
$senha_segura = sha1($senha . $salt);


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


// Passo 3 - Inserir dados do usuário
$resultado = $acessar->registrarUsuario($nomeUsuario, $senha_segura, $salt, $email, $nomeCompleto);

// Registro com sucesso
if ($resultado) {

    // Começa a registrar informação do usuário atual
    $usuario = $acessar->selecionarUsuario($nomeUsuario);

    // Declara informações para feedback para o usuário do App como JSON
    $arrayRetorno["status"] = "200";
    $arrayRetorno["mensagem"] = "Sucesso ao registrar o usuario";
    $arrayRetorno["id"] = $usuario["id"];
    $arrayRetorno["nomeUsuario"] = $usuario["nomeUsuario"];
    $arrayRetorno["email"] = $usuario["email"];
    $arrayRetorno["nomeCompleto"] = $usuario["nomeCompleto"];
    $arrayRetorno["ava"] = $usuario["ava"];

    // Passo 4 - Enviar Email
    // Incluindo email.php
    require ("seguranca/email.php");

    // Cria uma variável com todas as classes de email
    $email = new email();

    // Gerar token na variável $token
    $token = $email->gerarToken(20);

    // Salvar informações na tabela 'tokensEmail'
    $acessar->salvarToken("tokensEmail", $usuario["id"], $token);

    // Consulta a informação do email
    $detalhes = array();
    $detalhes["assunto"] = "Por favor confirme seu email no Kwitter";
    $detalhes["para"] = $usuario["email"];
    $detalhes["nomeDestinatario"] = "Kesley Ribeiro";
    $detalhes["emailDestinatario"] = "kesley002@gmail.com";

    // Acessar o arquivo template
    $template = $email->templateConfirmacao();

    // Substitui {token} a partir do templateConfirmacao.html por $token e guarda todo o índice dentro da var $template
    $template = str_replace("{token}", $token, $template);

    $detalhes["corpo"] = $template;

    $email->enviarEmail($detalhes);

} else {
    $arrayRetorno["status"] = "400";
    $arrayRetorno["mensagem"] = "Falha ao registrar o usuário";
}

// Passo 5 - Fechar conexão
$acessar->desconectar();

// Passo 6 - Dados JSON
echo json_encode($arrayRetorno);

?>