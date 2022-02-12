<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\Template;
    use app\users\User;

    if(User::isLoggedIn()) {
        header('location: ' . ROOT_PATH);
    }
?>

<?= Template::load('header', ['title' => 'Login', 'selected' => 'login', 'keywords' => '', 'description' => '', 'loggedInUser' => $loggedInUser]);?>

    <section class="container">
        <div class="row d-flex justify-content-center overflow-hidden">
            <div class="col-12  col-sm-7 col-md-6 col-lg-5 my-5">
                <h2 class="h4 fw-bold my-4">LOGIN</h2>
                <div class="alert d-none" id="login-alert"></div>
                <form id="login-form">
                    <div class="form-floating mb-3">
                        <input class="form-control form-control-lg" id="login-username" name="username" type="text" placeholder="Username">
                        <label for="username">Username</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control form-control-lg" id="login-password" name="password" type="password" placeholder="Password">
                        <label for="password">Password</label>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Login</button>
                </form>
                <div class="text-center mt-3">
                    <small class="text-muted"><a class="link-dark text-decoration-none" href="<?= ROOT_PATH;?>forgot-password">Forgot password?</a></small>
                </div>
                <div class="text-center mt-3 d-flex flex-column justify-content-center">
                    <span>Don't have an account?</span>
                    <a class="w-100" href="<?= ROOT_PATH;?>register">
                        <button class="text-decoration-none btn btn-outline-primary my-2 fw-bold btn-sm w-100" type="button">CREATE ONE</button>
                    </a>
                </div>
            </div>
            <div class="md-none p-0 col-12 col-sm-6 col-md-5 col-lg-4 bg-primary text-light d-flex justify-content-center align-items-center rounded overflow-hidden">
                <img draggable="false" class="img-filter-darken position-relative object-fit-cover w-100 h-100" src="<?= ROOT_PATH;?>assets/img/stc/network.png" alt="">
                <span class="user-select-none position-absolute h1">Welcome to <?= PAGE_TITLE;?></span>
            </div>
        </div>
    </section>

<?= Template::load('footer');?>