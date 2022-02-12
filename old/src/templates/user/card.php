<?php 
    use app\Template;
?>
<div class="card cursor-default">
    <div class="card-header bg-primary text-light d-flex justify-content-between align-items-center">
        <div class="mb-0 h4">
         <?= $user->getUsername();?>
        </div>
        <?php if($user != $loggedInUser && $loggedInUser != null):?>
        <div class="btn-group dropdown">
            <button type="button" class="btn text-white" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu p-0">
                <li><a class="dropdown-item text-danger" href="<?= ROOT_PATH;?>report/user"><span style="display: inline-block;width: 23px;"><i class="fas fa-bullhorn"></i></span> Report user</a></li>
                <li class="cursor-pointer dropdown-item" onclick="blockUser(<?= $user->getId();?>, true)"><span style="display: inline-block;width: 23px;"><i class="fas fa-lock"></i></span> Block user</li>
            </ul>
        </div>
        <?php endif;?>
    </div>
    <div class="card-body rounded-circle d-flex flex-column justify-content-center align-items-center">
        <div class="w-50 h-50 border-4 rounded-circle position-relative text-center" >
            <?= Template::load('user/avatar', ['user' => $user]);?>
            <?php if(($loggedInUser != null) && $user->getOnlineState()['name'] != 'Offline'):?><span title="<?= $user->getOnlineState()['name'];?>" style="background-color: <?= $user->getOnlineState()['color'];?>; width: 20%; height: 20%; bottom: 5%; right: 5%;" class="shadow rounded-circle position-absolute"></span><?php endif;?>
        </div>
        <?php if($user == $loggedInUser):?>
        <div class="mt-3 bg-danger rounded-pill text-light py-1 px-2">
            You
        </div>
        <?php endif;?>
        <div class="mt-4 row w-100 text-center">
            <div class="col-6">
                <div class="cursor-text fw-bold"><?= $user->getFollowersCount();?></div>
                <div class="user-select-none">Follower</div>
            </div>
            <div class="col-6">
                <div class="cursor-text fw-bold"><?= $user->getFollowingCount();?></div>
                <div class="user-select-none">Following</div>
            </div>
        </div>
        <ul class="mt-2 w-100 list-group list-group-flush ">
            <li title="Location" class="list-group-item"><i class="me-2 fas fa-map-marker-alt"></i>Switzerland, Zürich</li>
            <li title="Joindate" class="list-group-item"><i class="me-2 fas fa-calendar-alt"></i><?= $user->getJoinDate();?></li>
            <li title="bio" class="list-group-item"><?= $user->getBio();?></li>
        </ul>
        <?php if($user != $loggedInUser && $loggedInUser != null):?>
        <div class="user-select-none my-2 d-flex justify-content-between w-100">
            <div>
                <?= Template::load('buttons/follow', ['loggedInUser' => $loggedInUser, 'target' => $user->getId()]);?>
                <button onclick="openChat(<?= $user->getId();?>)" class="mx-2 btn btn-outline-primary">Message</button>
            </div>
        </div>
        <?php endif;?>
    </div>
</div>
<?php if($user->getSocials()):?>
    <div class="card my-4 user-select-none">
        <div class="card-header bg-primary text-light">
            Socials
        </div>
        <ul class="list-group list-group-flush">
            <?php foreach($user->getSocials() as $socials):?>
                <a title="<?= $socials->getName();?>" class="text-decoration-none" href="<?= $socials->getLink();?>" target="_blank"><li class="list-group-item hover-light"><i class="me-2 <?= $socials->getLogo();?>"></i><?= $socials->getUsername();?></li></a>
            <?php endforeach;?>
        </ul>
    </div>
<?php endif;