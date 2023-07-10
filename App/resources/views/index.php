<?php
    use Session\Login;

    Login::requiredLogin();

    include dirname(__DIR__).'/views/form.php';
?>