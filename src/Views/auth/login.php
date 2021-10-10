<?php App\Template::load('header', array('title' => 'Login'));?>
   <section class="container py-3">
       <div class="row d-flex justify-content-center align-items-center">
           <div class="col-xl-5">
                <div class="card bg-dark text-white">
                    <div class="card-body p-5 text-center">
                        <h2 class="fw-bold">Login</h2>
                        <?php App\Template::load('alertErrors', array('errors' => $data['errors']));?>
                        <form method="post">
                            <div class="form-outline form-white mb-4 text-start">
                                <label for="username" class="form-label">Username</label>
                                <input name="username" value="<?= $data['username']; ?>" id="username" type="text" class="form-control form-control-lg">
                            </div>
                            <div class="form-outline form-white mb-4 text-start">
                                <label for="password" class="form-label">Password</label>
                                <input name="password" id="password" type="password" class="form-control form-control-lg">
                            </div>
                            <div>
                                <p class="mb-3 small pb-lg-2"><a href="<?= ROOT_PATH;?>/auth/forgotpassword" class="text-white-50">Forgot password?</a></p>
                            </div>
                            <button name="submit" type="submit" class="btn btn-outline-light btn-lg px-5">Login</button>
                            <div class="mt-4">
                                <p class="mb-0">Don't have an account? <a href="<?= ROOT_PATH;?>/auth/register" class="text-white-50 fw-bold">Register</a></p>
                            </div>
                        </form>
                    </div>
                </div>
           </div>
       </div>
   </section>
<?php App\Template::load('footer');?>