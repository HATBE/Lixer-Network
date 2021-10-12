<h5><?= $user->friendsCount(); ?> Friends</h5>
<div class="card">
  <ul class="list-group list-group-flush">
    <?php foreach($user->getFriends() as $friend):?>
        <li class="list-group-item d-flex align-items-center">
            <img width="64" height="64" src="https://data.1freewallpapers.com/download/tree-alone-dark-evening-4k.jpg" class="rounded">
            <div class="flex-fill ps-3 pe-3">
                <div><a href="<?= ROOT_PATH . '/users/profile/' . $friend->getId();?>" class="text-dark font-weight-600"><?= $friend->getUsername()?></a></div>
                <h6><span class="badge bg-info">Moderator</span></h6>
            </div>
            <button class="btn btn-sm btn-primary">Add as Friend</button>
        </li>
    <?php endforeach;?> 
  </ul>
</div>