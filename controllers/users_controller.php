<?php
require_once('../config_database/config_database.php');
//demarrage session
session_start();

// try/catch pour lever les erreurs de connexion
try {
    // on se connecte avec les acces,  IL FAUT QUE LA BASE EXISTE POUR MANIPULER
    $dbh = new PDO(
        'mysql:host=' . $db_config['host'] . ':' . $db_config['port'] . ';dbname=' . $db_config['schema'] . ";charset=" . $db_config['charset'],
        $db_config['user'],
        $db_config['password']
    );
    // tableau d'erreurs initial, vide
    $errors = [];

    $action = isset($_GET['action']) ? $_GET['action'] : '';

    switch ($action){
        case 'login':
            if (isset($_POST['userLogin'])){
                if (empty($_POST['userLogin'])) {
                    $errors[] = 'champ login vide';
                }else if (mb_strlen($_POST['userLogin']) > 45) {
                    $errors[] = 'champ login trop long (45max)';
                }else {
                    if (isset($_POST['userPassword'])) {
                        if (empty($_POST['userPassword'])) {
                            $errors[] = 'champ password vide';
                        } else {
                            $sth = $dbh->prepare('SELECT login, password FROM users WHERE login = :login');
                            if ($sth->execute(array(
                                ':login' => $_POST['userLogin']
                            ))) {
                                // success
                            }
                            $connect = $sth->fetch();
                            $hashed_password = $connect['password'];
                            if ($connect != null && password_verify($_POST["userPassword"], $hashed_password)) {
                                echo "Vous êtes connecté";
                                header('Location: ../controllers/users_controller.php?action=list');
                                die;
                            } else {
                                echo "Vous n'êtes pas connecté";
                            }
                        }
                    }
                }
            }
            // put errors in $session
            $_SESSION['errors'] = $errors;
            // redirect to login
            // on *redirige* vers la VIEW par defaut
            header('Location: ../views/users_login.php');
            break;
        case 'register':
            if (isset($_POST['userLogin'])){
                if (empty($_POST['userLogin'])) {
                    $errors[] = 'champ login vide';
                    // si name > 50 chars
                } else if (mb_strlen($_POST['userLogin']) > 50) {
                    $errors[] = 'champ login trop long (50max)';
                }
                //requete qui doit retourner des resultats
                $stmt = $dbh->query("select * from users");
                // recupere les users et fout le resultat dans une variable sous forme de tableau de tableaux
                $users = $stmt->fetchAll();
                foreach ($users as $user) {
                    if ($_POST['userLogin'] == $user['login']) {
                        $errors[] = 'login déjà existant';
                    }
                }
            }
            if (isset($_POST['userPassword'])){
                if (empty($_POST['userPassword'])) {
                    $errors[] = 'champ password vide';
                    // si name > 50 chars
                } else if (mb_strlen($_POST['userPassword']) < 8) {
                    $errors[] = 'champ password trop court (8min)';
                } else if (mb_strlen($_POST['userPassword']) > 20) {
                    $errors[] = 'champ password trop long (20max)';
                }
                $password = $_POST['userPassword'];
                $password = password_hash($password, PASSWORD_DEFAULT);
            }
            if (isset($_POST['userFirstname'])){
                if (empty($_POST['userFirstname'])) {
                    $errors[] = 'champ firstname vide';
                    // si name > 50 chars
                } else if (mb_strlen($_POST['userFirstname']) > 50) {
                    $errors[] = 'champ firstname trop long (50max)';
                }
            }
            if (isset($_POST['userLastname'])){
                if (empty($_POST['userLastname'])) {
                    $errors[] = 'champ lastname vide';
                    // si name > 50 chars
                } else if (mb_strlen($_POST['userLastname']) > 50) {
                    $errors[] = 'champ lastname trop long (50max)';
                }
            }
            /*
             *  insertion base de données
            */
            if(isset($_POST['userLogin']) && count($errors) == 0){
                // ben on insere dans la table message
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
                    header('Location: ../controllers/users_controller.php?action=list');
                    die;
                }else{
                    // ERROR
                    // put errors in $session
                    $errors['pas reussi a creer le user'];
                }
            }
            $_SESSION['errors'] = $errors;
            header('Location: ../views/formUsers.php');
            break;
        case 'list':
            // load users
            //requete qui doit retourner des resultats
            $stmt = $dbh->query("select * from users");
            // recupere les users et fout le resultat dans une variable sous forme de tableau de tableaux
            $users = $stmt->fetchAll(PDO::FETCH_CLASS);
            $_SESSION['users'] = $users;
            header('Location: ../views/users_list.php');
            break;
        default;
            header('Location: ../views/connection.php');
            break;
    }
} catch (Exception $e) {
    echo('cacaboudin exception');
    print_r($e);
}
?>
