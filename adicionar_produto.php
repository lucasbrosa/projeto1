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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['key']) && isset($_POST['nome']) && isset($_POST['descricao']) && isset($_POST['preco'])) {
    // Processar os dados do primeiro formulário
    $key = $_POST['key'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];

    // Valide os dados do formulário (por exemplo, verifique se os campos estão preenchidos)
    $dados = [
      'nome' => $nome,
      'preco' => $preco,
      'descricao' => $descricao,
    ];

    // Salva ou atualiza os dados no Banco de dados.
    if($key == '0'){

      $dados['key'] = gerarChaveAleatoria();

      inserirProdutoSQLite3($dados);
      //inserirProdutoFirebase($dados);

      $resposta = true;
      if ($resposta) {
          $_SESSION['sucesso'] = 'Festa adicionada com sucesso!';
          header('Location: formulario.php?key='.$dados['key']);
          exit();
      } else {
          $_SESSION['erro'] = 'Erro ao adicionar a festa.';
          header('Location: formulario.php?key='.$dados['key']);
          exit();
      }
    }else{

      $dados['key'] = $key;

      atualizarProdutoSQLite3($dados);
      //atualizarProdutoFirebase($dados);

      $resposta = true;
      if ($resposta) {
          $_SESSION['sucesso'] = 'Festa atualizada com sucesso!';
          header('Location: formulario.php?key='.$dados['key']);
          exit();
      } else {        
          $_SESSION['erro'] = 'Erro ao atualizar a festa.';
          header('Location: formulario.php?key='.$dados['key']);
          exit();
      }
    }      
}


?>