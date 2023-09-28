<?php
class MinhaAPI {

    private $firebaseUrl;
    private $token;

    public function __construct() {
        $this->api_url =  'https://personal-e1754-default-rtdbs.firebaseio.com/';
        $this->token = '';
    }

    public function get($path) {
        $url = $this->firebaseUrl . $path . '.json';
        return $this->sendRequest($url, 'GET');
    }

    public function get_equal_to($path, $orderby ,$term) {
        $url = $this->firebaseUrl . $path . '.json?orderBy="'.$orderby. '"&equalTo="'.$term.'""';
        return $this->sendRequest($url, 'GET');
    }

    public function post($path, $data) {
        $url = $this->firebaseUrl . $path . '.json';
        return $this->sendRequest($url, 'POST', $data);
    }  

    public function put($path, $data) {
        $url = $this->firebaseUrl . $path . '.json';
        return $this->sendRequest($url, 'PUT', $data);
    } 

    private function sendRequest($url, $method, $data = null) {
        $options = [
            'http' => [
                'header' => 'Content-type: application/json',
                'method' => $method,
            ],
        ];

        if ($this->token) {
            $options['http']['header'] .= "\r\n" . 'Authorization: Bearer ' . $this->token;
        }

        if ($data !== null) {
            $options['http']['content'] = json_encode($data);
        }

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        return json_decode($response, true);
    }
}

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

    public function post($path, $data) {
        $url = $this->firebaseUrl . $path . '.json';
        return $this->sendRequest($url, 'POST', $data);
    }

    public function put($path, $data) {
        $url = $this->firebaseUrl . $path . '.json';
        return $this->sendRequest($url, 'PUT', $data);
    }

    public function delete($path) {
        $url = $this->firebaseUrl . $path . '.json';
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
}

?>