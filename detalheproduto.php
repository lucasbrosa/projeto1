<?php
// Inclua o arquivo que contém a definição da classe MinhaAPI
include 'MinhaAPI.php';
// Inclua o arquivo que contém as preferencias do site.
include './config/preferencias.php'; 

// Crie uma instância da classe MinhaAPI com a URL da API e o token
$api = new FirebaseAPI();

// Verifique se foi fornecido um ID de produto válido na consulta GET
if (isset($_GET['id'])) {
    $produto_id = $_GET['id'];
    // Use a instância da classe para buscar detalhes do produto
    $detalhes_produto = $api->get('produtos/'.$produto_id);

    $lista_fotos = [];   

    $resultado_busca = $api->get_equal_to('produtos_fotos','key_produto',$produto_id);   
    
    // Iterar pelos registros para encontrar correspondências
    foreach ($resultado_busca as $key => $imagem) {
        
      $dados_foto = [];
      $dados_foto['key'] = $key;
      $dados_foto['imagem'] = $imagem['imagem'];
    
      $lista_fotos[] = $dados_foto;
        
    } 

  
} else {
    // ID de produto inválido, redirecione ou mostre uma mensagem de erro
    header('Location: index.php'); // Redirecionar para a página Home
    exit;
}

// Resto do código da página Detalhe do Produto
?>
<!DOCTYPE html>
<html>
<head>
    <title>Minha Loja - Detalhes do Produto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Inclua seus estilos CSS e scripts JavaScript, se necessário -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <?php include './assets/templates/navbar.php'; ?>

      <!-- Formulário para pesquisar a festa -->
    <form action="index.php" method="post" class="mb-4">
        <div class="input-group">
            <input type="text" name="termo_pesquisa" class="form-control" placeholder="Pesquisar festa">
            <button type="submit" name="pesquisar" class="btn btn-primary">Pesquisar</button>
        </div>
    </form>

      

        
      <!-- Exibir os detalhes do produto obtidos da API -->      
        <?php if ($detalhes_produto !== false) : ?>
            <!-- Listagem das fotos -->
            <div class="row mt-4">
                <?php
                  if ($lista_fotos) {
                      ?>
                      <div id="carouselFotos" class="carousel slide" data-bs-ride="carousel">
                          <div class="carousel-inner">
                              <?php
                              $primeira_imagem = true;
                              foreach ($lista_fotos as $foto) {
                                  ?>
                                  <div class="carousel-item <?= $primeira_imagem ? 'active' : ''; ?>">
                                      <img src="<?= $foto['imagem']; ?>" class="d-block w-100" alt="Imagem <?= $index + 1; ?>">
                                  </div>
                                  <?php
                                  $primeira_imagem = false;
                              }
                              ?>
                          </div>
                          <a class="carousel-control-prev" href="#carouselFotos" role="button" data-bs-slide="prev">
                              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                              <span class="visually-hidden">Anterior</span>
                          </a>
                          <a class="carousel-control-next" href="#carouselFotos" role="button" data-bs-slide="next">
                              <span class="carousel-control-next-icon" aria-hidden="true"></span>
                              <span class="visually-hidden">Próximo</span>
                          </a>
                      </div>
                      <?php
                  } else {
                      echo '<p>Nenhuma foto encontrada.</p>';
                  }
                  ?>
              </div>        

            <!-- Detalhe do Produto -->
            <h1><?= $detalhes_produto['nome']; ?></h1>
            <p>Preço: $<?= $detalhes_produto['preco']; ?></p>
            <p>Descrição: <?= $detalhes_produto['descricao']; ?></p>
            <!-- Resto do conteúdo da página Detalhe do Produto -->

            <?php 

              // Mensagem que você deseja enviar no WhatsApp
              $mensagem = $mensagem_whatsapp . "\n\nFesta escolhida: https://projeto1.lucasrosa2.repl.co/detalheproduto.php?id=". $produto_id ."";
              
              
              ?>
              

            <a href="https://wa.me//<?= $telefone_whatsapp; ?>?text=<?= rawurlencode($mensagem); ?>" class="btn btn-success">
              <img  src=" ../assets/img/WhatsApp-icone.png" alt="WhatsApp" style="width: 1em">
             
              Solicitar Orçamento</a>
        <?php else : ?>
            <div class="alert alert-danger">
                Erro ao buscar detalhes do produto.
            </div>
        <?php endif; ?>
    </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    

</body>
</html>
