<?php

function log_out() {
    unset($_SESSION['username']);
    unset($_SESSION['last_login']);
    unset($_SESSION['login_expires']);

    $_SESSION = array();

    session_destroy();

    header('Location: index.php');
    exit();
}

function last_login_is_recent() {
    $max_time_elapsed = 60 * 60;

    if (!isset($_SESSION['last_login'])) {
        return FALSE;
    }

    return ($_SESSION['last_login'] + $max_time_elapsed) >= time();
}

function login_is_still_valid() {
    if (!isset($_SESSION['login_expires'])) {
        return FALSE;
    }

    return ($_SESSION['login_expires']) >= time();
}

?>