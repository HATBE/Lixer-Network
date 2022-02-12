<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Template;
    use app\users\User;
    use app\Sanitize;
    use app\posts\Post;

    if(!User::isLoggedIn()) {
        header('Location: ' . ROOT_PATH);
    }

    $cards = ['text', 'video', 'image'];
    $card = (isset($url[0])) && in_array($url[0], $cards) ? Sanitize::string($url[0]) : 'text';
    $id = isset($url[1]) ? Sanitize::int($url[1]) : null;
?>

<?= Template::load('header', ['title' => 'Content', 'selected' => 'content', 'keywords' => '', 'description' => 'init', 'loggedInUser' => $loggedInUser]);?>

    <section class="container">
        <?php if($id != null): ?>
            <?php if($card == 'text'): ?> 
                <?php $post = new Post($db, $id);?>
                <?php if($post->exists()):?>
                    <div class="row d-flex justify-content-center overflow-hidden">
                        <div class="col-12 col-md-7 row d-flex justify-content-center">
                            <?= Template::load('posts/post', ['post' => $post]);?>
                        </div>
                    </div>
                <?php else:?>
                    Post not found
                <?php endif;?>
            <?php elseif($card == 'video'): ?>
                Video
            <?php elseif($card == 'image'): ?>
                Image
            <?php else: ?> 
                Card not found
            <?php endif;?>
        <?php else: ?>
            Please provide an id
        <?php endif;?>
    </section>

<?= Template::load('footer');?>