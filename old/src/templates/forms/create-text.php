<?php
    use app\Template;
?>
<h2 class="h4 fw-bold my-4 user-select-none">Text Post</h2>
<div class="user-select-none col-5 col-md-2 text-center md-none">
    <?= Template::load('user/avatar', ['user' => $loggedInUser]);?>
    <?= Template::load('user/usernameLink', ['user' => $loggedInUser])?>
</div>
<div class="col-12 col-md-10">
    <div class="alert d-none" id="create-alert"></div>
    <form id="create-text-form">
        <div class="form-group mb-3">
            <textarea id="create-text-area" style="max-height: 230px;min-height: 120px;height: 150px;" class="form-control form-control-lg" placeholder="What do you want to share with us?"></textarea>
        </div>
        <button class="btn btn-primary w-100" type="submit">Share</button>
    </form>
</div>