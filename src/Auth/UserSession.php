<?php
    namespace App\Auth;

    use App\User\User;

    class UserSession extends Session {
        private $user;

        public function __construct($userId) {
            $this->userId = $userId;
        }

        public static function getUser() {
            if(!isset($_SESSION['loggedIn'])) {
                return null;
            }
            return new User($_SESSION['loggedIn']);
        }

        public function set() {
            $_SESSION['loggedIn'] = $this->userId;
            
            return true;
        }

        public function destroy() {
            unset($_SESSION['loggedIn']);

            return true;
        }
    }