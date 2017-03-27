<?php
/**
 * Created by PhpStorm.
 * User: KesleyRibeiro
 * Date: 12/Mar/17
 * Time: 17:02
 */

// Declaração da classe de acesso deste arquivo php
class acessar {

    var $host = null;
    var $usuario = null;
    var $senha = null;
    var $nome = null;
    var $conexao = null;
    var $resultado = null;

    // Construtor da classe
    function __construct($hostBD, $usuarioBD, $senhaBD, $nomeBD) {

        $this->host = $hostBD;
        $this->usuario = $usuarioBD;
        $this->senha = $senhaBD;
        $this->nome = $nomeBD;
    }

    // Abrir a conexão com o BD
    public function conectar() {

        // Estabelecendo a conexão
        $this->conexao = new mysqli($this->host, $this->usuario, $this->senha, $this->nome);

        // Se ocorreu um erro
        if (mysqli_connect_errno()) {
            echo "Não foi possível conectar ao BD.";
        } else {
            echo "Conectado e BD selecionado com sucesso!";
        }

        // Suporte a todas as línguas
        $this->conexao->set_charset("utf8");
    }

    // Fechar coneção com o BD
    public function desconectar() {

        // Se tiver conexão aberta
        if ($this->conexao != null) {

            // Fechar conexão
            $this->conexao->close();
        }
    }

    // Inserir Usuario ao BD
    public function registrarUsuario($nomeUsuario, $senha, $salt, $email, $nomeCompleto) {

        // Script SQL para inserir dados do usuário
        $sqlInserir = "INSERT INTO usuarios SET nomeUsuario=?, senha=?, salt=?, email=?, nomeCompleto=?";

        // Query com o resultado em $statement
        $statement = $this->conexao->prepare($sqlInserir);

        // Ocorreu um erro
        if (!$statement) {
            throw new Exception($statement->error);
        }

        // 5 parâmetros do tipo String para ser os campos do script SQL
        $statement->bind_param("sssss", $nomeUsuario, $senha, $salt, $email, $nomeCompleto);

        // Enviar / executar e dados em $valorRetorno
        $valorRetorno = $statement->execute();

        return $valorRetorno;
    }

    // Selecionar informações do usuário
    public function selecionarUsuario($nomeUsuario) {

        // Declara o array para guardar todas as informações necessárias
        $arrayRetorno = array();

        // Script SQL para selecionar dados do usuário
        $sqlSelecionar = "SELECT * FROM usuarios WHERE nomeUsuario ='".$nomeUsuario."'";

        // Atribuir ao resultado o resultado da execução
        $resultado = $this->conexao->query($sqlSelecionar);

        // Se tivermos no mínimo uma linha com o resultado retornado
        if ($resultado != null && (mysqli_num_rows($resultado) >= 1)) {

            // Atribuir resultado para a linha como array
            $linha = $resultado->fetch_array(MYSQLI_ASSOC);

            // Se linha não estiver vazia, $arrayRetorno recebe o conteúdo da linha
            if (!empty($linha)) {
                $arrayRetorno = $linha;
            }
        }
        return $arrayRetorno;
    }


    // Salvar mensagem token da confirmação de email
    public function salvarToken($tabela, $id, $token) {

        // Script SQL para inserir
        $sqlInserir = "INSERT INTO $tabela SET id=?, token=?";

        // Prepara o statement para ser executado
        $statement = $this->conexao->prepare($sqlInserir);

        // Ocorreu um erro
        if (!$statement) {
            throw new Exception($statement->error);
        }

        // Parâmetros para o script SQL
        $statement->bind_param("is", $id, $token);

        // Enviar / executar e dados em $valorRetorno
        $arrayRetorno = $statement->execute();

        return $arrayRetorno;
    }

    // Obtém id do usuário via $tokenEmail se ele receber via $_GET email
    function obterIdUsuario($tabela, $token) {

        // Declara o array para guardar todas as informações necessárias
        $arrayRetorno = array();

        // Script SQL
        $sqlObter = "SELECT id FROM $tabelaWHERE token = '".$token."'";

        // Atribuir ao resultado o resultado da execução
        $resultado = $this->conexao->query($sqlObter);

        // Se $resultado não estiver vazio, guarda o indíce
        if ($resultado != null && (mysqli_num_rows($resultado) >= 1)) {

            // Converte o índice do $resultado para o array assoc da linha
            $linha = $resultado->fetch_array(MYSQLI_ASSOC);

            // Se linha não estiver vazia, $arrayRetorno recebe o conteúdo da linha
            if (!empty($linha)) {
                $valorRetorno = $linha;
            }
        }
        return $arrayRetorno;
    }


    // Mudar o status da coluna emailConfirmado
    function statusEmailConfirmado($status, $id) {

        $sqlAlterar = "UPDATE usuarios SET emailConfirmado=? WHERE id=?";

        // Prepara o statement para ser executado
        $statement = $this->conexao->prepare($sqlAlterar);

        // Ocorreu um erro
        if(!$statement) {
            throw new Exception($statement->error);
        }

        $statement->bind_param("ii", $status, $id);

        // Enviar / executar e dados armazenados em $valorRetorno
        $arrayRetorno = $statement->execute();

        return $arrayRetorno;
    }

