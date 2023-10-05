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


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fotos"])) {
    // Diretório onde as fotos serão armazenadas
    $diretorio_destino = './assets/img/festas';

    // Loop através de todas as fotos enviadas
    foreach ($_FILES["fotos"]["tmp_name"] as $key => $tmp_name) {
        $nome_arquivo = $_FILES["fotos"]["name"][$key];
        $tamanho_arquivo = $_FILES["fotos"]["size"][$key];
        $tipo_arquivo = $_FILES["fotos"]["type"][$key];
        $arquivo_temporario = $_FILES["fotos"]["tmp_name"][$key];

        // Verifique se é uma imagem válida (opcional, mas recomendado)
        $extensoes_permitidas = array("jpeg", "jpg", "png", "gif");
        $extensao = pathinfo($nome_arquivo, PATHINFO_EXTENSION);
        if (!in_array($extensao, $extensoes_permitidas)) {
            $_SESSION['erro'] = 'Erro: Apenas imagens JPEG, JPG, PNG ou GIF são permitidas';
            header('Location: formulario_fotos.php?key_produto='. $_POST["key"]);
            exit();;
        }

        
        // Crie um novo nome para o arquivo
        $novo_nome_arquivo = uniqid() . '.' . $extensao;

        // Mova o arquivo original para o diretório de destino
        $caminho_completo = $diretorio_destino . $novo_nome_arquivo;
        move_uploaded_file($arquivo_temporario, $caminho_completo);

        $key = $_POST["key"];

        //salva no firebase
        $data = [
          'key_produto' => $key,
          'imagem' => $caminho_completo,
        ];

        $api->post('produtos_fotos', $data, $_SESSION['usuario_token']);
    }

    // Redirecione de volta para a página de gerenciamento de fotos com uma mensagem de sucesso
    $_SESSION['sucesso'] = 'Imagens salvas com sucesso!';
    header('Location: formulario_fotos.php?key_produto='. $_POST["key"]);
    exit();
}


if (isset($_GET['key_foto']) && isset($_GET['key_produto'])) {
    // Recupere o valor do parâmetro "id" da URL
    $key_foto = $_GET['key_foto'];
    $key_produto = $_GET['key_produto'];

    $imagem = $api->get('produtos_fotos/'. $key_foto);

    //echo $imagem['imagem'];

    if (file_exists($imagem['imagem'])) {
        if (unlink($imagem['imagem'])) {
            $api->delete('produtos_fotos/'. $key_foto, $_SESSION['usuario_token']);

             $_SESSION['sucesso'] = 'A imagem foi excluída com sucesso!';
            header('Location: formulario_fotos.php?key_produto='. $_GET['key_produto']);
            exit();            
        } else {          
             $_SESSION['erro'] = 'Erro ao excluir a imagem!';
            header('Location: formulario_fotos.php?key_produto='. $_GET['key_produto']);
            exit();
        }
    } else {
        $_SESSION['erro'] = 'A imagem não existe no diretório!';
        header('Location: formulario_fotos.php?key_produto='. $_GET['key_produto']);
        exit();
    }
  
}

if (isset($_GET['key_produto'])) {
    // Recupere o valor do parâmetro "id" da URL
    $key_produto = $_GET['key_produto'];
  
    $lista_fotos = [];   

    $resultado_busca = $api->get_equal_to('produtos_fotos','key_produto',$key_produto);   
    
    // Iterar pelos registros para encontrar correspondências
    foreach ($resultado_busca as $key => $imagem) {
        
      $dados_foto = [];
      $dados_foto['key'] = $key;
      $dados_foto['imagem'] = $imagem['imagem'];
    
      $lista_fotos[] = $dados_foto;
        
    }  
    // Agora você pode usar $produto_id na página para fazer o que desejar, como carregar os detalhes do produto com esse ID.
}

?>

  
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include './assets/templates/header.php'; ?>
</head>
<body>
    <div class="container">
  
        <?php include './assets/templates/navbar.php'; ?>
        <h1>Gerenciar Fotos</h1>

        <!-- Mensagem caso houver erro ao salvar os dados. -->
        <?php if (isset($_SESSION['sucesso'])){
            echo '<div class="alert alert-success">'. $_SESSION['sucesso'] .'</div>';
            unset($_SESSION['sucesso']);
        }?>

        <!-- Mensagem caso houver erro ao salvar os dados. -->
        <?php if (isset($_SESSION['erro'])){
            echo '<div class="alert alert-danger">'. $_SESSION['erro'] .'</div>';
            unset($_SESSION['erro']);
        }?>

        <!-- Formulário para upload de fotos -->
        <form method="post" enctype="multipart/form-data">

            <!-- Campo oculto -->
            <input type="hidden" id="key" name="key" value="<?= $key_produto; ?>">
          
            <div class="mb-3">
                <label for="fotos" class="form-label">Selecione as Fotos</label>
                <input type="file" class="form-control" id="fotos" name="fotos[]" multiple accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enviar Fotos</button>
        </form>            

        <!-- Listagem das fotos -->
        <div class="row mt-4">
            <?php            
              if ($lista_fotos) {
                  foreach ($lista_fotos as $foto) {
                      ?>
                      <div class="col-md-4 mb-4">
                          <div class="card"> 
                              <div class="card-body">
                                  <img src="<?= $foto['imagem']; ?>" class="card-img-top" alt="Imagem">
                              </div>
                              <div class="card-footer">
                                  <a href="formulario_fotos.php?key_produto=<?= $key_produto; ?>&key_foto=<?= $foto['key']; ?>" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Excluir</a>
                              </div>
                          </div>
                      </div>
                      <?php
                  }
              } else {
                  echo '<p>Nenhuma foto encontrada.</p>';
              }
            
            ?>
        </div>

      <a href="index_admin.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Voltar para Administração</a>
      
    </div>

    <script src="../caminho-para-o-bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
