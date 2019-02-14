<?php
// connection base mysql
$db_config = [
    'host'      => 'localhost', // machine, la machine locale s'appelle par convention "localhost"
    'schema'    => 'projet', // nom du schema
    'port'      => 3306, // 3306 est le port par defaut de mysql
    'user'      => 'mysqluser', // nom d'utilisateur
    'password'  => 'mysqlpassword', // mot de passe
    'charset'   => 'utf8mb4', // le charset utilisé pour communiquer avec mysql via PDO
];

// try/catch pour lever les erreurs de connexion

try{
    // on se connecte avec les acces,  IL FAUT QUE LA BASE EXISTE POUR MANIPULER
    $dbh = new PDO(
        'mysql:host='. $db_config['host'] .':'. $db_config['port'] .';dbname='. $db_config['schema'] .";charset=". $db_config['charset'],
        $db_config['user'],
        $db_config['password']
    );

    $errors = [];

    //requete qui doit retourner des resultats
    $stmt = $dbh->query("select * from users");
    // recupere les users et fout le resultat dans une variable sous forme de tableau de tableaux
    $users = $stmt->fetchAll();

    /*
     *  check/validation du formulaire
    */
    if (isset($_POST['userLogin'])){
        if (empty($_POST['userLogin'])) {
            $errors[] = 'champ user_id vide';
            // si name > 50 chars
        } else if (strlen($_POST['userLogin']) > 50) {
            $errors[] = 'champ user_id trop long (50max)';
        }
        foreach ($users as $user) {
            if ($_POST['userLogin'] == $user['login']) {
                $errors[] = 'login déjà existant';
            }
        }
    }
    if (isset($_POST['userFirstname'])){
        if (empty($_POST['userFirstname'])) {
            $errors[] = 'champ user_id vide';
            // si name > 50 chars
        } else if (strlen($_POST['userFirstname']) > 50) {
            $errors[] = 'champ user_id trop long (50max)';
        }
    }
    if (isset($_POST['userLastname'])){
        if (empty($_POST['userLastname'])) {
            $errors[] = 'champ user_id vide';
            // si name > 50 chars
        } else if (strlen($_POST['userLastname']) > 50) {
            $errors[] = 'champ user_id trop long (50max)';
        }
    }
    if (isset($_POST['userPassword'])){
        if (empty($_POST['userPassword'])) {
            $errors[] = 'champ user_id vide';
            // si name > 50 chars
        } else if (strlen($_POST['userPassword']) < 8) {
            $errors[] = 'champ user_id trop court (8min)';
        } else if (strlen($_POST['userPassword']) > 20) {
            $errors[] = 'champ user_id trop long (20max)';
        }
        $password = $_POST['userPassword'];
        $password = password_hash($password, PASSWORD_DEFAULT);
    }

    // tableau d'erreurs initial, vide
    // test simple pour verifier que le champ $_POST['user_id'] existe ET (&&) contient une valeur
    // verifier qu'il existe ca permet de ne pas avoir le message au premier chargement de page

    /*
     *  insertion base de données
    */
    if(isset($_POST['userLogin']) && count($errors) == 0){
        // ben on insere dans la table message
        // la synaxe ":user_id" ca veut dire qu'on prepare la requete et que juste quand on la lance, on va remplacer ":user_id" par la bonne valeur.

        /* syntaxe avec preparedStatements */
        $sql = "insert into users (login, password, firstname, lastname) values (:login, :password, :firstname, :lastname)";
        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        if($sth->execute(array(
            ':login' => $_POST['userLogin'],
            ':password' => $password,
            ':firstname' => $_POST['userFirstname'],
            ':lastname' => $_POST['userLastname']
        ))){
            // success
        }
    }
}catch (Exception $e){
    echo('cacaboudin exception');
    print_r($e);
}
?>

<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://puteborgne.sexy/_css/normalize.css" />
    <link rel="stylesheet" href="https://puteborgne.sexy/_css/skeleton.css" />
    <style>
        fieldset {
            border: 0.25rem solid rgba(225,225,225,0.5);
            border-radius: 4px;
            padding: 1rem 2rem;
        }
        .errors {
            color: #ff5555;
        }
    </style>
</head>

<body>
<div class="container">

    <div class="row">
        <h1>formulaire ajout user</h1>
        <ul class="errors">
            <?php
            foreach( $errors as $error) {
                echo("<li>". $error . "</li>");
            }
            ?>
        </ul>
        <form method="post" action="" id="messageForm">
            <fieldset>
                <legend>user</legend>
                <label for="userLogin">Login</label>
                <input type="text" id="userLogin" name="userLogin"/>
                <label for="userPassword">Password</label>
                <input type="text" id="userPassword" name="userPassword"/>
                <label for="userFirstname">Firstname</label>
                <input type="text" id="userFirstname" name="userFirstname"/>
                <label for="userLastname">Lastname</label>
                <input type="text" id="userLastname" name="userLastname"/>
            </fieldset>
            <input type="submit" value="Envoyer" class="button-primary">
        </form>
    </div>

    <div class="row">
        <h2>Users</h2>
        <table class="u-full-width">
            <thead>
            <tr>
                <th>Login</th>
                <th>Password</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Created</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($users as $user) {
                ?>
                <tr>
                    <td><?= $user['login'] ?></td>
                    <td><?= $user['password'] ?></td>
                    <td><?= $user['firstname'] ?></td>
                    <td><?= $user['lastname'] ?></td>
                    <td><?= $user['created'] ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
