<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Template;
?>

<?= Template::load('header', ['title' => 'Dashboard', 'selected' => 'dashboard', 'keywords' => '', 'description' => 'init']);?>

    <section class="container">
        This is my dashboard
    </section>

<?= Template::load('footer');?>