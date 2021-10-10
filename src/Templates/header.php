<?php
    // $title
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Aaron Gensetter">
		<meta name="keywords" content="">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="/assets/css/style.css">

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>        <script src="https://kit.fontawesome.com/319cbe1b39.js" crossorigin="anonymous"></script>

        <title><?= SITE_NAME . ' | ' . $title ?></title>
    </head>
    <body>
    <nav class="navbar sticky-top navbar-dark bg-dark">
        <div class="container">
            <a class=" navbar-brand" href="<?= ROOT_PATH;?>"><?= SITE_NAME;?></a>
            <?php if(App\User\User::isLoggedIn()):?>
                <div class="btn-group dropstart">
                    <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= App\Auth\UserSession::getUser()->getUsername();?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= ROOT_PATH . '/users/profile';?>">Profile</a></li>
                        <li><a class="dropdown-item" href="<?= ROOT_PATH . '/users/settings';?>">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-primary" href="<?= ROOT_PATH . '/auth/logout';?>">Logout</a></li>
                    </ul>
                </div>


            <?php else:?>
                <a href="<?= ROOT_PATH;?>/auth/login">
                    <button class="btn btn-sm btn-outline-primary" type="button">Login</button>
                </a>
            <?php endif;?>
        </div>
    </nav>
    <div class="container">
 
