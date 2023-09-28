<?php

class FirebaseAPI {

    private $firebaseUrl;

    public function __construct() {
        $this->firebaseUrl = 'https://personal-e1754-default-rtdb.firebaseio.com/';       
    }

    public function get($path) {
        $url = $this->firebaseUrl . $path . '.json';
        return $this->sendRequest($url, 'GET');
    }

    public function get_equal_to($path, $orderby ,$term) {
        $url = $this->firebaseUrl . $path . '.json?orderBy="'.$orderby. '"&equalTo="'.$term.'"';
        return $this->sendRequest($url, 'GET');
    }

    public function post($path, $data, $token) {
        $url = $this->firebaseUrl . $path . '.json?auth='.$token;
        return $this->sendRequest($url, 'POST', $data);
    }

    public function put($path, $data, $token) {
        $url = $this->firebaseUrl . $path . '.json?auth='.$token;
        return $this->sendRequest($url, 'PUT', $data);
    }

    public function delete($path, $token) {
        $url = $this->firebaseUrl . $path . '.json?auth='.$token;
        return $this->sendRequest($url, 'DELETE');
    }
  

    private function sendRequest($url, $method, $data = null) {
        $options = [
            'http' => [
                'header' => 'Content-type: application/json',
                'method' => $method,
            ],
        ];       

        if ($data !== null) {
            $options['http']['content'] = json_encode($data);
        }

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        return json_decode($response, true);
    }

    public function autenticarEmailSenha($data){
       $apiKey = getenv('API_KEY');

      if (!$apiKey) {
          $_SESSION['erro'] = 'API_KEY não definida no ambiente.';
          header('Location: login.php');
          exit();
      }
  
      $autenticadorUrl = 'https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=' . $apiKey;
  
      $options = [
          'http' => [
              'header' => 'Content-type: application/json',
              'method' => 'POST',
          ],
      ];
  
      if ($data !== null) {
          $options['http']['content'] = json_encode($data);
      }
  
      $context = stream_context_create($options);
      $response = @file_get_contents($autenticadorUrl, false, $context);
  
      if ($response === false) {
          $_SESSION['erro'] = 'Erro ao fazer a solicitação de autenticação.';
          header('Location: login.php');
          exit();
      }
  
      $jsonData = json_decode($response, true);
  
      if ($jsonData === null) {
          $_SESSION['erro'] = 'Erro ao analisar a resposta JSON.';
          header('Location: login.php');
          exit();
      }
  
      return $jsonData;
    }
}


?>