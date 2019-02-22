<?php
require_once('../models/users_class.php');
//demarrage session
session_start();

// try/catch pour lever les erreurs de connexion
try {


    $action = isset($_GET['action']) ? $_GET['action'] : '';

    $user = new User();

    switch ($action){
        case 'login':
            if ($user->login($_POST)){
                $_SESSION['errors'] = [];
                $users = $user->findAll();
                $_SESSION['users'] = $users;
                header('Location: ../views/users_list.php');
                die;
            }
            // put errors in $session
            $_SESSION['errors'] = $user->errors;
            header('Location: ../views/connection.php');
            break;

        case 'list':
            $_SESSION['errors'] = [];
            $users = $user->findAll();
            $_SESSION['users'] = $users;
            header('Location: ../views/users_list.php');
            break;
        case 'jsonlist':
            $users = $user->findAll();
            header("Access-Control-Allow-Origin: *");
            header('Content-type: application/json; charset=UTF-8');
            echo json_encode($users);
            break;

        case 'register';
            if ($user->save($_POST)){
                $_SESSION['errors'] = [];
                header('Location: ../views/users_list.php');
                die;
            }
            $_SESSION['errors'] = $user->errors;
            header('Location: ../views/formUsers.php');
            break;
        default:
            header('Location: ../views/connection.php');
            break;
    }
} catch (Exception $e) {
    echo('cacaboudin exception');
    print_r($e);
}
?>