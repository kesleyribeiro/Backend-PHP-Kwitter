<?php
/**
 * Created by PhpStorm.
 * User: KesleyRibeiro
 * Date: 14/Mar/17
 * Time: 14:35
 */

// Passo 1 - Verificar variáveis passando para este arquivo via POST
if (empty($_REQUEST["nomeUsuario"]) || empty($_REQUEST["senha"])) {

    $arrayRetorno["status"] = "400";
    $arrayRetorno["mensagem"] = "Falta alguma informação obrigatória";
    echo json_encode($arrayRetorno);
    return;
}

$nomeUsuario = htmlentities($_REQUEST["nomeUsuario"]);
$senha = htmlentities($_REQUEST["senha"]);

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


// Passo 3 - Obter informações do usuário
// Atribui o resultado da execução para var $usuario
$usuario = $acessar->obterUsuario($nomeUsuario);

// Se não conseguirmos obter informações do usuário
if (empty($usuario)) {

    $arrayRetorno["status"] = "403";
    $arrayRetorno["mensagem"] = "Usuário não encontrado";
    echo json_encode($arrayRetorno);
    return;
}


// Passo 4 - Verificar validação da senha informada
// Obter a senha e salt do BD
$senha_segura = $usuario["senha"];
$salt = $usuario["salt"];


// Verificar a combinação de senha a partir do BD e da senha informada
if ($senha_segura == sha1($senha . $salt)) {

    $arrayRetorno["status"] = "200";
    $arrayRetorno["mensagem"] = "Login realizado com sucesso";
    $arrayRetorno["id"] = $usuario["id"];
    $arrayRetorno["nomeUsuario"] = $usuario["nomeUsuario"];
    $arrayRetorno["email"] = $usuario["email"];
    $arrayRetorno["nomeCompleto"] = $usuario["nomeCompleto"];
    $arrayRetorno["ava"] = $usuario["ava"];
    echo json_encode($arrayRetorno);
    return;

} else {
    $arrayRetorno["status"] = "403";
    $arrayRetorno["mensagem"] = "Combinação errada da senha";
    echo json_encode($arrayRetorno);
    return;
}

// Passo 5 - Fechar conexão
$acessar->desconectar();


// Passo 6 - Apresentar os dados em JSON
echo json_encode($arrayRetorno);


?>