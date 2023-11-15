<?php
// Inicie ou retome a sessão
session_start();

// Verifique se a variável de sessão "usuario_logado" está definida e é verdadeira
if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    // Se o usuário não estiver logado, redirecione para a página de login.php
    header('Location: login.php');
    exit; // Certifique-se de sair do script após redirecionar
}

// Inclua o arquivo que contém a definição da classe MinhaAPI
include 'model.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['imagem_principal']) && isset($_POST['key'])) {

  if($_POST['imagem_principal'] != '0'){

    //cria as variaveis e obtem os dados do formulário
    $key_produto = $_POST['key'];
    $key_imagem = $_POST['imagem_principal'];

    try {
      $dados = [
        'key_produto' => $key_produto,
        'key_imagem' => $key_imagem
      ];

      //verificar se contém alguma imagem de capa configurada para o produto
      $resultado = obterImagemCapaPorKeyProdutoSQLite3($key_produto);

      //return var_dump(count($resultado));

      //se não tiver cria um registro no bando de dados informado a imagem
      if (count($resultado)==0){      

        inserirImagemCapaSQLite3($dados);
      }
      //se tiver, apenas atualiza a imagem.
      else{
        $dados = [
          'key_produto' => $key_produto,
          'key_imagem' => $key_imagem
        ];

        atualizarImagemCapaSQLite3($dados);
      }

      // Redirecione de volta para a página de gerenciamento de fotos com uma mensagem de sucesso
      $_SESSION['sucesso'] = 'Imagem de capa configurada com sucesso!';
      header('Location: formulario.php?key='.$_POST["key"]);
      exit();

    } catch (Exception $e) {
      $_SESSION['erro'] = 'Erro ao configurar a imagem de capa!';
      header('Location: formulario.php?key='.$_POST["key"]);
      exit();
    }

  }else {
    $_SESSION['erro'] = 'Selecione uma imagem para poder configurar a imagem de capa!';
    header('Location: formulario.php?key='.$_POST["key"]);
    exit();
  }

  

}
?>