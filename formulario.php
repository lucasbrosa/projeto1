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

// Trate a submissão do formulário para adicionar um novo produto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key = $_POST['key'];
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $descricao = $_POST['descricao'];
    
    // Valide os dados do formulário (por exemplo, verifique se os campos estão preenchidos)
    $data = [
        'nome' => $nome,
        'preco' => $preco,
        'descricao' => $descricao,
      ];

    // Salva ou atualiza os dados no Banco de dados.
    if($key == '0'){
      if ($api->post('produtos', $data, $_SESSION['usuario_token'])) {
          $_SESSION['sucesso'] = 'Festa adicionada com sucesso!';
          header('Location: index_admin.php');
          exit();
      } else {
          $_SESSION['erro'] = 'Erro ao adicionar a festa.';
          exit();
      }
    }else{
      if ($api->put('produtos/'.$key, $data, $_SESSION['usuario_token'])) {
          $_SESSION['sucesso'] = 'Festa atualizada com sucesso!';
          header('Location: index_admin.php');
          exit();
      } else {        
          $_SESSION['erro'] = 'Erro ao atualizar a festa.';
          exit();
      }
    } 
    
}

if (isset($_GET['key'])) {
    // Recupere o valor do parâmetro "id" da URL
    $key = $_GET['key'];
    $dados_produto = null;

    if($key != "0"){
      $dados_produto = $api->get('produtos/'.$key);
    }

    // Agora você pode usar $produto_id na página para fazer o que desejar, como carregar os detalhes do produto com esse ID.
}

if (isset($_GET['key'])) {
    // Recupere o valor do parâmetro "id" da URL
    $key = $_GET['key'];
    $dados_produto = null;

    if($key != "0"){
      $dados_produto = $api->get('produtos/'.$key);
    }

    // Agora você pode usar $produto_id na página para fazer o que desejar, como carregar os detalhes do produto com esse ID.
}

?>
<!DOCTYPE html>
<html>
<head>
    <?php include './assets/templates/header.php'; ?>
<body>
    <div class="container">
        <?php include './assets/templates/navbar.php'; ?>
        <h1>Formulário Dados da Festa</h1>        
        <!-- Mensagem caso houver erro ao salvar/atualizar os dados. -->
        <?php if (isset($_SESSION['erro'])){
            echo '<div class="alert alert-danger">'. $_SESSION['erro'] .'</div>';
            unset($_SESSION['erro']);
        }?>

        <form method="post">
            <!-- Campo oculto -->
            <input type="hidden" id="key" name="key" value="<?= $key; ?>">
          
            <div class="mb-3">
                <label for="nome" class="form-label">Nome da Festa</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?= $dados_produto ? $dados_produto['nome'] : ''?>" required>
            </div>
            <div class="mb-3">
                <label for="preco" class="form-label">Preço</label>
                <input type="text" class="form-control" id="preco" name="preco" oninput="formatarValor(this)" value="<?= $dados_produto ? $dados_produto['preco'] : ''?>" required>
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="3" required><?= $dados_produto ? $dados_produto['descricao'] : $descricao_padrao_produto?></textarea>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> <?= $key == "0" ? ' Adicionar Festa' : ' Atualizar Festa'?></button>
        </form>

        <a href="excluir_produto.php?key=<?= $_GET['key']; ?>" class="btn btn-danger"><i class="fas fa-trash"></i> Excluir festa</a>
      
        <a href="index_admin.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Voltar para Administração</a>
    </div>    

    <script>
        function formatarValor(input) {
            // Remove tudo que não seja número
            let valor = input.value.replace(/\D/g, '');
            
            // Formate o valor como um número financeiro
            let valorFormatado = (Number(valor) / 100).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });

            input.value = valorFormatado;
        }
    </script>
</body>
</html>