    // Apagar token quando email é confirmado
    function apagarToken($tabela, $token) {

        // Script SQL para apagar token no BD
        $sqlApagar = "DELETE FROM $tabela WHERE token=?";

        // Prepara o statement para ser executado
        $statement = $this->conexao->prepare($sqlApagar);

        // Se ocorreu um erro
        if(!$statement) {
            throw new Exception($statement->error);
        }

        $statement->bind_param("s", $token);

        $arrayRetorno = $statement->execute();

        return $arrayRetorno;
    }

    // Obter informações do usuário
    public function obterUsuario($nomeUsuario) {

        // Declaro o array para guardar todas as informações que precisamos
        $arrayRetorno = array();

        // Script SQL para selecionar o usuário
        $sqlObter = "SELECT * FROM usuarios WHERE nomeUsuario='".$nomeUsuario."'";

        // Atribuir ao resultado o resultado da execução
        $resultado = $this->conexao->query($sqlObter);

        // Se tivermos no mínimo uma linha com o resultado retornado
        if ($resultado != null && (mysqli_num_rows($resultado) >= 1)) {

            // Atribuir resultado para a linha como array
            $linha = $resultado->fetch_array(MYSQLI_ASSOC);

            // Se linha não estiver vazia, $arrayRetorno recebe o conteúdo da linha
            if (!empty($linha)) {
                $arrayRetorno = $linha;
            }
        }
        return $arrayRetorno;
    }

    // Selecionar informações do usuário
    public function selecionarUsuarioViaEmail($email) {

        // Declaro o array para guardar todas as informações que precisamos
        $arrayRetorno = array();

        // Script SQL para selecionar email do usuário
        $sqlSelecionarEmail = "SELECT * FROM usuarios WHERE email ='".$email."'";

        // Atribuir ao resultado o resultado da execução
        $resultado = $this->conexao->query($sqlSelecionarEmail);

        // Se tivermos no mínimo uma linha com o resultado retornado
        if ($resultado != null && (mysqli_num_rows($resultado) >= 1)) {

            // Atribuir resultado para a linha como array
            $linha = $resultado->fetch_array(MYSQLI_ASSOC);

            // Se linha não estiver vazia, $arrayRetorno recebe o conteúdo da linha
            if (!empty($linha)) {
                $arrayRetorno = $linha;
            }
        }
        return $arrayRetorno;
    }

    // Atualizar senha do usuário via link enviado no email para redefinir senha
    public function atualizarSenha($id, $senha, $salt) {

        // Script SQL para atualizar a senha do usuário no BD
        $sqlAtualizar = "UPDATE usuarios SET senha=?, salt=? WHERE id=?";

        // Prepara o statement para ser executado
        $statement = $this->conexao->prepare($sqlAtualizar);

        // Se ocorreu um erro
        if(!$statement) {
            throw new Exception($statement->error);
        }

        // parâmetro bind para sql statement
        $statement->bind_param("ssi", $senha, $salt, $id);

        // Enviar / executar e dados armazenados em $valorRetorno
        $arrayRetorno = $statement->execute();

        return $arrayRetorno;
    }


    // Atualizar path da imagem do perfil no BD
    function atualizarPathImagemPerfil($path, $id) {

        // Script SQL para atualizar ava do usuário no BD
        $sqlPath = "UPDATE usuarios SET ava=? WHERE id=?";

        // Prepara o statement para ser executado
        $statement = $this->conexao->prepare($sqlPath);

        // Se ocorreu um erro
        if (!$statement) {
            throw new Exception($statement->error);
        }

        // parâmetro bind para sql statement
        $statement->bind_param("si", $path, $id);

        // Atribui resultado da execução para $arrayRetorno
        $arrayRetorno = $statement->execute();

        return $arrayRetorno;
    }

    // Selecionar informações do usuário
    public function selecionarUsuarioViaID($id) {

        // Declaro o array para guardar todas as informações que precisamos
        $arrayRetorno = array();

        // Script SQL para selecionar id do usuário
        $sqlSelecionarId = "SELECT * FROM usuarios WHERE id ='".$id."'";

        // Atribuir ao resultado o resultado da execução
        $resultado = $this->conexao->query($sqlSelecionarId);

        // Se tivermos no mínimo uma linha com o resultado retornado
        if ($resultado != null && (mysqli_num_rows($resultado) >= 1)) {

            // Atribuir resultado para a linha como array
            $linha = $resultado->fetch_array(MYSQLI_ASSOC);

            // Se linha não estiver vazia, $arrayRetorno recebe o conteúdo da linha
            if (!empty($linha)) {
                $arrayRetorno = $linha;
            }
        }
        return $arrayRetorno;
    }

