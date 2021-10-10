<?php
    namespace App\User;

use App\Auth\UserSession;
use App\Models\UserModel;

    class User {
        private $userModel;

        private $id;
        private $username;
        private $password;
        private $displayname;

        private $userSession;

        public function __construct($id) {
            $this->userModel = new UserModel();
            
            $userData = $this->userModel->findById($id);

            if($userData == false) {
                return false;
            }

            $this->id = $userData->id;
            $this->username = $userData->username;
            $this->password = $userData->password;
            $this->displayName = $userData->displayname;

            $this->userSession = new UserSession($this->getId());
        }

        public static function isLoggedIn() {
            return isset($_SESSION['loggedIn']);
        }

        public function verifyPassword($password) {
            return password_verify($password, $this->password);
        }

        public function logout() {
            $this->userSession->destroy();
        }

        public function getId() {
            return $this->id;
        }
        public function getUsername() {
            return $this->username;
        }
    }
