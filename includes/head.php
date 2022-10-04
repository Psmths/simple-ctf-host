<link rel='stylesheet' type='text/css' href='/stylesheets/primary-stylesheet.css'>
<title>
    <?php 
        if (empty(PAGE_TITLE)){
            echo(CTF_NAME); 
        } else {
            echo(CTF_NAME." - ".PAGE_TITLE); 
        }
    ?>
</title>