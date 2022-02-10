<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Template;
    use app\users\User;

    if(User::isLoggedIn()) {
        header('Location: ' . ROOT_PATH . 'feed');
    }
?>

<?= Template::load('header', ['title' => 'Home', 'selected' => 'index', 'keywords' => '', 'description' => 'init', 'loggedInUser' => $loggedInUser]);?>

    <section class="container">
        Hello, i am 1 website
    </section>

<?= Template::load('footer');?>