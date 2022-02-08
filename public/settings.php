<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Template;
?>

<?= Template::load('header', ['title' => 'Settings', 'selected' => 'settings', 'keywords' => '', 'description' => 'init']);?>

    <section class="container">
        Settings
    </section>

<?= Template::load('footer');?>