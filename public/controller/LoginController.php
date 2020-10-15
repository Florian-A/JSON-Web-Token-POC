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

    private function base64_url_encode($data)
    {
        return strtr(base64_encode($data), '+/=', '-_,');
    }

    private function base64_url_decode($data)
    {
        return base64_decode(strtr($data, '-_,', '+/='));
    }

    private function createJWT()
    {
        // Créer une en-tête de jeton sous forme de chaîne JSON
        $header = json_encode(
            [
                "typ" => "JWT",
                "alg" => "HS256"
            ]
        );

        // Création d'une charge utile du token sous forme de chaîne JSON
        $payload = json_encode(
            [
                "iat" => time(),
                "exp" => time() + (60 * 60),
                "aswerOfAnythink" => 42
            ]
        );

        // Génération de la signature
        $signature = $this->generateJWTSignature($this->base64_url_encode($header), $this->base64_url_encode($payload));

        // Création du token JWT
        $jwt = $this->base64_url_encode($header) . "." . $this->base64_url_encode($payload) . "." . $this->base64_url_encode($signature);

        return $jwt;
    }

    public function generateJWTSignature($header, $payload)
    {
        // Création de la signature
        $signature = hash_hmac('sha256', $header . "." . $payload, $this->JWTHashingSecret, true);

        return $signature;
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
        $tokenSent = str_replace("Bearer ", '', $_SERVER['HTTP_AUTHORIZATION']);

        // Décomposition du token en un tableau avec destructuration.
        [$header, $payload, $signature] = explode(".", $tokenSent);
        $header = $this->base64_url_decode($header);
        $payload = $this->base64_url_decode($payload);
        $signature = $this->base64_url_decode($signature);

        $headerObject = json_decode($header);
        $payloadObject = json_decode($payload);

        // Re-création de la signature suivant le header et payload renvoyé par le token.
        $recalculatedSignature = $this->generateJWTSignature($this->base64_url_encode($header),$this->base64_url_encode($payload));
        
        // Récupération de la date d'expiration.
        $tokenExpirationTime = filter_var($payloadObject->exp, FILTER_SANITIZE_NUMBER_INT);

        // Test de la signature.
        if ($recalculatedSignature == $signature) {

            // Test de la date d'expiration.
            if ($tokenExpirationTime >= time()) {
                echo "Token JWT non expiré";
            } else {
                echo "Token JWT expiré !";
            }
        }
        else {
            echo "Token JWT avec une signature NON valide !";
        }

        // Retour de 1 afin de bloquer l'affichage de la vue.
        return 1;
    }
}
