<?php

function conectarSQLite3() {
    return new SQLite3('meu_banco_de_dados.db');
}

function conectarFirebase(){
  $url_lucas = "https://personal-e1754-default-rtdb.firebaseio.com/";
  $url_jose = "https://site-26270-default-rtdb.firebaseio.com/";

  return $url_lucas;
}

function criarTabelaProdutosSQLite3() {
    $db = conectarSQLite3();
    $queryCriarTabela = "CREATE TABLE IF NOT EXISTS produtos (key TEXT PRIMARY KEY UNIQUE, descricao TEXT, nome TEXT, preco TEXT)";
    $db->query($queryCriarTabela);
    $db->close();
}

function criarTabelaProdutosFotosSQLite3() {
    $db = conectarSQLite3();
    $queryCriarTabela = "
        CREATE TABLE IF NOT EXISTS produtos_fotos (
            key TEXT PRIMARY KEY UNIQUE,
            imagem TEXT,
            key_produto TEXT
        )
    ";
    $db->exec($queryCriarTabela);
    $db->close();
}

function criarTabelaProdutosFotosCapaSQLite3() {
    $db = conectarSQLite3();
    $queryCriarTabela = "
        CREATE TABLE IF NOT EXISTS produtos_fotos_capa (
            key TEXT PRIMARY KEY UNIQUE,
            imagem TEXT,
            key_produto TEXT
        )
    ";
    $db->exec($queryCriarTabela);
    $db->close();
}

function criarTabelaImagemCapaSQLite3() {
    $db = conectarSQLite3();
    $queryCriarTabela = "
        CREATE TABLE IF NOT EXISTS imagem_capa (
            key_produto TEXT PRIMARY KEY UNIQUE,
            key_imagem TEXT
        )
    ";
    $db->exec($queryCriarTabela);
    $db->close();
}

// Função para gerar uma chave aleatória no estilo Firebase
function gerarChaveAleatoria() {
    return bin2hex(random_bytes(16));
}

//Funções para a tabela produtos

function inserirProdutoSQLite3($dados) {
  $conn = conectarSQLite3();
  $sql = "INSERT INTO produtos (key, descricao, nome, preco) VALUES ('".$dados['key']."','".$dados['descricao']."','".$dados['nome']."','".$dados['preco']."')";
  $conn->query($sql);
  $conn->close();
}

function inserirProdutoFirebase($dados) {
  //return var_dump(conectarFirebase());
  $url = conectarFirebase() . 'produtos/'. $dados['key'] . '.json';

  $options = [
      'http' => [
          'header' => 'Content-type: application/json',
          'method' => 'PUT',
          'content' => json_encode($dados),
      ],
  ];

  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);  
}

function atualizarProdutoSQLite3($dados) {
  $conn = conectarSQLite3();
  $sql = "UPDATE produtos SET nome = '".$dados['nome']."', descricao = '".$dados['descricao']."', preco = '".$dados['preco']."' WHERE key = '".$dados['key']."'";
  $conn->query($sql);
  $conn->close();
}

function atualizarProdutoFirebase($dados) {
  //return var_dump(conectarFirebase());
  $url = conectarFirebase() . 'produtos/'. $dados['key'] . '.json';

  $options = [
      'http' => [
          'header' => 'Content-type: application/json',
          'method' => 'PATCH',
          'content' => json_encode($dados),
      ],
  ];

  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);  
}

function obterProdutosSQLite3() {
  $produtos = array();
  // Create connection
  $conn = conectarSQLite3();
  $sql = "SELECT * FROM produtos";
  $result = $conn->query($sql);
  while($row = $result->fetchArray()) {
    $produto = [
      'key' => $row['key'],
      'nome' => $row['nome'],
      'descricao' => $row['descricao'],
      'preco' => $row['preco']
    ];
    array_push($produtos, $produto);
  }
  //$produtos = $result->fetchArray();
  $conn->close();
  return $produtos;
}

function obterProdutoSQLite3($key) {
  // Create connection  
  $produtos = array();
  // Create connection
  $conn = conectarSQLite3();
  $sql = "SELECT * FROM produtos WHERE key = '$key'";
  $result = $conn->query($sql);
  while($row = $result->fetchArray()) {
    $produto = [
      'key' => $row['key'],
      'nome' => $row['nome'],
      'descricao' => $row['descricao'],
      'preco' => $row['preco']
    ];
    array_push($produtos, $produto);
  }
  //$produtos = $result->fetchArray();
  $conn->close();
  return $produtos;
}

