<?php
// Inicie ou retome a sessão
session_start();

// Inclua o arquivo que contém a definição da classe MinhaAPI
include 'MinhaAPI.php';
// Inclua o arquivo que contém as preferencias do site.
include './config/preferencias.php'; 

// Crie uma instância da classe MinhaAPI com a URL da API e o token
$api = new FirebaseAPI();

$lista_festas = [];


// Verifique se o formulário de pesquisa foi enviado
if (isset($_POST['pesquisar'])) {
    // Processar a pesquisa
    $termo_pesquisa = $_POST['termo_pesquisa'];
    // Use a classe FirebaseAPI ou outra classe para buscar os resultados
    $resultado_busca_produtos = $api->get('produtos');
    $resultado_busca_produtos_fotos = $api->get('produtos_fotos');
    
    // Iterar pelos registros para encontrar correspondências
    foreach ($resultado_busca_produtos as $key => $produto) {
        if (strpos(strtolower($produto['nome']), strtolower($termo_pesquisa)) !== false) {
            $dados_festa = [];
            $dados_festa['key'] = $key;
            $dados_festa['nome'] = $produto['nome'];

            foreach ($resultado_busca_produtos_fotos as $key => $foto) {
              if($foto['key_produto'] == $dados_festa['key']){
                $dados_festa['imagem'] = $foto['imagem'];
                break;
              }else{
                $dados_festa['imagem'] = '';
              }
            }
          
            $lista_festas[] = $dados_festa;
        }
    }  
  
} else {
    // A pesquisa não foi realizada, mostrar todos os produtos
    $resultado_busca = $api->get('produtos');
    $resultado_busca_produtos_fotos = $api->get('produtos_fotos');

    // Agora $categories contém todos os registros          
    
    
    // Iterar pelos registros para encontrar correspondências
    foreach ($resultado_busca as $key => $produto) {        
        $dados_festa = [];
        $dados_festa['key'] = $key;
        $dados_festa['nome'] = $produto['nome'];

        foreach ($resultado_busca_produtos_fotos as $key => $foto) {
          if($foto['key_produto'] == $dados_festa['key']){
            $dados_festa['imagem'] = $foto['imagem'];
            break;
          }else{
            $dados_festa['imagem'] = '';
          }
        }
      
        $lista_festas[] = $dados_festa;        
    }
}

// Resto do código da página Home
?>
<!DOCTYPE html>
<html>
<head>
    <title>Minha Loja - Administração</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Inclua seus estilos CSS e scripts JavaScript, se necessário -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
  <div class="container"> 
    <?php include './assets/templates/navbar.php'; ?>
    <h2>Painel de Administração</h2> 

    <!-- Exibe mensagem que retorna da página formulario após adicionar ou atualizar a festa -->
    <!-- Mensagem caso houver erro ao salvar os dados. -->
    <?php if (isset($_SESSION['sucesso'])){
        echo '<div class="alert alert-success">'. $_SESSION['sucesso'] .'</div>';
        unset($_SESSION['sucesso']);
    }?>

    <!-- Mensagem caso houver erro ao salvar os dados. -->
    <?php if (isset($_SESSION['erro'])){
        echo '<div class="alert alert-success">'. $_SESSION['erro'] .'</div>';
        unset($_SESSION['erro']);
    }?>
      
    <!-- Formulário para pesquisar a festa -->
    <form method="post" class="mb-4">
        <div class="input-group">
            <input type="text" name="termo_pesquisa" class="form-control" placeholder="Pesquisar festa">
            <button type="submit" name="pesquisar" class="btn btn-primary">Pesquisar</button>
        </div>
    </form>
    <!-- Botão para acessar o formulário de preferencias -->
    <a href="formulario_preferencias.php" class="btn btn-primary">Configurações</a>

    
    <!-- Exibir a lista de produtos obtidos da API -->
    <h1>Festas</h1>
    <a href="formulario.php?key=0" class="btn btn-primary">Nova Festa</a>
    <?php if ($lista_festas !== null) : ?>
    <div class="row">
        <?php foreach ($lista_festas as $festa) : ?>
            <div class="col-<?= $cards_por_linha ?> col-mb-<?= $cards_por_linha ?> col-sm-<?= $cards_por_linha ?>">
                <div class="card">
                    <img class="card-img-top" src="<?= $festa['imagem']; ?>" alt="<?= $festa['nome']; ?>" width="<?= $tamanho_fotos ?>" height="<?= $tamanho_fotos ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= $festa['nome']; ?></h5>
                        <a href="formulario.php?key=<?= $festa['key']; ?>" class="btn btn-warning">Editar</a>
                        <a href="#" class="btn btn-danger">Excluir</a>
                        <a href="formulario_fotos.php?key_produto=<?= $festa['key']; ?>" class="btn btn-primary">Fotos</a>
                    </div>
                </div>
            </div>            

      
        <?php endforeach; ?>
    </div>
    <?php else : ?>
        <p class="alert alert-danger">Erro ao buscar produtos.</p>
    <?php endif; ?>
    <!-- Resto do conteúdo da página Home -->
    
    
  </div>
</body>
</html>
