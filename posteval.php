<?php
session_start();

if($_POST['captcha']!=$_SESSION['captcha_code']){
    echo "ERRORcaptcha|Wrong captcha! The correct value is (".$_SESSION['captcha_code'].")";
}else{
    echo "HURRAH!!!|Right captcha";
}
?>