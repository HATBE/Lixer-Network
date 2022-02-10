<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Template;
    use app\users\User;

    if(!User::isLoggedIn()) {
        header('Location: ' . ROOT_PATH);
    }
?>

<?= Template::load('header', ['title' => 'Report', 'selected' => 'report', 'keywords' => '', 'description' => 'init', 'loggedInUser' => $loggedInUser]);?>

    <section class="container">
        Report
    </section>

<?= Template::load('footer');?>