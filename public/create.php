<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Template;
    use app\users\User;

    if(!User::isLoggedIn()) {
        header('Location: ' . ROOT_PATH);
    }
?>

<?= Template::load('header', ['title' => 'Create', 'selected' => 'create', 'keywords' => '', 'description' => 'init']);?>

    <section class="container">
        Create something
    </section>

<?= Template::load('footer');?>