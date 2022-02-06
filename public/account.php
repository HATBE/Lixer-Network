<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Template;
?>

<?= Template::load('header', ['title' => 'Account', 'selected' => 'account', 'keywords' => '', 'description' => 'init']);?>

    <section class="container">
        Account
    </section>

<?= Template::load('footer');?>