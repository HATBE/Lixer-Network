<?php
    use app\Template;
?>

<div class="row">
    <div class="col-4 text-center">
        <?= Template::load('user/avatar', ['user' => $post->getUser()]);?>
        <?= Template::load('user/usernameLink', ['user' => $post->getUser()])?>
    </div>
    <div class="col-8">
        <div class="p-4"><?= $post->getText();?></div>
        <div class="ps-4"><?= $post->getTime();?></div>
    </div>
</div>