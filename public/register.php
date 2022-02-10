<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Template;
    use app\users\User;

    if(User::isLoggedIn()) {
        header('location: ' . ROOT_PATH);
    }
?>

<?= Template::load('header', ['title' => 'Register', 'selected' => 'register', 'keywords' => '', 'description' => '', 'loggedInUser' => $loggedInUser]);?>

    <section class="container">
        Register
    </section>

<?= Template::load('footer');?>