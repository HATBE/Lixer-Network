<?php
    use app\users\User;
    use app\Template;
?>

<nav role="main-nav" class="fs-28 d-flex align-items-center h-100">
<?php if(User::isLoggedIn()):?>
    <span class="mx-1 dropdown">
        <div title="Add" class="cursor-pointer link-light hover-text-light" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-plus-circle"></i></div>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= ROOT_PATH?>create/text"><span style="display: inline-block;width: 23px;"><i class="fas fa-align-left"></i></span> Text Post</a></li>
            <li><a class="dropdown-item" href="<?= ROOT_PATH?>create/video"><span style="display: inline-block;width: 23px;"><i class="fas fa-video"></i></span> Uplaod Video</a></li>
            <li><a class="dropdown-item" href="<?= ROOT_PATH?>create/image"><span style="display: inline-block;width: 23px;"><i class="fas fa-image"></i></span> Upload Image</a></li>
        </ul>
    </span>
    <a title="Chat" class="mx-1 link-light hover-text-light" href="<?= ROOT_PATH;?>chat"><i class="fas fa-comment"></i></a>
    <span class="mx-1 dropdown">
        <div title="Notifications" class="position-relative cursor-pointer link-light hover-text-light" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-bell"></i></div>
        <small style="font-size: 10px; top:0px; right: -5px; width: 15px; height: 15px;" class="position-absolute bg-danger rounded-circle d-flex justify-content-center align-items-center">9+</small>
        <ul class="dropdown-menu">
            Notifications
        </ul>
    </span>
    <span class="rounded-circle mx-1 dropdown">
        <div style="height: 28px; width: 28px; font-size: 0;" title="User menu" class="cursor-pointer overflow-hidden" data-bs-toggle="dropdown" aria-expanded="false"><?= Template::load('user/avatar', ['user' => $loggedInUser]);?></div>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= ROOT_PATH?>dashboard"><span style="display: inline-block;width: 23px;"><i class="fas fa-tachometer-alt"></i></span> Dashboard</a></li>
            <li><a class="dropdown-item" href="<?= ROOT_PATH?>settings"><span style="display: inline-block;width: 23px;"><i class="fas fa-cog"></i></span> Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?= ROOT_PATH?>logout"><span style="display: inline-block;width: 23px;"><i class="fas fa-sign-out-alt"></i></span> Logout</a></li>
        </ul>
    </span>
<?php else:?>
    <a title="Login" class="mx-1 link-light hover-text-light" href="<?= ROOT_PATH;?>login"><i class="fas fa-sign-in-alt"></i></a>
<?php endif;?>
    <span class="mx-1 dropstart">
        <div title="Search" class="position-relative cursor-pointer link-light hover-text-light" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-search"></i></div>
        <ul class="dropdown-menu p-0">
            <form method="get" class="btn-group">
                <input id="searchbar" name="searchterm" style="width: 300px; min-width: 75px;" class="rounded-0 form-control" type="text" placeholder="Search...">
                <button type="subbmit" class="rounded-0 btn btn-outline-primary"><i class="fas fa-search"></i></button>
            </form>
        </ul>
</span>
</nav>