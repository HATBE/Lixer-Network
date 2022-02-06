<?php
    use app\Template;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta property="og:locale" content="<?= PAGE_LANG;?>">
        <meta property="og:title" content="<?= $title?> - <?= PAGE_TITLE;?>">
        <meta property="og:site_name" content="<?= PAGE_TITLE;?>">
        <meta property="og:type" content="website">
        <meta property="og:description" content="<?= $description;?>">
        <meta property="og:url" content="<?= PAGE_URL;?>">
        <meta property="og:image" content="<?= PAGE_FAVICON;?>">

        <meta name="description" content="<?= $description?>">
        <meta name="keywords" content="<?= DEFAULT_KEYWORDS?>, <?= $keywords?>">
        <meta name="author" content="Aaron Gensetter">

        <link rel="stylesheet" href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= ROOT_PATH;?>assets/css/style.css">

        <link rel="icon" href="<?= PAGE_FAVICON;?>" type="image/x-icon">
        <link rel="apple-touch-icon" href="<?= PAGE_FAVICON;?>">

        <title><?= $title;?> - <?= PAGE_TITLE;?></title>
    </head>
    <body class="bg-primary">
        <?php if(!isset($noparts)):?>
        <header class="bg-primary text-light p-3">
            <div class="container d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 p-0 m-0">
                        <a class="link-light text-decoration-none" href="<?= ROOT_PATH;?>">
                            <?= PAGE_TITLE;?>
                        </a>
                    </h1>
                </div>
                <div class="w-50">
                    <form action="<?= ROOT_PATH;?>search" method="GET" class="input-group">
                        <input type="search" class="form-control" placeholder="Search...">
                        <button type="submit" class="btn btn-outline-light">Search</button>
                    </form>
                </div>
                <div>
                    <?= Template::load('buttons/loginLogoutBtns');?>
                </div>
            </div>
        </header>
        <main class="bg-light py-4 px-3">
        <?php endif;?>
        
