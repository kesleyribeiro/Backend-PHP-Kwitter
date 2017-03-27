<?php
/**
 * Created by PhpStorm.
 * User: KesleyRibeiro
 * Date: 18/Mar/17
 * Time: 19:54
 */

// Capítulo 1 - UPLOAD DO ARQUIVO

// Passo 1 - Verificar dados passados para este arquivo
if (empty($_POST["id"])) { // $_REQUEST

    $arrayRetorno["mensagem"] = "Falta alguma informação obrigatória";
    return;
}

// Passar POST via htmlencrypt e atribui para $id
$id = htmlentities($_POST["id"]); // $_REQUEST

// Passo 2 - Criar uma pasta para o usuário com o nome de seu ID
$pasta = "/Applications/XAMPP/xamppfiles/htdocs/Kwitter/ava". $id;

// Se não existir a pasta, deve criá-la
if (!file_exists($pasta)) {

    mkdir($pasta, 0777, true);
}

// Passo 3 - Mover arquivo que foi feito upload
$pasta = $pasta . "/" . basename($_FILES["arquivo"]["nome"]);

if (move_uploaded_file($_FILES["arquivo"]["tmp_name"], $pasta)) {

    $arrayRetorno["status"] = "200";
    $arrayRetorno["mensagem"] = "Upload do arquivo realizado com sucesso";
} else {
    $arrayRetorno["status"] = "300";
    $arrayRetorno["mensagem"] == "Erro ao fazer upload do arquivo";
}


// Capítulo 2 - ATUALIZANDO PATH AVA

// Passo 4 - Criar conexão
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


// Passo 5 - Salvar path para upload do arquivo no BD
$path = "http://localhost/Kwitter/ava" . $id . "/ava.jpg";
$acessar->atualizarPathImagemPerfil($path, $id);


// Passo 6 - Obter informações do novo usuário após atualização
$usuario = $acessar->selecionarUsuarioViaID($id);

$arrayRetorno["id"] = $usuario["id"];
$arrayRetorno["nomeUsuario"] = $usuario["nomeUsuario"];
$arrayRetorno["nomeCompleto"] = $usuario["nomeCompleto"];
$arrayRetorno["email"] = $usuario["email"];
$arrayRetorno["ava"] = $usuario["ava"];


// Passo 7 - Fechar conexão
$acessar->desconectar();


// Passo 8 - Dados JSON
echo json_encode($arrayRetorno);


?>