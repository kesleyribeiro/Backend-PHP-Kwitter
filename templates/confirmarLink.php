<?php
/**
 * Created by PhpStorm.
 * User: KesleyRibeiro
 * Date: 13/Mar/17
 * Time: 17:19
 */

// Passo 1 - Verificar solicitação e informação passada
if (empty($_GET["token"])) {

    echo "Falha ao solicitar informação.";
    return;
}

$token = htmlentities($_GET["token"]);

// Passo 2 - Criar conexão
// Caminho seguro para criar conexão
$arquivo = parse_ini_file("../../../../Kwitter.ini");

// Variáveis com informações a partir do .ini
$host = trim($arquivo["hostBD"]);
$usuario = trim($arquivo["usuarioBD"]);
$senha = trim($arquivo["senhaBD"]);
$nome = trim($arquivo["nomeBD"]);

// Inclui o arquivo para chamar a função do arquivo
require("../seguranca/acessar.php");
$acessar = new acessar($host, $usuario, $senha, $nome);
$acessar->conectar();

// Passo 3 - Obtém id do usuário
// guarda no id o resultado da função
$id = $acessar->obterIdUsuario("tokensEmail", $token);

if (empty($id["id"])) {

    echo 'Usuário com o token não encontrado.';
    return;
}


// Passo 4 - Mudar o status de confirmação e apagar o token
// Atribui o resultado da função para var $resultado
$resultado = $acessar->statusEmailConfirmado(1, $id["id"]);

if ($resultado) {

    // 4.1 - Apagar token na tabela 'tokensEmail' do BD
    $acessar->apagarToken("tokensEmail", $token);

    echo "Obrigado por confirmar o seu email.";
}

// Passo 5 - Fechar conexão
$acessar->desconectar();

?>