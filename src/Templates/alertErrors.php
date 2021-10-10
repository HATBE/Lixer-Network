<?php if($errors != null):?>
    <div class="alert alert-danger text-start" role="alert">
    <?php foreach($errors as $error):?>
        <?= $error;?><br>
    <?php endforeach;?>
    </div>
<?php endif;?>