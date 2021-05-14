<?php
    namespace App\Controller;

    use App\Core\AbstractController;
    use App\Core\Container;

    class UsersController extends AbstractController {

        public function __construct(Container $container) {
          $this->router = $container->getRouter();
          $this->config = $container->getConfig();
          $this->usersService = $container->getUsersService();
        }

        public function login() {

          $error = null;
          
            if($this->usersService->checkLogin()) {
              // redirect already logged in users directly to dashboard
              $this->router->navigate('users/dashboard');
            } else {
              // render login form for not loggedin users
              if(isset($_POST['username']) && isset($_POST['password'])) {
                if(!empty($_POST['username']) && !empty($_POST['password'])) {
                  if(!$this->usersService->login($_POST['username'], $_POST['password'])) {
                    $error = "<b class='text-red'>Username oder Passwort falsch!</b>";
                  }
                } else {
                  $error = "<b class='text-red'>Bitte alle felder ausfüllen!</b>";
                }
              }
  
              $this->render("Users/login", ['config' => $this->config, 'error' => $error]);
            }
        }

        public function logout() {
          $this->usersService->logout();
          $this->router->navigate('users/login');
        }

        public function dashboard() {
          if($this->usersService->checkLogin()) {
            $this->render("Users/dashboard", ['config' => $this->config]);
          } else {
            $this->router->navigate('users/login');
          }
        }
        
        public function user() {
          $this->render("Users/user", ['config' => $this->config]);
        }
    }