<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Sanitize;
    use app\Template;
    use app\users\User;

    $id = isset($url[0]) ? Sanitize::int($url[0]) : null;
    $cards = ['profile', 'posts'];
    $card = (isset($url[1])) && in_array($url[1], $cards) ? Sanitize::string($url[1]) : 'profile';

    $user = new User($db, $id);

    if(!$user->exists()) {
        http_response_code(404);
        $title = "User not found";
    } else {
        $title = "{$user->getUsername()}";
    }
?>

<?= Template::load('header', ['title' => $title, 'selected' => 'account', 'keywords' => '', 'description' => 'init', 'loggedInUser' => $loggedInUser]);?>

    <?php if(isset($url[0])):?>
        <section class="container">
            <?php if($user->exists()): ?>
                <div class="row">
                    <div class="col-12 col-md-4 col-sm-10 offset-md-0 offset-sm-1">
                        <?= Template::load('user/card', ['user' => $user, 'loggedInUser' => $loggedInUser]);?>
                    </div>
                    <div class="col-12 col-md-8 col-sm-10 offset-md-0 offset-sm-1">
                        <div class="card">
                            <div class="d-flex user-select-none card-header bg-primary text-light">
                                <ul class="nav">
                                    <li class="nav-item rounded <?= $card == 'profile' ? 'bg-dark-primary' : ''?>">
                                        <a class="nav-link hover-text-light link-light" href="<?= ROOT_PATH?>users/<?=$user->getId();?>/profile">Profile</a>
                                    </li>
                                    <li class="nav-item rounded <?= $card == 'posts' ? 'bg-dark-primary' : ''?> ">
                                        <a class="nav-link hover-text-light link-light" href="<?= ROOT_PATH?>users/<?=$user->getId();?>/posts">Posts</a>
                                    </li>
                                    <li class="nav-item rounded <?= $card == 'other' ? 'bg-dark-primary' : ''?>">
                                        <a class="nav-link hover-text-light link-light" href="<?= ROOT_PATH?>users/<?=$user->getId();?>/other">Other</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <?php if($card == 'profile'):?>
                                    Welcome, this is my profile
                                <?php elseif($card == 'posts'):?>
                                    posts
                                <?php else:?>
                                    Card not found
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else:?>
                User not found
            <?php endif;?>
        </section>
    <?php else:?>
        <section class="container">
            User list
        </section>
    <?php endif;?>

<?= Template::load('footer');?>