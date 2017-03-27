<?php
/**
 * Created by PhpStorm.
 * User: KesleyRibeiro
 * Date: 23/Mar/17
 * Time: 23:48
 */


// Passo 1 - Criar conexão
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

// Passo 2 - Verificar dados passados para este arquivo via app
$palavra = null;
$nomeUsuario = htmlentities($_REQUEST["nomeUsuario"]);

if (!empty($_REQUEST["palavra"])) {

    $palavra = htmlentities($_REQUEST["palavra"]);
}

// Passo 3 - Acessar a função pesquisar e obter os dados no servidor
$usuarios = $acessar->pesquisarSelecionarUsuarios($palavra, $nomeUsuario);


if (!empty($usuarios)) {
    $arrayRetorno["usuarios"] = $usuarios;

} else {
    $arrayRetorno["mensagem"] = "Erro na pesquisa da palavra";
}


// Passo 4 - Fechar conexão
$acessar->desconectar();


// Passo 5 - Dados JSON
echo json_encode($arrayRetorno);


?>