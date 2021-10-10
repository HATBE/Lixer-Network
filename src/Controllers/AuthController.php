<?php
    namespace App\Controllers;

    use App\Controller;
    use App\Validation\Validator;
    use App\Auth\Login;
    use App\User\User;

    class AuthController extends Controller {
        public function index() {
            $this->login();
        }

        public function login() {
            if(User::isLoggedIn()) header('Location: ' . ROOT_PATH . '/index/index');

            $errors = array();
            $username = '';

            if(isset($_POST['submit'])) {
                if(empty($_POST['username']) || empty($_POST['password'])) {
                    array_push($errors, 'Please fill in all fields');
                }
                if(empty($errors)) {
                    $username = $_POST['username'];
                    if(Validator::username($username)) {
                        array_push($errors, 'Username format does not match');
                    }
                    $password = $_POST['password'];

                   if(empty($errors)) {
                        $login = new Login($username, $password);

                        if(!$login->try()) {
                            array_push($errors, 'Username or password invalid.');
                        } else {
                            header('Location: ' . ROOT_PATH . '/index/index');
                        }
                   }
                }
            }

            $data = array(
                'errors' => $errors,
                'username' => $username
            );  

            $this->render('auth/login', $data);
        }

        public function register() {
            if(User::isLoggedIn()) header('Location: ' . ROOT_PATH . '/index/index');

            $errors = array();
            $username = '';

            /*if(isset($_POST['submit'])) { 
                if(empty($_POST['username']) || empty($_POST['password'])) {
                    array_push($errors, 'Please fill in all fields');
                }
                if(empty($errors)) {
                    $username = $_POST['username'];
                    if(Validator::username($username)) {
                        array_push($errors, 'Username format does not match');
                    }
                    $password = $_POST['password'];
                    if(Validator::password($password)) {
                        array_push($errors, 'Password format does not match');
                    }
                }
            }*/

            $data = array(
                'errors' => $errors,
                'username' => $username
            );  

            $this->render('auth/register', $data);
        }

        public function logout() {
            session_destroy(); // change this to Session class
            header('Location: ' . ROOT_PATH . '/index/index');
        }

        public function forgotpassword() {
            echo "not yet implemented";
        }
    }