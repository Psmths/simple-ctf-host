<?php

    require_once "../includes/config.php";
    require_once "../includes/db.php";

    if (isset($_POST['username'], $_POST['password'])) { 
        register();
    }

    function register() {

        // Sanitize client input
        $client_username = trim(filter_var($_POST['username'], FILTER_SANITIZE_STRING));
        $client_password = trim(filter_var($_POST['password'], FILTER_SANITIZE_STRING));

        // Check if registration form was completed
        if (empty($client_username) || empty($client_password)) {
            $_SESSION['return_msg'] = "You must supply a username and password!";
            return;
        }

        // Check if username is valid
        if (preg_match(USERNAME_REGEX, $client_username) == 0) {
            $_SESSION['return_msg'] = "Username is not valid!";
            return;
        }

        // Check if password meets length requirements
        if (strlen($client_password) < MIN_PASSWORD_LENGTH) {
            $_SESSION['return_msg'] = "Password must be more than ".MIN_PASSWORD_LENGTH." characters long!";
            return;
        }

        // Check if username meets length requirements
        if (strlen($client_username) < MIN_USERNAME_LENGTH || strlen($client_username) > MAX_USERNAME_LENGTH ) {
            $_SESSION['return_msg'] = "Username must be between ".MIN_USERNAME_LENGTH." and ".MAX_USERNAME_LENGTH." characters long!";
            return;
        }

        // Check if user already exists
        $sql = 'SELECT * FROM accounts WHERE username=:username';
        $statement = db()->prepare($sql);
        $statement->bindValue(':username', $client_username, PDO::PARAM_STR);
        $statement->execute();
        if ($statement->rowCount() != 0) {
            $_SESSION['return_msg'] = "This username is taken!";
            return;
        }
        
        // Hash the password
        $client_hashed_password = password_hash($client_password, PASSWORD_BCRYPT);

        // Get timestamp
        $timestamp = date('Y-m-d H:i:s');
        $timestamp = $timestamp->format('c');
        
        // Connect to database and add the new account
        $sql = 'INSERT INTO accounts(username, password_hash, registration_time, last_logon) VALUES(:username, :password_hash, :registration_time, :last_logon)';
        $statement = db()->prepare($sql);
        $statement->bindValue(':username', $client_username, PDO::PARAM_STR);
        $statement->bindValue(':password_hash', $client_hashed_password, PDO::PARAM_STR);
        $statement->bindValue(':registration_time', $timestamp, PDO::PARAM_STR);
        $statement->bindValue(':last_logon', $timestamp, PDO::PARAM_STR);
        $statement->execute();

        $_SESSION['return_msg'] = "Registration successful! You may continue to <a href=\"/login\">login</a>.";
    }
?>
<!doctype html>
<html lang='en-US'>
    <head>
        <title>Simple CTF Framework - Register</title>
        <?php include("../includes/html-head.html") ?>
    </head>
    <body>

    <?php include("../includes/header.html") ?>
    <?php include("../includes/topbar.php") ?>
    
    <h2>Register for Simple CTF Framework</h2>

    <center>
        <form action="/register" method="post" autocomplete="off" style='display: inline'>
            <input type="text" name="username" placeholder="Username" id="username"><br><br>
            <input type="password" name="password" placeholder="Password" id="password"><br><br>
            <input type="submit" value="Register" style='display: inline'><br><br>
        </form>
    </center>

    <?php
        if(!empty($_SESSION['return_msg'])){
            echo("<center><span>".$_SESSION['return_msg']."</span></center>");
            unset($_SESSION['return_msg']);
        }
    ?>

    <?php include("../includes/footer.php") ?>

    </body>
</html>