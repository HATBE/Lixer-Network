<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Template;
    use app\users\User;
    use app\Sanitize;

    if(!User::isLoggedIn()) {
        header('Location: ' . ROOT_PATH);
    }

    $cards = ['text', 'video', 'image'];
    $card = (isset($url[0])) && in_array($url[0], $cards) ? Sanitize::string($url[0]) : 'text';
?>

<?= Template::load('header', ['title' => "Create - {$card}", 'selected' => 'create', 'keywords' => '', 'description' => 'init', 'loggedInUser' => $loggedInUser]);?>

    <section class="container">
    <div class="row d-flex justify-content-center overflow-hidden">
        <div class="col-12 col-md-7 row d-flex justify-content-center">
            <?php if($card == 'text'): ?>
                <?= Template::load('forms/create-text', ['loggedInUser' => $loggedInUser]);?>
            <?php elseif($card == 'video'): ?>
                Video
            <?php elseif($card == 'image'): ?>
                Image
            <?php else: ?>
                Not found
            <?php endif; ?>
        </div>
    </div>
    </section>

<?= Template::load('footer');?>