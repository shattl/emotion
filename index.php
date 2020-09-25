<?php
require 'functions.php';
echo "Session file contains: <pre>".print_r($_SESSION,true)."</pre>";

if (isset($_POST['msisdn']) || isset($_POST['code'])) {
    if (isset($_POST['msisdn'])) {
        $_SESSION['msisdn'] = $_POST['msisdn'];
        $coderes = getCode($_POST['msisdn']);
        if ($coderes===true) {
            require_once 'template/code.html';
            exit();
        }
    }
    if (isset($_POST['code'])) {
        if (checkCode($_SESSION['msisdn'],$_POST['code'])===true) {
            $password = getPasswordAfterThat($_SESSION['msisdn']);
            echo "Ваш пароль: {$password}. Ваша сессия хранится в течении 2 минут, после чего она уничтожается. Пожалуйста, проверьте все настройки.";
            echo "<br>Для уничтожения сессии сейчас, нажмите <a href=\"/purge.php\">сюда</a>";
        }
    }
}
else {
    require_once 'template/index.html';
    exit();
}