<?php
/**
 * Created by PhpStorm.
 * User: KesleyRibeiro
 * Date: 24/Mar/17
 * Time: 15:42
 */


$arrayRetorno = array();

// Passo 1 - Verificar variáveis passadas
if (empty($_REQUEST["nomeUsuario"]) && empty($_REQUEST["nomeCompleto"]) && empty($_REQUEST["email"]) && empty($_REQUEST["id"])) {

    $arrayRetorno["status"] = "400";
    $arrayRetorno["mensagem"] = "Falta alguma informação obrigatória";
    return;
}

// Variáveis com informação segura
$nomeUsuario = htmlentities($_REQUEST["nomeUsuario"]);
$nomeCompleto = htmlentities($_REQUEST["nomeCompleto"]);
$email= htmlentities($_REQUEST["email"]);
$id= htmlentities($_REQUEST["id"]);


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


// Passo 3 - Atualizar informações do usuário
$resultado = $acessar->atualizarInformacoesUsuario($nomeUsuario, $nomeCompleto, $email, $id);


if (!empty($resultado)) {

    // Passo 4 - Nova atualização nas informações do usuário
    $usuario = $acessar->selecionarUsuarioViaID($id);

    $arrayRetorno["id"] = $usuario["id"];
    $arrayRetorno["nomeUsuario"] = $usuario["nomeUsuario"];
    $arrayRetorno["nomeCompleto"] = $usuario["nomeCompleto"];
    $arrayRetorno["email"] = $usuario["email"];
    $arrayRetorno["ava"] = $usuario["ava"];
    $arrayRetorno["status"] = ["200"];
    $arrayRetorno["mensagem"] = ["Sucesso ao atualizar informações do usuário"];

} else {
    $arrayRetorno["status"] = "400";
    $arrayRetorno["mensagem"] = "Erro ao atualizar informações do usuário";
}

// Passo 5 - Fechar conexão
$acessar->desconectar();

// Passo 6 - Dados JSON
echo json_encode($arrayRetorno);


?>