<div class="container">

    <?php if ($LoginController->loginError == 1) {
        // Affichage des erreurs.
    ?>
        <br>
        <div class="alert alert-danger" role="alert">
            Erreur d'association entre le nom d'utilisateur et le mot de passe.
        </div>
    <?php
    }
    if ($LoginController->loginError == 0 && $_POST) {
        // Affichage du token JWT
    ?>
        <br>
        <div class="alert alert-info" role="alert">
            Votre token JWT : <code><?php echo $LoginController->token; ?></code>
        </div>
    <?php
    } ?>

    <br>
    <form action="" method="post">
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="userLogin">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="userLogin" name="userLogin" placeholder="" value="" required="true" minlength="2" maxlength="20">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="userPassword">Mot de passe</label>
                <input type="password" class="form-control" id="userPassword" name="userPassword" placeholder="" value="" required="true" minlength="4" maxlength="20">
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <button class="btn btn-primary m-auto" type="submit">Connexion</button>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col text-center mt-3">
            <a href="#" class="test-jwt">Tester le JWT</a>
        </div>
    </div>

</div>

<script>
    // Fonction de déclanchement de la requete Ajax de test.
    function get(url,jwt) {
        return new Promise((resolve, reject) => {
            const req = new XMLHttpRequest();
            req.open('GET', url);
            req.setRequestHeader("Authorization", "Bearer "+jwt);
            req.onload = () => req.status === 200 ? resolve(req.response) : reject(Error(req.statusText));
            req.onerror = (e) => reject(Error(`Network Error: ${e}`));
            req.send();
        });
    }

    document.querySelector(".test-jwt").addEventListener("click", async () =>  {
        jwt = await localStorage.getItem('jwt');
        reponse = await get('http://localhost/index.php?LoginController&test',jwt);
        alert(reponse);
    });
</script>

<?php if ($LoginController->loginError == 0 && $_POST) {
    // Assignation du token dans le local storage.
?>
    <script>
    localStorage.setItem("jwt", "<?php echo $LoginController->token ?>");
    </script>
<?php
}
?>