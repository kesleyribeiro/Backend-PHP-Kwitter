<?php
/**
 * Created by PhpStorm.
 * User: KesleyRibeiro
 * Date: 13/Mar/17
 * Time: 17:24
 */

class email {

    // Gerar um unico token para o usuário quando ele confirmar o email
    function gerarToken($tamanho) {

        // Alguns caracteres
        $caracteres = "dfgqbnmQWERTYUIOPASDFGHwertyuiZXCopashjklzxcvJKLVBNM1489527360";

        // Obtém o tamanho de caracteres
        $tamanhoCaracteres = strlen($caracteres);

        $token = '';

        // A todo momento gera até estiver menos que $tamanhoCaracteres
        for($i = 0; $i < $tamanho; $i++) {

            $token .= $caracteres[rand(0, $tamanhoCaracteres-1)];
        }
        return $token;
    }

    // Abrir o template de confirmação do usuário
    function templateConfirmacao() {

        // Acessa o arquivo
        $arquivo = fopen("templates/templateConfirmacao.html", "r") or die("Não foi possível abrir o arquivo.");

        // Variável que contém o arquivo do template
        $template = fread($arquivo, filesize("templates/templateConfirmacao.html"));

        // Fecha o arquivo
        fclose($arquivo);

        return $template;
    }

    // Abrir o template de redefinir a senha
    function templateRedefinirSenha() {

        // Acessa o arquivo
        $arquivo = fopen("templates/templateRedefinirSenha.html", "r") or die("Não foi possível abrir o arquivo.");

        // Variável que contém o arquivo do template
        $template = fread($arquivo, filesize("templates/templateRedefinirSenha.html"));

        // Fecha o arquivo
        fclose($arquivo);

        return $template;
    }

    // Enviar email com PHP
    function enviarEmail($detalhes) {

        // Informações do Email
        $assunto = $detalhes["assunto"];
        $para = $detalhes["para"];
        $nomeDestinatario = $detalhes["nomeDestinatario"];
        $emailDestinatario = $detalhes["emailDestinatario"];
        $corpo = $detalhes["corpo"];

        // Cabeçalho
        $cabecalho = "MIME-Version: 1.0" . "\r\n";
        $cabecalho .= "Content-type:text/html;content=UTF-8" . "\r\n";
        $cabecalho .= "De: " . $nomeDestinatario . " <" . $emailDestinatario . ">" . "\r\n"; // Ex. De: Kesley <kesley002@gmail.com>

        // Função PHP para enviar email final
        mail($para, $assunto, $corpo, $cabecalho);
    }
}



?>