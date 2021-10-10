<?php
    namespace App\Auth;

    use App\Models\UserModel;
    use App\User\User;
    use App\Auth\UserSession;

    class Login {
        private $username, $password;
        private $userModel;
        private $user;
        private $userSession;

        public function __construct($username, $password) {
            $this->username = $username;
            $this->password = $password;
            $this->userModel = new UserModel();
        }

        public function try() {
            if(!$this->userModel->existsByUsername($this->username)) {
                return false;
            }
            $this->user = new User($this->userModel->usernameToId($this->username));
            if($this->user->verifyPassword($this->password)) {
                $this->userSession = new UserSession($this->user->getId());
                $this->userSession->set();
                return true;
            }
            return false;
        }
    }