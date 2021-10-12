<?php App\Template::load('header', array('title' => 'User'));?>
    <?php if($data['noUser']):?>
        No user found
    <?php else:?>
        <div class="row ">
            <div class="col-md-12 mb-3 mt-3">
                <div class="card">
                    <div class="card-body">
                        <nav aria-label="breadcrumb">
                            <ol class="m-0 breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= ROOT_PATH;?>">Home</a></li>
                                <li class="breadcrumb-item"><a href="<?= ROOT_PATH . '/users';?>">Users</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?= $data['user']->getUsername()?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img width="200" height="200" src="https://data.1freewallpapers.com/download/tree-alone-dark-evening-4k.jpg" class="rounded-circle">
                            <div class="mt-3">
                                <h4><?= $data['user']->getUsername();?> <?= $data['user']->hasBirthday() ? '<i class="text-danger fas fa-birthday-cake"></i>' : '';?></h4>
                                <h5><span class="badge bg-danger">Administrator</span></h5>
                                <p class="text-secondary mt-2"><?= $data['user']->getSlogan()?></p>
                            </div>
                            <div>
                                <p class="text-secondary mt-2 mb-1">45 Friends</p>
                                <button class="btn btn-primary">Add as Friend</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 mb-3">
                <div class="card mb-3">
                    <div class="card-body">
                        <ul class="nav nav-tabs">
                            <?php foreach($data['tabs'] as $tab):?>
                                <li class="nav-item">
                                    <a class="nav-link link-secondary <?= $data['tab'] == $tab ? 'active' : '';?>" href="<?= ROOT_PATH . '/users/profile/' . $data['id'] . '/' . $tab;?>">
                                        <?= ucfirst($tab); ?>
                                    </a>
                                </li>
                            <?php endforeach;?>
                        </ul>
                        <div class="mt-2">
                            <?php if($data['tab'] == 'friends'):?>
                                <?php App\Template::load('friendsWidget', array('user' => $data['user']))?>
                            <?php elseif($data['tab'] == 'about'):?>
                                <?php App\Template::load('aboutWidget', array('user' => $data['user']))?>
                            <?php endif;?>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    <?php endif;?>
<?php App\Template::load('footer');?>