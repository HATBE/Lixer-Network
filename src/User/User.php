<?php
    namespace App\User;

use App\Auth\UserSession;
use App\Models\UserModel;
use DateTime;

class User {
        private $userModel;

        private $id;
        private $username;
        private $password;
        private $displayname;
        private $joinDate;
        private $birthDate;
        private $showBirthYear;
        private $gender;
        private $primaryGroup;
        private $profilePicturePath;
        private $slogan;

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
            $this->displayname = $userData->displayname;
            $this->joinDate = $userData->joinDate;
            $this->birthDate = $userData->birthDate;
            $this->showBirthYear = $userData->showBirthYear;
            $this->gender = '';
            $this->primaryGroup = '';
            $this->slogan = $userData->slogan;
            $this->profilePicturePath = $userData->profilePicturePath;

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
        public function getDisplayName() {
            return $this->displayname;
        }
        public function getGender() {
            return $this->gender;
        }
        public function getSlogan() {
            return $this->slogan;
        }
        public function getPrimaryGroup() {
            return $this->primaryGroup;
        }
        public function getJoinDate() {
            return date('d.m.Y H:m', $this->joinDate);
        }
        public function getRealBirthdate() {
            return date('d.m.Y', $this->birthDate);
        }
        public function getPublicBirthdate() {
            return date($this->showBirthYear == 1 ? 'd.m.Y' : 'd.m', $this->birthDate);
        }
        public function hasBirthday() {
            $date = date('d.m', $this->birthDate);
            $now = date('d.m');
            if($date == $now) {
                return true;
            }

            return false;
        }
        public function friendsCount() {
            return $this->userModel->friendsCount($this->getId());
        }
        public function getFriends() {
            $friendsArray = array();
            $friends = $this->userModel->getFriends($this->getId());
            foreach($friends as $friend)  {
                if($friend->requester != $this->getId()) {
                    $friend = $friend->requester;
                } else {
                    $friend = $friend->target;
                }
                array_push($friendsArray, new User($friend));
            }

            return $friendsArray;
        }
        public function getFriendRequests() {

        }
    }
