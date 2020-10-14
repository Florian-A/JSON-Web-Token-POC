<?php

class LoginController
{

    private $userName;
    private $userPassword;

    private $secretUserName = "bob";
    private $secretUserPassword = 1234;

    public $token;
    public $loginError = 0;

    public function __construct()
    {
    }

    public function createJWT()
    {
        // Créer une en-tête de jeton sous forme de chaîne JSON
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        // Créer une charge utile de jeton sous forme de chaîne JSON
        $payload = json_encode(['user_id' => 123]);

        // Modification des chaines de caractères
        // Encoder l'en-tête en chaîne Base64Url
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        // Encoder la charge utile en chaîne Base64Url
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Créer un hachage de la signature
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);

        // Encoder la signature en chaîne Base64Url
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        // Création du JWT.
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        return $jwt;
    }

    public function login()
    {
        if ($_POST) {
            if ($_POST['userLogin'] != NULL && strlen($_POST['userLogin']) >= 2 &&  strlen($_POST['userLogin']) <= 20) {

                $this->userName = $_POST['userLogin'];
            } else {
                $this->loginError = 1;
            }

            if ($_POST['userPassword'] != NULL && strlen($_POST['userPassword']) >= 4 &&  strlen($_POST['userPassword']) <= 20) {

                $this->userPassword = $_POST['userPassword'];
            } else {
                $this->loginError = 1;
            }

            // Filtrage de sécurité des variables envoyés par depuis le post.
            if ($this->loginError == 0) {
                $this->userName = strtolower(filter_var($this->userName, FILTER_SANITIZE_STRING));
                $this->userPassword = filter_var($this->userPassword, FILTER_SANITIZE_STRING);
            }

            if ($this->userName == $this->secretUserName && $this->userPassword == $this->secretUserPassword) {

                $this->token = $this->createJWT();
            }
        }
    }
}
