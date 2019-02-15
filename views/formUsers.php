<?php
session_start();
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
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
<?php require_once('../components/nav.php') ?>
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
        <form method="post" action="../controllers/users_controller.php?action=register"" id="usersForm">
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
        <div class="column">
            $_SESSION
            <pre><?php print_r($_SESSION) ?></pre>
        </div>
    </div>
</div>
</body>
</html>
