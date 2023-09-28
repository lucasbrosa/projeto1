<?php
// Inicie a sessão (se ainda não estiver iniciada)
session_start();

// Verifique se o usuário já está logado, redirecione para a página admin se estiver
if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
    header('Location: index_admin.php');
    exit;
}

// Inclua o arquivo que contém a definição da classe MinhaAPI
include 'MinhaAPI.php';

// Crie uma instância da classe MinhaAPI com a URL da API e o token
$api = new FirebaseAPI();

// Verifique se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifique se os campos de e-mail e senha foram enviados
    if (isset($_POST['email']) && isset($_POST['senha'])) {
        // Verifique se as credenciais são válidas (substitua esta parte com a validação real)
        $email_valido = 'seuemail@example.com';
        $senha_valida = 'suasenha';

        $data = [
          'email' => $_POST['email'],
          'password' => $_POST['senha'],
          'returnSecureToken' => true
        ];    

        $resultado = $api->autenticarEmailSenha($data);

        //return var_dump($resultado);

        $_SESSION['usuario_logado'] = true;
        $_SESSION['usuario_token'] = $resultado['idToken'];

        // Redirecione para a página admin após o login bem-sucedido
        header('Location: index_admin.php');
        exit();
      
    } 
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Inclua seus estilos CSS e scripts JavaScript, se necessário -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
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

    <!-- Adicione o link para o arquivo JS do Bootstrap 5 (opcional, dependendo do seu uso) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+Jtbc4UWy5w5lL/TIPbMdm2L5w5wlF5dYlLO2tvmhJABIlFf" crossorigin="anonymous"></script>
</body>
</html>