    // Inserir Posts no BD
    public function inserirPosts($id, $uuid, $texto, $path) {

        // Script SQL para inserir o post
        $sqlInserirPost = "INSERT INTO posts SET id=?, uuid=?, texto=?, path=?";

        // Prepara o statement para ser executado
        $statement = $this->conexao->prepare($sqlInserirPost);

        // Ocorreu um erro
        if (!$statement) {
            throw new Exception($statement->error);
        }

        // Parâmetros para o script SQL
        $statement->bind_param("isss", $id, $uuid, $texto, $path);

        // Enviar / executar e dados armazenados em $valorRetorno
        $arrayRetorno = $statement->execute();

        return $arrayRetorno;
    }


    // Selecionar no BD todos os Posts + informações do usuário através do $id
    public function selecionarPosts($id) {

        // Declaro o array para guardar todas as informações que precisamos
        $arrayRetorno = array();

        // Script SQL para selecionar posts e informações do usuário
        $sqlSelecionarPost = "SELECT posts.id,
        posts.uuid,
        posts.texto,
        posts.path,
        posts.data,
        usuarios.id,
        usuarios.nomeUsuario,
        usuarios.nomeCompleto,
        usuarios.email,
        usuarios.ava
        FROM Kwitter.posts JOIN Kwitter.usuarios ON
        posts.id = $id AND usuarios.id = $id ORDER BY data DESC";

        // Prepara o statement para ser executado
        $statement = $this->conexao->prepare($sqlSelecionarPost);

        // Ocorreu um erro
        if (!$statement) {
            throw new Exception($statement->error);
        }

        // Executa o SQL
        $statement->execute();

        // $resultado recebe a execução
        $resultado = $statement->get_result();

        // Quando se encontrar resultado adiciona uma por uma linha no $arrayRetorno
        while ($linha = $resultado->fetch_assoc()) {

            $arrayRetorno[] = $linha;
        }

        return $arrayRetorno;
    }


    // Apagar Post no BD de acordo com o uuid
    public function apagarPost($uuid) {

        // Script SQL para apagar post
        $sqlApagarPost = "DELETE FROM posts WHERE uuid=?";

        // Prepara o statement para ser executado
        $statement = $this->conexao->prepare($sqlApagarPost);

        // Ocorreu um erro
        if (!$statement) {
            throw new Exception($statement->error);
        }

        // Parâmetro para o script SQL
        $statement->bind_param("s", $uuid);

        // Executa o SQL
        $statement->execute();

        // Enviar / executar e dados armazenados em $valorRetorno
        $arrayRetorno = $statement->affected_rows;

        return $arrayRetorno;
    }


    // Pesquisar / selecionar usuário, execeto o usuário atual do app
    public function pesquisarSelecionarUsuarios($palavra, $nomeUsuario){

        // Declaro o array para guardar todas as informações que precisamos
        $arrayRetorno = array();

        // Script SQL para selecionar usuários
        $sqlSelecionarUsuarios = "SELECT id, nomeUsuario, email, nomeCompleto, ava FROM usuarios WHERE NOT nomeUsuario = '".$nomeUsuario."'";

        // Se existir alguma palavra/letra
        if (!empty($palavra)) {
            $sqlSelecionarUsuarios .= " AND ( nomeUsuario LIKE ? OR nomeCompleto LIKE ? )";
        }

        // Prepara o statement para ser executado
        $statement = $this->conexao->prepare($sqlSelecionarUsuarios);

        // Ocorreu um erro
        if (!$statement) {
            throw new Exception($statement->error);
        }

        // Se existir alguma palavra/letra
        if (!empty($palavra)) {
            $palavra = '%' . $palavra . '%'; // Ex. %kesley%

            // Parâmetro para o script SQL
            $statement->bind_param("ss", $palavra, $palavra);
        }

        // Executa o SQL
        $statement->execute();

        // Enviar / executar e dados armazenados em $valorRetorno
        $resultado = $statement->get_reslt();

        // A todo momento converter $resultado para array assoc e adiciona para $linha
        while ($linha = $resultado->fetch_assoc())  {

            // Guardar todas $linha em $arrayRetorno
            $arrayRetorno[] = $linha;
        }
        return $arrayRetorno;
    }


    // Atualizar informações do usuário no BD via $id
    public function atualizarInformacoesUsuario($nomeUsuario, $nomeCompleto, $email, $id) {

        // Script SQL para atualizar ava do usuário no BD
        $sqlAtualizar = "UPDATE usuarios SET nomeUsuario=?, nomeCompleto=?, email=? WHERE id=?";

        // Prepara o statement para ser executado
        $statement = $this->conexao->prepare($sqlAtualizar);

        // Se ocorreu um erro
        if (!$statement) {
            throw new Exception($statement->error);
        }

        // Parâmetro bind para sql statement
        $statement->bind_param("sssi", $nomeUsuario, $nomeCompleto, $email, $id);

        // Atribuir resultado da execução para $arrayRetorno
        $arrayRetorno = $statement->execute();

        return $arrayRetorno;
    }
}

?>