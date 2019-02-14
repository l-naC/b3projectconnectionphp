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
    /*
     *  check/validation du formulaire
    */
    $sth=  $dbh->prepare('SELECT login, password FROM users WHERE login = :login');
    if($sth->execute(array(
        ':login' => $_POST['userLogin']
    ))){
        // success
    }
    $connect = $sth->fetch();
    $hashed_password = $connect['password'];
    if ($connect != null && password_verify($_POST["userPassword"],$hashed_password)){
        echo "Vous êtes connecté";
    }else{
        echo "Vous n'êtes pas connecté";
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
        <h1>formulaire connection user</h1>
        <form method="post" action="" id="messageForm">
            <fieldset>
                <legend>Connection</legend>
                <label for="userLogin">Login</label>
                <input type="text" id="userLogin" name="userLogin"/>
                <label for="userPassword">Password</label>
                <input type="text" id="userPassword" name="userPassword"/>
            </fieldset>
            <input type="submit" value="Envoyer" class="button-primary">
        </form>
    </div>

    <div class="row">

    </div>
</div>
</body>
</html>
