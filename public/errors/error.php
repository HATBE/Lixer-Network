<?php
    require_once(__DIR__ . '/../../src/init.php');

    use app\Template;

    $errors = [
        '404' => "We're sorry, the resource you want to access, was not found on our servers.",
        '403' => "We're sorry, you don't have permission to access this resource.",
        '500' => "Internal server error",
        'unknown' => 'Error Unknown.'
    ];

    $error = $errors['unknown'];
    $errorId = 'unk';

    if(isset($_GET['n'])) {
        if(isset($errors[htmlspecialchars($_GET['n'])])) {
            $error = $errors[htmlspecialchars($_GET['n'])];
            $errorId = htmlspecialchars($_GET['n']);
        }
    }
?>

<?= Template::load('header', ['title' => "Error ${errorId}", 'selected' => 'error', 'keywords' => '', 'description' => '']);?>

    <style>
        main {
            background-image: url('<?= ROOT_PATH?>assets/img/stc/network.png');
            object-fit: cover;
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            filter: brightness(0.7);
            filter: grayscale(0.5);
        }
    </style>

    <section class="container d-flex justify-content-center">
        <div class="p-5 m-5 col-md-6">
            <h1 class="glitch">
                <span aria-hidden="true">Error <?= $errorId;?></span>
                Error <?= $errorId;?>
                <span aria-hidden="true">Error <?= $errorId;?></span>
            </h1>
            <h6 style="line-height:30px !important; text-shadow: 2px 2px 10px #030303;" ><?= $error;?></h6>
        </div>
    </section>

<?= Template::load('footer');?>