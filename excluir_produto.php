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

// Crie uma instância da classe MinhaAPI com a URL da API e o token
//$api = new FirebaseAPI();


if (isset($_GET['key_produto'])) {

  $key_produto = $_GET['key_produto'];
  $lista_fotos_capa = obterProdutoFotoCapaSQLite3($key_produto);
  $lista_fotos_galeria = obterProdutoFotosSQLite3($key_produto);
  
  // ------------------------------------------------------------------------
  //1ª Etapa - Deletar a imagens da galeria do diretório e remover os dados do banco de dados
  if($lista_fotos_galeria > 0){  
    //exclui e deleta o dados do banco de dados.  
    foreach( $lista_fotos_galeria as $foto){
      //se imagem existir no diretorio
      if(file_exists($foto["imagem"])){
        //exclui o arquivo do diretorio
        unlink($foto["imagem"]);
      }
      removerImagemCapaSQLite3($foto['key']);
      //removerProdutoFotoFirebase($foto['key']); 
      removerProdutoFotoSQLite3($foto['key']);    
    }
  }
  // ------------------------------------------------------------------------
  //2ª Etapa - Deletar a imagem de capa do diretório e remover os dados do banco de dados
  if($lista_fotos_capa>0){
    //exclui e deleta o dados do banco de dados.  
    foreach($lista_fotos_capa as $foto){
      //se imagem existir no diretorio
      if(file_exists($foto["imagem"])){
        //exclui o arquivo do diretorio
        unlink($foto["imagem"]);
      }
      //removerProdutoFotoCapaFirebase($foto['key']); 
      removerProdutoFotoCapaSQLite3($foto['key']);    
    }    
  }
  // ------------------------------------------------------------------------
  //3ª Etapa - Deletar os dados do produto do banco de dados
  
  //1. Deleta dados do produto do firebase
  removerProdutoSQLite3($key_produto);
  //removerProdutoFirebase($key_produto);
  // ------------------------------------------------------------------------
  //4ª Etapa - Redirecionar e enviar uma mensagem de sucesso  

  //1. Atribui a mensagem de sucesso na variável global $_SESSION
  $_SESSION['sucesso'] = 'Festa excluída com sucesso!';
  //2. Redireciona para a página "index_admin.php"
  header('Location: index_admin.php');
  exit();

}else{
  $_SESSION['erro'] = 'Erro ao excluir a festa!';
  header('Location: index_admin.php');
  exit();  
}


?>