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
  // Inclua o arquivo que contém as preferencias do site.
  include './config/preferencias.php'; 

  

  // Busca os dados das no banco de dados.
  if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['key'])){
      // Recupere o valor do parâmetro "id" da URL
      $key = $_GET['key'];
      $dados_produto = null;
      $dados_foto_capa = null;
      $dados_imagem_capa = null;
      $dados_fotos_galeria = null;
      
      if($key != "0"){
        $dados_produto = obterProdutoSQLite3($key);
        $dados_foto_capa = obterProdutoFotoCapaSQLite3($key);
        $dados_fotos_galeria = obterProdutoFotosSQLite3($key);
        $dados_imagem_capa = obterImagemCapaPorKeyProdutoSQLite3($key);
      } 

  }



  
?>
  
<!DOCTYPE html>
<html>
<head>
    <?php include './assets/templates/header.php'; ?>
    
    <script>
        function excluirItem(row) {
            if (confirm("Tem certeza de que deseja excluir este item?")) {
                var table = row.parentNode.parentNode;
                table.deleteRow(row.rowIndex);
            }
        }
    </script>
</head>
<body>
  <div class="container">
    <?php include './assets/templates/navbar.php'; ?>
    <h1>Formulário Dados da Festa</h1>  
    <!-- Exibe mensagem que retorna da página formulario após adicionar ou atualizar a festa -->
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

    <form action="adicionar_produto.php" method="post">

        <input type="hidden" name="key" value="<?= $_GET['key'] ?>" required>
        <div class="mb-3">
          <label for="nome" class="form-label">Nome da Festa</label>
          <input type="text" class="form-control" name="nome" value="<?= $dados_produto ? $dados_produto[0]['nome'] : '' ?>" required>
        </div>
        <div class="mb-3">
          <label for="descricao" class="form-label">Descrição</label>
          <textarea class="form-control" name="descricao" required><?= $dados_produto ? $dados_produto[0]['descricao'] : $descricao_padrao_produto ?></textarea>
        </div>
        <div class="mb-3">
          <label for="preco" class="form-label">Preço</label>
          <input type="text" class="form-control" name="preco" oninput="formatarValor(this)" value="<?= $dados_produto ? $dados_produto[0]['preco'] : '' ?>" required>
        </div>
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> <?= $key == "0" ? ' Adicionar Festa' : ' Atualizar Festa'?></button>
    </form>
  
  
    <!-- Fotos Galeria --> 
    <h2> Fotos Galeria </h2>
    <?php if($_GET['key'] != '0'): ?>
      <form action="adicionar_foto_produto_galeria.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="key" value="<?= $_GET['key'] ?>" required>
        <input type="file" name="foto_galeria[]" multiple>
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Adicionar Foto Galeria</button>
      </form>
    <?php endif; ?>

    <!-- configura a imagem de capa -->

    <?php if(count($dados_fotos_galeria) > 0): ?>
      <form method="post" enctype="multipart/form-data" action="adicionar_foto_capa.php"> 
        <div class="mb-3">
          <input type="hidden" name="key" value="<?= $_GET['key'] ?>" required>

          <label for="imagem_principal" class="form-label"><h2>Selecione a imagem principal</h2></label>
          <select class="form-select" id="imagem_principal" name="imagem_principal">
            <option value="0" selected>Selecione a imagem principal</option>
            <?php 
              //variaveis local
              $qtd_fotos_para_indice = count($dados_fotos_galeria);
              $contador_indice = 1;
              $imagem_capa_value = null;

              //se $dados_imagem_capa contem valor
              if (count($dados_imagem_capa) > 0) {
                //adiciona a o valor da key_imagem na variavel
                $imagem_capa_value = $dados_imagem_capa[0]['key_imagem'];
              }
              
              //percorre as imagens 
              foreach($dados_fotos_galeria as $key => $foto){
                //cria a opção com os dados da imagem selecionada se o valor do option for igual a cadastrada no banco de dados
                if($foto["key"] == $imagem_capa_value){
                  
                  echo '<option value="'.$foto["key"].'" selected>'.$contador_indice.'</option>';
                }                
                else{
                  //cria a opção com os dados da imagem
                  echo '<option value="'.$foto["key"].'">'.$contador_indice.'</option>';
                }
                
                $contador_indice++;
              }
            ?>
          </select>          
          <button type="submit" class="btn btn-primary">Configurar</button>
        </div>
      </form>
    <?php endif; ?>

    
    <table>
        <!-- Conteúdo da tabela aqui -->   
        <?php if($dados_fotos_galeria > 0): ?>
          <?php foreach($dados_fotos_galeria as $key => $foto): ?>
            <tr>           
              <td><img src="<?= $foto["imagem"] ?>" alt="" height="100" width="100"></td>
              <td><a href="excluir_imagem_produto_galeria.php?key_foto_galeria=<?= $foto["key"] ?>&caminho_foto=<?= $foto["imagem"] ?>&key_produto=<?= $foto["key_produto"] ?>" class="btn btn-danger"><i class="fas fa-trash"></i> Excluir</a></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
    </table>    

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