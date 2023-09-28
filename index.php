<?php
// Inclua o arquivo que contém a definição da classe MinhaAPI
include 'MinhaAPI.php';
// Inclua o arquivo que contém as preferencias do site.
include './config/preferencias.php'; 

// Crie uma instância da classe MinhaAPI com a URL da API e o token
$api = new FirebaseAPI();

$lista_festas = [];

// Função de comparação personalizada para classificar pelo valor da chave "nome"
function compararPorNome($a, $b) {
    return strcmp($a['nome'], $b['nome']);
}


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

    // Classifique o array usando a função de comparação personalizada
    usort($lista_festas, 'compararPorNome');

    // Crie uma matriz para agrupar os itens alfabeticamente
    $grupos_alfabeticos = [];
    foreach ($lista_festas as $festa) {
        $primeira_letra = strtoupper(substr($festa['nome'], 0, 1));
        $grupos_alfabeticos[$primeira_letra][] = $festa;
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

    // Classifique o array usando a função de comparação personalizada
    usort($lista_festas, 'compararPorNome');

    // Crie uma matriz para agrupar os itens alfabeticamente
    $grupos_alfabeticos = [];
    foreach ($lista_festas as $festa) {
        $primeira_letra = strtoupper(substr($festa['nome'], 0, 1));
        $grupos_alfabeticos[$primeira_letra][] = $festa;
    }
}

// Resto do código da página Home
?>
<!DOCTYPE html>
<html>
<head>
    <?php include './assets/templates/header.php'; ?>
</head>
<body>
  <div class="container">
    <?php include './assets/templates/navbar.php'; ?>
    <!-- Formulário para pesquisar a festa -->
    <form action="index.php" method="post" class="mb-4">
        <div class="input-group">
            <input type="text" name="termo_pesquisa" class="form-control" placeholder="Pesquisar festa">
            <button type="submit" name="pesquisar" class="btn btn-primary"><i class="fas fa-search"></i> Pesquisar</button>
        </div>
    </form>
    <!-- Cards das festas -->
    <h1>Festas</h1>
    <!-- Botões de página dentro do container -->
    <div class="container">
        <div class="btn-group d-flex flex-wrap" role="group" aria-label="Paginação alfabética">
            <?php foreach (range('A', 'Z') as $letra) : ?>
                <?php if (isset($grupos_alfabeticos[$letra]) && !empty($grupos_alfabeticos[$letra])) : ?>
                    <a href="#<?= $letra ?>" class="btn btn-primary"><?= $letra ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    
    
    <!-- Lista de itens classificados por letra -->
    <?php foreach (range('A', 'Z') as $letra) : ?>
        <?php if (isset($grupos_alfabeticos[$letra])) : ?>
            <h2 id="<?= $letra ?>"><?= $letra ?></h2>
            <div class="row">
                <?php foreach ($grupos_alfabeticos[$letra] as $festa) : ?>
                    <div class="col-<?= $cards_por_linha ?> col-mb-<?= $cards_por_linha ?> col-sm-<?= $cards_por_linha ?>">
                        <div class="card">
                            <img class="card-img-top" src="<?= $festa['imagem']; ?>" alt="<?= $festa['nome']; ?>" width="<?= $tamanho_fotos ?>" height="<?= $tamanho_fotos ?>">
                            <div class="card-footer text-center">
                                <h5 class="card-title"><?= $festa['nome']; ?></h5>
                                <a href="detalheproduto.php?id=<?= $festa['key']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-info-circle"></i> Detalhes</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

  <button id="btnTopo" class="btn btn-primary back-to-top" style="display:none;">
      <i class="fas fa-chevron-up"></i> Voltar para o Topo
  </button>
    
  </div>   
</body>

    <script>
      // Quando a página é rolada, exibe ou oculta o botão "Voltar para o Topo"
      window.onscroll = function() {
          if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
              document.getElementById("btnTopo").style.display = "block";
          } else {
              document.getElementById("btnTopo").style.display = "none";
          }
      };
  
      // Quando o botão "Voltar para o Topo" é clicado, rola a página até o topo
      document.getElementById("btnTopo").onclick = function() {
          document.body.scrollTop = 0; // Para navegadores da web
          document.documentElement.scrollTop = 0; // Para o Internet Explorer, Edge, Firefox e Chrome
      };
  </script>

  
    <!-- Resto do conteúdo da página Home --> 
    <?php include './assets/templates/footer.php'; ?>
</html>
