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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['key_foto_galeria']) && isset($_GET['caminho_foto']) && isset($_GET['key_produto'])){
  // Recupere o valor do parâmetro "id" da URL
  $key_foto = $_GET['key_foto_galeria'];
  $key_produto = $_GET['key_produto'];
  $caminho_foto = $_GET['caminho_foto']; 

  $resultado_imagem_capa = obterImagemCapaPorKeyProdutoSQLite3($key_produto);

  if(count($resultado_imagem_capa)> 0){
    foreach ($resultado_imagem_capa as $key => $imagem_capa) {
      if($imagem_capa['key_imagem'] == $key_foto){
        removerImagemCapaSQLite3($key_foto);
      }
    }
  }

  //busca as imanges de capa cadastradas para verificar se a imagem exlcuida esta nos resultados.
  removerProdutoFotoSQLite3($key_foto);
  //removerProdutoFotoFirebase($key_foto);

  //$api->delete('produtos_fotos/'. $key_foto, $_SESSION['usuario_token']);

  // Verifica se a imagem existe no diretório.
  if (file_exists($caminho_foto)) {
    //Se sim, exclui a foto
    unlink($caminho_foto);
  }    

  //Redireciona para a página "formulario_fotos.php" com a mensagem de sucesso.
  $_SESSION['sucesso'] = 'A imagem de capa foi excluída com sucesso!';
  header('Location: formulario.php?key='. $key_produto);
  exit();
}


?>