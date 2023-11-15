<?php
// Inicie a sessão (se ainda não estiver iniciada)
session_start();

// Verifique se o usuário já está logado, redirecione para a página admin se estiver
if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
    header('Location: index_admin.php');
    exit;
}

// Inclua o arquivo que contém a definição da classe MinhaAPI
include 'model.php';

// Crie uma instância da classe MinhaAPI com a URL da API e o token
//$api = new FirebaseAPI();

// Verifique se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifique se os campos de e-mail e senha foram enviados
    if (isset($_POST['email']) && isset($_POST['senha'])) {
        // Verifique se as credenciais são válidas (substitua esta parte com a validação real)
        
        $resultado = realizarLogin($_POST['email'], $_POST['senha']);

        //return var_dump($resultado);

        if($resultado){
            $_SESSION['usuario_logado'] = true;
            //$_SESSION['usuario_token'] = $resultado['idToken'];

            // Redirecione para a página admin após o login bem-sucedido
            header('Location: index_admin.php');
            exit();
        }else{
            $_SESSION['erro'] = "E-mail e/ou senha errados!";
            header('Location: login.php');
            exit();
        }      
    } 
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php include './assets/templates/header.php'; ?>
</head>
<body>
    <div class="container mt-5">
        <?php include './assets/templates/navbar.php'; ?>
        <h1 class="mb-4">Login</h1>
        <!-- Mensagem caso houver erro ao salvar os dados. -->
        <?php if (isset($_SESSION['erro'])){
            echo '<div class="alert alert-danger">'. $_SESSION['erro'] .'</div>';
            unset($_SESSION['erro']);
        }?>

        <form method="post">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail:</label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha:</label>
                <input type="password" class="form-control" name="senha" id="senha" required>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>    
</body>
</html>
