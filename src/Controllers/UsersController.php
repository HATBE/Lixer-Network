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

        public function index() {
            $this->users();
        }

        public function users() {
            $this->render('users/users');
        }

        public function profile($id, $tab = 'data') {
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

            $tabs = array(
                'posts',
                'about',
                'friends'
            );
            if(!in_array($tab, $tabs)) {
                $tab = 'posts';
            }

            $data = array(
                'noUser' => false,
                'id' => $id,
                'tab' => $tab,
                'tabs' => $tabs,
                'user' => $user
            );

            $this->render('users/profile', $data);
        }

        public function settings() {
            $this->render('users/settings');
        }
    }