<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Template;
    use app\users\User;

    if(!User::isLoggedIn()) {
        header('Location: ' . ROOT_PATH);
    }
?>

<?= Template::load('header', ['title' => 'Dashboard', 'selected' => 'dashboard', 'keywords' => '', 'description' => 'init']);?>

    <section class="container">
        This is my dashboard
    </section>

<?= Template::load('footer');?>