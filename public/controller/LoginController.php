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

    public function check()
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

    public function test()
    {

        // Récupération du token sous format plain.
        $token = str_replace("Bearer ", '', $_SERVER['HTTP_AUTHORIZATION']);

        // Décomposition du token en un tableau.
        $tokentoArray = explode(".", $token);

        // Assignation du haut de page, de la charge utile et de la signature du token.
        $header = base64_decode($tokentoArray[0]);
        $payload = base64_decode($tokentoArray[1]);
        $signature = base64_decode($tokentoArray[2]);

        $headerObject = json_decode($header);
        $payloadObject = json_decode($payload);


        // Recréation de la signature JWT afin de vérifier qu'elle est valide.
        // $recalculatedSignature = hash_hmac('sha256', $header . "." . $payload, $this->JWTHashingSecret, true);
        // $base64UrlRecalculatedSignature = str_replace(['+', '/', '='], ['-', '_', ''], $recalculatedSignature);

        // // Test de la signature.
        // if($base64UrlRecalculatedSignature == $signature )
        // {
        //     echo "Signature ok";
        // }

        // Récupération de la date d'éxpiration.
        $tokenExpirationTime = filter_var($payloadObject->exp, FILTER_SANITIZE_NUMBER_INT);

        if ($tokenExpirationTime >= time()) {
            echo "Token non expiré !";
        } else {
            echo "Token expiré !";
        }

        // Retour de 1 afin de bloquer l'affichage de la vue.
        return 1;
    }
}
