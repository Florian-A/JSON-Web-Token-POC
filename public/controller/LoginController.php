<?php

class LoginController
{

    private $userName;
    private $userPassword;

    private $secretUserName = "bob";
    private $secretUserPassword = 1234;

    private $JWTHashingSecret = "xz54op5uwe32nbzkj3jh43";

    public $token;
    public $loginError = 0;

    public function __construct()
    {
    }

    public function createJWT()
    {
        // Créer une en-tête de jeton sous forme de chaîne JSON
        $header = json_encode(
            [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ]
        );

        // Création d'une charge utile du token sous forme de chaîne JSON
        $payload = json_encode(
            [
                'iat' => time(),
                'exp' => time() + (60 * 60),
                'aswerOfAnythink' => 42
            ]
        );

        // Encodage l'en-tête et de la charge utile en Base64Url
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Création de la signature
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->JWTHashingSecret, true);

        // Encodage de la signature en chaîne Base64Url
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        // Création du token JWT.
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        return $jwt;
    }

    public function login()
    {
        if ($_POST) {
            if ($_POST['userLogin'] != NULL && strlen($_POST['userLogin']) >= 2 &&  strlen($_POST['userLogin']) <= 20) {

                $this->userName = $_POST['userLogin'];
            } else {
                $this->loginError = 2;
            }

            if ($_POST['userPassword'] != NULL && strlen($_POST['userPassword']) >= 4 &&  strlen($_POST['userPassword']) <= 20) {

                $this->userPassword = $_POST['userPassword'];
            } else {
                $this->loginError = 2;
            }

            // Filtrage de sécurité des variables envoyés par depuis le post.
            if ($this->loginError == 0) {
                $this->userName = strtolower(filter_var($this->userName, FILTER_SANITIZE_STRING));
                $this->userPassword = filter_var($this->userPassword, FILTER_SANITIZE_STRING);
            }

            if ($this->loginError == 0 && $this->userName == $this->secretUserName && $this->userPassword == $this->secretUserPassword) {

                $this->token = $this->createJWT();

                header("Authorization:" . "Bearer " . $this->token);
            } else {
                $this->loginError = 1;
            }
        }
    }
}
