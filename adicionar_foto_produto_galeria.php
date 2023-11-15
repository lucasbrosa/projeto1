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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_galeria']) && isset($_POST['key'])) {
    // Processar o envio de arquivo do segundo formulário
    if ($_POST['key'] != '0'){
      // Diretório onde as fotos serão armazenadas
      $diretorio_destino = './assets/img/festas';

      // Obter informações sobre os arquivos
      $arquivos = $_FILES["foto_galeria"];
      

      foreach ($arquivos["tmp_name"] as $indice => $arquivo_temporario) {
        // Obter informações do arquivo atual
        $nome_arquivo = $arquivos["name"][$indice];

        //return var_dump($nome_arquivo);

        $extensao_imagem = pathinfo($nome_arquivo, PATHINFO_EXTENSION);

        // Lista de extensões permitidas
        $extensoes_permitidas = array("jpeg", "jpg", "png", "gif", "webp");

        // Verificar a extensão do arquivo
        if (!in_array($extensao_imagem, $extensoes_permitidas)) {
            $_SESSION['erro'] = 'Erro: Apenas imagens JPEG, JPG, PNG, GIF ou WebP são permitidas';
            header('Location: formulario.php?key=' . $_POST["key"]);
            exit();
        }

        // Criar um novo nome para o arquivo
        $novo_nome_arquivo = uniqid() . '.' . $extensao_imagem;

        // Mover o arquivo original para o diretório de destino
        $caminho_completo = $diretorio_destino . $novo_nome_arquivo;
        move_uploaded_file($arquivo_temporario, $caminho_completo);

        // Dados para inserção no banco de dados
        $dados = [
            'key' => gerarChaveAleatoria(),
            'key_produto' => $_POST["key"],
            'imagem' => $caminho_completo,
        ];

        // Inserir no banco de dados
        inserirProdutoFotoSQLite3($dados);
        //inserirProdutoFotoFirebase($dados);        
      }

      /*
      // Obtem o nome do arquivo
      $nome_arquivo = $_FILES["foto_galeria"]["name"];
      //Obtem o tamanho do arquivo
      $tamanho_arquivo = $_FILES["foto_galeria"]["size"];
      //Obtem o tipo do arquivo
      $tipo_arquivo = $_FILES["foto_galeria"]["type"];

      $arquivo_temporario = $_FILES["foto_galeria"]["tmp_name"];

      // Lista de extensões permitidas
      $extensoes_permitidas = array("jpeg", "jpg", "png", "gif", "webp");

      //Obtem a extensão da imagem (.jpg, .gif, ...)
      $extensao_imagem = pathinfo($nome_arquivo, PATHINFO_EXTENSION);

      //se  a extensão da imagem não estiver na lista de extensões permitidas, gera um erro informando
      //sobre as extensões permitidas
      if (!in_array($extensao_imagem, $extensoes_permitidas)) {
          $_SESSION['erro'] = 'Erro: Apenas imagens JPEG, JPG, PNG, GIF ou WebP são permitidas';
          header('Location: formulario.php?key=' . $_POST["key"]);
          exit();
      }

      // Crie um novo nome para o arquivo
      $novo_nome_arquivo = uniqid() . '.' . $extensao_imagem;

      // Mova o arquivo original para o diretório de destino
      $caminho_completo = $diretorio_destino . $novo_nome_arquivo;
      move_uploaded_file($arquivo_temporario, $caminho_completo);

      $dados = [
        'key' => gerarChaveAleatoria(),
        'key_produto' => $_POST["key"],
        'imagem' => $caminho_completo,
      ];

      inserirProdutoFotoSQLite3($dados);
      inserirProdutoFotoFirebase($dados);
      */

      // Redirecione de volta para a página de gerenciamento de fotos com uma mensagem de sucesso
      $_SESSION['sucesso'] = 'Imagem salva com sucesso!';
      header('Location: formulario.php?key='.$_POST["key"]);
      exit();
    }else{
      $_SESSION['sucesso'] = 'Crie primeiro o produto!';
      header('Location: formulario.php?key='.$_POST["key"]);
      exit();
    }

    // Exemplo de manipulação do arquivo:
    // move_uploaded_file($arquivo['tmp_name'], 'caminho/para/destino/'.$arquivo['name']);
}





?>