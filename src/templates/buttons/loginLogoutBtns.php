<?php
    use app\users\User;
?>

<?php if(!User::isLoggedIn()):?>
    <a class="mx-2 link-light text-decoration-none" href="<?= ROOT_PATH;?>login">Login</a> |
    <a class="mx-2 link-light text-decoration-none" href="<?= ROOT_PATH;?>register">Register</a>
<?php else:?>
    <a class="mx-2 link-light text-decoration-none" href="<?= ROOT_PATH;?>logout">Logout</a> |
    <a class="mx-2 link-light text-decoration-none" href="<?= ROOT_PATH;?>account">Account</a>
<?php endif;?>