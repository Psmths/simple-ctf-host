<hr>
<?php 
    if(isset($_SESSION["authenticated"])) {
        echo("<small>Logged in as: ".$_SESSION["username"]." Last Login: ".$_SESSION["last_logon"]."</small>");
    }
?>