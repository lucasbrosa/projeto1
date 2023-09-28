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
// Inclua o arquivo que contém as preferencias do site.
include './config/preferencias.php'; 

// Crie uma instância da classe MinhaAPI com a URL da API e o token
$api = new FirebaseAPI();

$lista_festas = [];

// Função de comparação personalizada para classificar pelo valor da chave "nome"
function compararPorNome($a, $b) {
    return strcmp($a['nome'], $b['nome']);
}

// Verifique se o parâmetro "logout" está definido na URL
if (isset($_GET["logout"]) && $_GET["logout"] === "true") {
    // Destruição das variáveis de sessão específicas
    unset($_SESSION["usuario_logado"]);
    unset($_SESSION["usuario_token"]);

    // Redirecione para a página de login ou outra página desejada
    header("Location: login.php"); // Substitua "login.php" pela página desejada
    exit();
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
  
}else {
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

    <div>
      <!-- Botão para acessar o formulário de preferencias -->
      <a href="formulario_preferencias.php" class="btn btn-primary">Configurações</a>
      
      <!-- Botão para encerrar a sessão do usuário -->
      <a href="index_admin.php?logout=true" class="btn btn-danger">Sair</a>
    </div>   
    

    <!-- Formulário para pesquisar a festa -->
    <form method="post" class="mb-4">
        <div class="input-group">
            <input type="text" name="termo_pesquisa" class="form-control" placeholder="Pesquisar festa">
            <button type="submit" name="pesquisar" class="btn btn-primary">Pesquisar</button>
        </div>
    </form>

    
    <!-- Exibir a lista de produtos obtidos da API -->
    <h1>Festas</h1>
    <a href="formulario.php?key=0" class="btn btn-primary">Nova Festa</a>

    <!-- Exibe a paginação e cards -->    
    
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
        <?php endif; ?>
    <?php endforeach; ?>
    
    <!-- Botões de página dentro do container -->
  </div>
  
</body>
</html>
