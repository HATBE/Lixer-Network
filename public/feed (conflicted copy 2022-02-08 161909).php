<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Template;
?>

<?= Template::load('header', ['title' => 'Feed', 'selected' => 'feed', 'keywords' => '', 'description' => 'init']);?>

    <section class="container">
        This is a feed
    </section>

<?= Template::load('footer');?>