<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Template;
?>

<?= Template::load('header', ['title' => 'Home', 'selected' => 'index', 'keywords' => '', 'description' => 'init']);?>

    <section class="container">
        Hello, i am 1 website
    </section>

<?= Template::load('footer');?>