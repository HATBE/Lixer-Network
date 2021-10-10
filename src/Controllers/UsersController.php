<?php
    namespace App\Controllers;

    use App\Controller;
    use App\Models\UserModel;
    use App\User\User;

    class UsersController extends Controller {
        private $userModel;

        public function __construct($user) {
            $this->user = $user;
            $this->userModel = new UserModel();
        }

        public function index($id) {
            $this->profile($id);
        }

        public function profile($id) {
            if($id == null && $this->user != null) {
                $id = $this->user->getId();
            }

            $user = $this->userModel->findById($id);
            if($user == false) {
                
                $this->render('users/profile', array('noUser' => true));
                exit();
            } else {
                $user = new User($user->id);
            }

            $data = array(
                'noUser' => false,
                'username' => $user->getUsername()
            );

            $this->render('users/profile', $data);
        }

        public function settings() {
            $this->render('users/settings');
        }
    }