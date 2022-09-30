<div class="topbar">
    <center>
        <a href="/">[ Home ]</a>
        <?php if(isset($_SESSION["authenticated"])) echo("<a href=\"/leaderboard\">[ Leaderboard ]</a>"); ?>
        <?php if(isset($_SESSION["authenticated"])) echo("<a href=\"/logout\">[ Logout ]</a>"); ?>
        <?php if(!isset($_SESSION["authenticated"])) echo("<a href=\"/register\">[ Register ]</a>"); ?>
        <?php if(!isset($_SESSION["authenticated"])) echo("<a href=\"/login\">[ Login ]</a>"); ?>
    </center>
</div>
