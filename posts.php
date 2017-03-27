<?php
/**
 * Created by PhpStorm.
 * User: KesleyRibeiro
 * Date: 20/Mar/17
 * Time: 18:38
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


// Passo 2 - Verificar se foi passado dados para este arquivo
// Se for passado, salvar Post do usuário
if (!empty($_REQUEST["uuid"]) && !empty($_REQUEST["texto"])) {

    // Passo 2.1 - Passar POST / GET via criptografia html e atribuir para variaveis
    $id = htmlentities($_REQUEST["id"]);
    $uuid = htmlentities($_REQUEST["uuid"]);
    $texto = htmlentities($_REQUEST["texto"]);

    // Passo 2.2 - Criar a pasta no servidor para as imagens Postadas
    $pasta = "/Applications/XAMPP/xamppfiles/htdocs/Kwitter/posts" . $id;

    // Se não existir a pasta, deve criá-la
    if (!file_exists($pasta)) {
        mkdir($pasta, 0777, true);
    }


    // Passo 2.3 - Mover arquivo que foi feito upload
    $pasta = $pasta . "/" . basename($_FILES["arquivo"]["nome"]);

    if (move_uploaded_file($_FILES["arquivo"]["tmp_name"], $pasta)) {

        $arrayRetorno["mensagem"] = "Post realizado com a imagem";
        $path = "http://localhost/Kwitter/posts/" . $id . "/post-" . $uuid . ".jpg"; // Exemplo....Kwitter/posts/5/post-12412757.jpg
    } else {
        $arrayRetorno["mensagem"] = "Post realizado sem a imagem";
        $path = "";
    }

    // Passo 2.4 - Salvar o path e outros detalhes do Post no BD
    $acessar->inserirPosts($id, $uuid, $texto, $path);

} // Se o id do usuário não é passado, mas o uuid do post é passado, apagar post
else if (!empty($_REQUEST["uuid"]) && empty($_REQUEST["id"])) {

    // Passo 2.1 - Obter uuid do Post e path da imagem passada via Swift para este arquivo PHP
    $uuid = htmlentities($_REQUEST["uuid"]);
    $path = htmlentities($_REQUEST["path"]);

    // Passo 2.2 - Apagar Post de acordo com o uuid
    $resultado = $acessar->apagarPost($uuid);

    // Se houver algum valor na variável $resultado
    if (!empty($resultado)) {

        $arrayRetorno["mensagem"] = "Post apagado com sucesso";
        $arrayRetorno["resultado"] = $resultado;

        // Passo 2.3 - Se existir este $path, apagar o arquivo
        if (!empty($path)) {

            // Ex: /Applications/XAMPP/xamppfiles/htdocs/Kwitter/posts/5/imagem.jpg
            $path = str_replace("http://localhost/", "/Applications/XAMPP/xamppfiles/htdocs/", $path);

            // Sucesso ao apagar arquivo
            if (unlink($path)) {
                $arrayRetorno["status"] = "1000";

            } // Erro ao apagar arquivo
            else {
                $arrayRetorno["status"] = "400";
            }
        }
    } else {
        $arrayRetorno["mensagem"] = "Erro ao apagar o post";
    }
} // Se os dados não forem passados - mostrar posts
else {

    // Passo 2.1 - Passar POSt / GET via criptografia html e atribuir o id do usuário para var. $id
    $id = htmlentities($_REQUEST["id"]);

    // Passo 2.2 - Selecionar Posts + usuário relacionado ao $id
    $posts = $acessar->selecionarPosts($id);

    // Passo 2.3 - Se Posts forem encontrados, então adicionar para o $arrayRetorno
    if (!empty($posts)) {
        $arrayRetorno["posts"] = $posts;
    }
}


// Passo 3 - Fechar conexão
$acessar->desconectar();


// Passo 4 - Apresentar todas as informações ao usuário
echo json_encode($arrayRetorno);

?>