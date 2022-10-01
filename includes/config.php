<?php
    define('CTF_NAME', 'SimpleCTF');
    define('FILE_STORE_DIRECTORY', '/tmp/simplectf/');

    define('MIN_PASSWORD_LENGTH', 10);
    define('MIN_USERNAME_LENGTH', 5);
    define('MAX_USERNAME_LENGTH', 15);
    define('USERNAME_REGEX', '/^[a-zA-Z0-9]+$/');
    define('LOGIN_FAILED_ERROR', 'Failed to authenticate.');

    define('MIN_CHALLENGE_DIFFICULTY', 1);
    define('MAX_CHALLENGE_DIFFICULTY', 5);

    define('DB_SERVER', '127.0.0.1');
    define('DB_USERNAME', 'username');
    define('DB_PASSWORD', 'password');
    define('DB_NAME', 'simplectfdb');
?>