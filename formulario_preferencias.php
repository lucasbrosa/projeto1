<?php
// Inicie ou retome a sessão
session_start();

// Inclua o arquivo que contém a definição da classe MinhaAPI
include 'MinhaAPI.php';

$utils = new Utils();

// Verifique se a variável de sessão "usuario_logado" está definida e é verdadeira
if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    // Se o usuário não estiver logado, redirecione para a página de login.php
    header('Location: login.php');
    exit; // Certifique-se de sair do script após redirecionar
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $preferencias = [
        'telefone' => $_POST['telefone'],
        'mensagem' => $_POST['mensagem'],
        'descricao' => $_POST['descricao'],
        'cards_por_linha' => $_POST['cards_por_linha'],
        'tamanho_fotos' => $_POST['tamanho_fotos']
    ];

    // Criptografar os dados e salvá-los em um arquivo JSON
    $json_preferencias = json_encode($preferencias);
    file_put_contents('./config/preferencias.json', $json_preferencias);    

    // Redireciona de volta para a página de preferências
    $_SESSION['sucesso'] = 'Configurações salvas com sucesso!';
    header('Location: index_admin.php');
    exit();
    
} else {
  include './config/preferencias.php';  
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações</title>
    <!-- Adicione o link para o Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <?php include './assets/templates/navbar.php'; ?>
        <h1 class="mb-4">Configurações</h1>
        
        <form method="POST">
            <div class="mb-3">
            <label for="telefone" class="form-label">Número do WhatsApp:</label>
            <input type="text" id="telefone" name="telefone" class="form-control" value="<?= $telefone_whatsapp ?>" required>
        </div>

        <div class="mb-3">
            <label for="mensagem" class="form-label">Texto Padrão para a Mensagem do WhatsApp:</label>
            <textarea id="mensagem" name="mensagem" class="form-control" rows="4" required><?= $mensagem_whatsapp ?></textarea>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Texto Padrão da Descrição de Novos Produtos:</label>
            <textarea id="descricao" name="descricao" class="form-control" rows="4" required><?= $descricao_padrao_produto ?></textarea>
        </div>

        <div class="mb-3">
            <label for="cards_por_linha" class="form-label">Número de Cards por Linha na Página Inicial:</label>
            <select id="cards_por_linha" name="cards_por_linha" class="form-select" required>
                <option value="12" <?= ($cards_por_linha == '12') ? 'selected' : '' ?>>1</option>
                <option value="6" <?= ($cards_por_linha == '6') ? 'selected' : '' ?>>2</option>
                <option value="4" <?= ($cards_por_linha == '4') ? 'selected' : '' ?>>3</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="tamanho_fotos" class="form-label">Tamanho das Fotos nos Cards (em pixels):</label>
            <input type="number" id="tamanho_fotos" name="tamanho_fotos" class="form-control" min="100" value="<?= $tamanho_fotos ?>" required>
        </div>

        <button type="submit" class="btn btn-success">Salvar Configurações</button>
        </form>

        <a href="index_admin.php" class="btn btn-primary">Voltar para Administração</a>
    </div>
 

    <!-- Adicione os scripts do Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-9aSfPBSc5VhKDg7F5RJWrF5v/jpCf5m7aPDv6KoVk5Ck3CU5PzEr9z6wVbcKsSF5" crossorigin="anonymous"></script>
</body>
</html>
