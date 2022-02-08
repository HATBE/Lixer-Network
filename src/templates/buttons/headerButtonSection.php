<?php
    use app\users\User;
?>

<div class="user-select-none h4 mb-0 d-flex">
<?php if(!User::isLoggedIn()):?>
    <div class="bg-dark-primary px-2 py-1 rounded-pill d-flex mx-1">
        <a title="Login" class="mx-1 link-light text-decoration-none hover-text-light" href="<?= ROOT_PATH;?>login"><i class="fas fa-sign-in-alt"></i></a>
    </div>
<?php else:?>
    <div class="bg-dark-primary px-2 py-1 rounded-pill d-flex mx-1">
        <span class="dropdown">
            <div title="Add" class="position-relative mx-1 cursor-pointer hover-text-light" id="searchmenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-plus-circle"></i>
            </div>
            <ul class="user-select-auto dropdown-menu">
                <li><a class="dropdown-item" href="<?= ROOT_PATH?>create/text"><span style="display: inline-block;width: 23px;"><i class="fas fa-align-left"></i></span> Text</a></li>
                <li><a class="dropdown-item" href="<?= ROOT_PATH?>create/video"><span style="display: inline-block;width: 23px;"><i class="fas fa-video"></i></span> Video</a></li>
                <li><a class="dropdown-item" href="<?= ROOT_PATH?>create/image"><span style="display: inline-block;width: 23px;"><i class="fas fa-image"></i></span> Image</a></li>
            </ul>
        </span>
    </div>
    <div class="bg-dark-primary px-2 py-1 rounded-pill d-flex mx-1">
        <a title="Dashboard" class="mx-1 link-light text-decoration-none hover-text-light" href="<?= ROOT_PATH;?>dashboard"><i class="fas fa-tachometer-alt"></i></a>
        <a title="Chat" class="mx-1 link-light text-decoration-none hover-text-light" href="<?= ROOT_PATH;?>chat"><i class="fas fa-comment"></i></a>
        <span class="dropdown">
            <div title="Notifications" class="position-relative mx-1 cursor-pointer hover-text-light" id="searchmenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bell"></i>
                <small style="font-size: 10px; top:-5px; right: -5px; width: 15px; height: 15px;" class="position-absolute bg-danger rounded-circle d-flex justify-content-center align-items-center">9+</small>
            </div>
            <ul class="user-select-auto dropdown-menu">
                Here are notifications
            </ul>
        </span>
    </div>
    <div class="bg-dark-primary px-2 py-1 rounded-pill d-flex mx-1">
        <a title="Settings" class="mx-1 link-light text-decoration-none hover-text-light" href="<?= ROOT_PATH;?>settings"><i class="fas fa-cog"></i></a>
        <a title="Logout" class="mx-1 link-light text-decoration-none hover-text-light" href="<?= ROOT_PATH;?>logout"><i class="fas fa-sign-out-alt"></i></a>
    </div>
<?php endif;?>
    <div class="bg-dark-primary px-2 py-1 rounded-pill d-flex mx-1">
        <span class="dropdown">
            <div title="Search" class="mx-1 cursor-pointer hover-text-light" id="searchmenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-search"></i>
            </div>
            <ul class="border-2 border-primary rounded dropdown-menu p-0 overflow-hidden">
                <form method="get" class="btn-group">
                    <input id="searchbar" name="searchterm" style="width: 300px; min-width: 75px;" class="rounded-0 form-control" type="text" placeholder="Search...">
                    <button type="subbmit" class="rounded-0 btn btn-outline-primary"><i class="fas fa-search"></i></button>
                </form>
            </ul>
        </span>
    </div>
</div>