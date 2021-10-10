<?php App\Template::load('header', array('title' => 'Register'));?>
    <section class="container py-3">
       <div class="row d-flex justify-content-center align-items-center">
           <div class="col-xl-5">
                <div class="card bg-dark text-white">
                    <div class="card-body p-5 text-center">
                        <h2 class="fw-bold">Register</h2>
                        <?php App\Template::load('alertErrors', array('errors' => $data['errors']));?>
                        <form method="post">
                            <div class="form-outline form-white mb-4 text-start">
                                <label for="username" class="form-label">Username</label>
                                <input name="username" id="username" type="text" class="form-control form-control-lg">
                            </div>
                            <div class="form-outline form-white mb-4 text-start">
                                <label for="password" class="form-label">Password</label>
                                <input name="password" id="password" type="password" class="form-control form-control-lg">
                            </div>
                            <button name="submit" type="submit" class="btn btn-outline-light btn-lg px-5">Register</button>
                            <div class="mt-4">
                                <p class="mb-0">Have an account? <a href="<?= ROOT_PATH;?>/auth/login" class="text-white-50 fw-bold">Login</a></p>
                            </div>
                        </form>
                    </div>
                </div>
           </div>
       </div>
   </section>
<?php App\Template::load('footer');?>