function obterProdutosPorPesquisaSQLite3($nome) {
  // Create connection  
  $produtos = array();
  // Create connection
  $conn = conectarSQLite3();
  $sql = "SELECT * FROM produtos WHERE nome LIKE '%$nome%'";
  $result = $conn->query($sql);
  while($row = $result->fetchArray()) {
    $produto = [
      'key' => $row['key'],
      'nome' => $row['nome'],
      'descricao' => $row['descricao'],
      'preco' => $row['preco']
    ];
    array_push($produtos, $produto);
  }
  //$produtos = $result->fetchArray();
  $conn->close();
  return $produtos;
}

function removerProdutoSQLite3($key) {
  $conn = conectarSQLite3();
  $sql = "DELETE FROM produtos WHERE key = '".$key."'";
  $conn->query($sql);
  $conn->close();
}

function removerProdutoFirebase($key) {
  //return var_dump(conectarFirebase());
  $url = conectarFirebase() . 'produtos/'. $key . '.json';

  $options = [
      'http' => [
          'header' => 'Content-type: application/json',
          'method' => 'DELETE',
      ],
  ];

  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);
}

//Funções para a tabela produtos_fotos_capa

function inserirProdutoFotoCapaSQLite3($dados) {
  $conn = conectarSQLite3();
  $sql = "INSERT INTO produtos_fotos_capa (key, imagem, key_produto) VALUES ('".$dados['key']."','".$dados['imagem']."','".$dados['key_produto']."')";
  $conn->query($sql);
  $conn->close();
}

function inserirProdutoFotoCapaFirebase($dados) {
  //return var_dump(conectarFirebase());
  $url = conectarFirebase() . 'produtos_fotos_capa/'. $dados['key'] . '.json';

  $options = [
      'http' => [
          'header' => 'Content-type: application/json',
          'method' => 'PUT',
          'content' => json_encode($dados),
      ],
  ];

  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);  
}

function removerProdutoFotoCapaSQLite3($key) {
  $conn = conectarSQLite3();
  $sql = "DELETE FROM produtos_fotos_capa WHERE key = '".$key."'";
  $conn->query($sql);
  $conn->close();
}

function removerProdutoFotoCapaFirebase($key) {
  //return var_dump(conectarFirebase());
  $url = conectarFirebase() . 'produtos_fotos_capa/'. $key . '.json';

  $options = [
      'http' => [
          'header' => 'Content-type: application/json',
          'method' => 'DELETE',
      ],
  ];

  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);  
}

function obterProdutoFotoCapaSQLite3($key_produto) {
  // Create connection  
  $produtos_foto_capa = array();
  // Create connection
  $conn = conectarSQLite3();
  $sql = "SELECT * FROM produtos_fotos_capa WHERE key_produto = '$key_produto'";
  $result = $conn->query($sql);
  while($row = $result->fetchArray()) {
    $produto_foto_capa = [
      'key' => $row['key'],
      'imagem' => $row['imagem'],
      'key_produto' => $row['key_produto']
    ];
    array_push($produtos_foto_capa, $produto_foto_capa);
  }
  $conn->close();
  return $produtos_foto_capa;
}


//Funções para a tabela produtos_fotos

function inserirProdutoFotoSQLite3($dados) {
  $conn = conectarSQLite3();
  $sql = "INSERT INTO produtos_fotos (key, imagem, key_produto) VALUES ('".$dados['key']."','".$dados['imagem']."','".$dados['key_produto']."')";
  $conn->query($sql);
  $conn->close();
}

function inserirProdutoFotoFirebase($dados) {
  //return var_dump(conectarFirebase());
  $url = conectarFirebase() . 'produtos_fotos/'. $dados['key'] . '.json';

  $options = [
      'http' => [
          'header' => 'Content-type: application/json',
          'method' => 'PUT',
          'content' => json_encode($dados),
      ],
  ];

  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);  
}

function removerProdutoFotoSQLite3($key) {
  $conn = conectarSQLite3();
  $sql = "DELETE FROM produtos_fotos WHERE key = '".$key."'";
  $conn->query($sql);
  $conn->close();
}

function removerProdutoFotoFirebase($key) {
  //return var_dump(conectarFirebase());
  $url = conectarFirebase() . 'produtos_fotos/'. $key . '.json';

  $options = [
      'http' => [
          'header' => 'Content-type: application/json',
          'method' => 'DELETE',
      ],
  ];

  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);  
}

