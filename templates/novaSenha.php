<?php
/**
 * Created by PhpStorm.
 * User: KesleyRibeiro
 * Date: 14/Mar/17
 * Time: 18:34
 */

// Segunda página carregada

// Passo 1 - Verificar informações que foram passadas
if (!empty($_POST["senha_1"]) && !empty($_POST["senha_2"]) && !empty($_POST["token"])) {

    $senha_1 = htmlentities($_POST["senha_1"]);
    $senha_2 = htmlentities($_POST["senha_2"]);
    $token = htmlentities($_POST["token"]);

    // Passo 2 - Verificar se as senhas combinam ou não
    if ($senha_1 == $senha_2) {

        // Passo 3 - Criar conexão
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

        // Passo 4 - Obter id do usuário via token do usuário
        $usuario = $acessar->obterIdUsuario("tokensSenha", $token);

        // Passo 5 - Atualizar BD
        if (!empty($usuario)) {

            // Passo 5.1 - Gerar senha segura
            $salt = openssl_random_pseudo_bytes(20);
            $senha_segura = sha1($senha_1 . $salt);

            // Passo 5.2 - Atualizar senha do usuário
            $resultado = $acessar->atualizarSenha($usuario["id"], $senha_segura, $salt);

            if ($resultado) {

                // Pasoo 5.3 - Apagar token único
                $acessar->apagarToken("tokensSenha", $token);

                $mensagem = "Nova senha criada com sucesso.";

                header("Location:didRedefinirSenha.php?mensagem=" . $mensagem);

            } else {

                echo "ID do usuário está vazio.";
            }
        }
    } else {
        $mensagem = "Senha não está combinando corretamente";
    }
}



?>

<!-- Primeira página carregada -->
<html>
    <head>
        <title>Criar nova senha</title>

        <!-- CSS -->
        <style>
            .password_field {
                margin: 10px;
            }

            .button {
                margin: 10px;
            }
        </style>
    </head>
        <body>
            <h1>Criar nova senha</h1>

            <?php

                if (!empty($mensagem)) {
                    echo "</br>" . $mensagem . "</br>";
                }
            ?>

            <!-- Formulário -->
            <form method="POST" action="<?php $_SERVER['PHP_SELF'];?>">
                <div><input type="password" name="senha_1" placeholder="Nova senha:" class="password_field"/></div>
                <div><input type="password" name="senha_2" placeholder="Confirmar nova senha:" class="password_field"/></div>
                <div><input type="submit" value="Salvar senha" class="button"/></div>
                <input type="hidden" value="<?php echo $_GET['token'];?>" name="token">
            </form>

        </body>
</html>
