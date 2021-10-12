<?php App\Template::load('header', array('title' => 'User'));?>
<div class="row ">
    <div class="col-md-12 mb-3 mt-3">
        <div class="card">
            <div class="card-body">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= ROOT_PATH;?>">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<?php App\Template::load('footer');?>