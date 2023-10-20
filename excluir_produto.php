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
include 'MinhaAPI.php';

// Crie uma instância da classe MinhaAPI com a URL da API e o token
$api = new FirebaseAPI();


if (isset($_GET['key'])) {

  $key_produto = $_GET['key'];

  $firebase_produtos = $api->get('produtos'); 
  $firebase_imagens = $api->get_equal_to('produtos_fotos','key_produto',$key_produto); 
  
  $lista_imagens = array();
  $lista_keys_imagens = array();
  
  //deleta dados do produto do firebase
  $api->delete("produtos/".$key_produto, $_SESSION['usuario_token']);
  
  //adiciona os dados na lista de imagens e lista de keys
  foreach($firebase_imagens as $key => $value){
    array_push($lista_imagens, $value["imagem"]);
    array_push($lista_imagens,$key);
  }
  
  //itera sobre o array lista_imagens
  foreach( $lista_imagens as $value){
    //se imagem existir no diretorio
    if(file_exists($value)){
      //exclui o arquivo do diretorio
      unlink($value);
    }
  }
  
  //itera sobre o array lista_imagens
  foreach( $lista_keys_imagens as  $value){
    //deleta dados da imagem do firebase
    $api->delete("produtos_fotos/".$value, $_SESSION['usuario_token']);
  }

  $_SESSION['sucesso'] = 'Festa excluída com sucesso!';
  header('Location: index_admin.php');
  exit();

}else{
  $_SESSION['sucesso'] = 'Erro ao excluir a festa!';
  header('Location: formulario.php?key='.$_GET['key']);
  exit();  
}


?>