function obterProdutoFotosSQLite3($key_produto) {
  // Create connection  
  $produtos_foto_capa = array();
  // Create connection
  $conn = conectarSQLite3();
  $sql = "SELECT * FROM produtos_fotos WHERE key_produto = '$key_produto'";
  $result = $conn->query($sql);
  while($row = $result->fetchArray()) {
    $produto_foto_capa = [
      'key' => $row['key'],
      'imagem' => $row['imagem'],
      'key_produto' => $row['key_produto']
    ];
    array_push($produtos_foto_capa, $produto_foto_capa);
  }
  $conn->close();
  return $produtos_foto_capa;
}

function obterProdutoFotosPorKeyImagemSQLite3($key_imagem) {
  // Create connection  
  $produtos_foto_capa = array();
  // Create connection
  $conn = conectarSQLite3();
  $sql = "SELECT * FROM produtos_fotos WHERE key= '$key_imagem'";
  $result = $conn->query($sql);
  while($row = $result->fetchArray()) {
    $produto_foto_capa = [
      'key' => $row['key'],
      'imagem' => $row['imagem'],
      'key_produto' => $row['key_produto']
    ];
    array_push($produtos_foto_capa, $produto_foto_capa);
  }
  $conn->close();
  return $produtos_foto_capa;
}


function realizarLogin($email, $senha) {
  $status = null;
  //return var_dump($email, $senha);
  // Conectar ao banco de dados SQLite
  $conn = conectarSQLite3();
  // Consulta para obter as informações do usuário com base no e-mail
  $sql = "SELECT * FROM logins WHERE email = '$email' AND senha = '$senha'";    
  $result = $conn->query($sql);
  if ($result->fetchArray() > 0){
    $conn->close();
    $status = true;
  }else{
    $conn->close();
    $status = false;
  } 

  return $status;  
    
}


function inserirImagemCapaSQLite3($dados) {
  $conn = conectarSQLite3();
  $sql = "INSERT INTO imagem_capa (key_produto, key_imagem) VALUES ('".$dados['key_produto']."','".$dados['key_imagem']."')";
  $conn->query($sql);
  $conn->close();
}

function inserirImagemCapaFirebase($dados) {
  //return var_dump(conectarFirebase());
  $url = conectarFirebase() . 'imagem_capa/'. $dados['key_produto'] . '.json';

  $options = [
      'http' => [
          'header' => 'Content-type: application/json',
          'method' => 'PUT',
          'content' => json_encode($dados),
      ],
  ];

  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);

}

function atualizarImagemCapaSQLite3($dados) {
  $conn = conectarSQLite3();
  $sql = "UPDATE imagem_capa SET key_imagem = '".$dados['key_imagem']."' WHERE key_produto = '".$dados['key_produto']."'";
  $conn->query($sql);
  $conn->close();
}

function atualizarImagemCapaFirebase($dados) {
  //return var_dump(conectarFirebase());
  $url = conectarFirebase() . 'imagem_capa/'. $dados['key_produto'] . '.json';

  $options = [
      'http' => [
          'header' => 'Content-type: application/json',
          'method' => 'PATCH',
          'content' => json_encode($dados),
      ],
  ];

  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);

}

function removerImagemCapaSQLite3($key_imagem) {
  $conn = conectarSQLite3();
  $sql = "DELETE FROM imagem_capa WHERE key_imagem = '".$key_imagem."'";
  $conn->query($sql);
  $conn->close();
}

function removerImagemCapaFirebase($key) {
  //return var_dump(conectarFirebase());
  $url = conectarFirebase() . 'produtos_fotos/'. $key . '.json';

  $options = [
      'http' => [
          'header' => 'Content-type: application/json',
          'method' => 'DELETE',
      ],
  ];

  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);  
}

function obterImagemCapaPorKeyProdutoSQLite3($key_produto) {
  // Create connection  
  $produtos_foto_capa = array();
  // Create connection
  $conn = conectarSQLite3();
  $sql = "SELECT * FROM imagem_capa WHERE key_produto = '$key_produto'";
  $result = $conn->query($sql);
  while($row = $result->fetchArray()) {
    $produto_foto_capa = [
      'key_produto' => $row['key_produto'],
      'key_imagem' => $row['key_imagem'],
    ];
    array_push($produtos_foto_capa, $produto_foto_capa);
  }
  $conn->close();
  return $produtos_foto_capa;
}


// Chame a função para criar a tabela quando o arquivo é incluído
criarTabelaProdutosSQLite3();
criarTabelaProdutosFotosSQLite3();
criarTabelaProdutosFotosCapaSQLite3();
criarTabelaImagemCapaSQLite3();


?>
