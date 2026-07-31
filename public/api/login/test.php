<?php

$password = "";

if (isset($_GET['s'])) {
    if ($_GET['s'] !== "") {
        $password = $_GET['s'];
    } else {
        echo '参数s不能为空';
        return;
    }
} else {
    echo '请传入参数s';
    return;
}

$hashed_password = password_hash($password, PASSWORD_BCRYPT);
echo $hashed_password;

?>