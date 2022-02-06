<?php
    namespace app\users;

    use app\Database;
    use app\Model;
    use app\Sanitize;

    class User extends Model{
        public static function isLoggedIn() {
            return isset($_SESSION['loggedIn']);
        }

        public static function getFromUsername(Database $db, $username) {
            $db->query('SELECT * FROM users WHERE username LIKE :username;');
            $db->bind('username', $username);
            $res = $db->single();

            if($db->rowCount() >= 1) {
                return new User($db, $res);
            } else {
                return null;
            }
        }

        public function __construct(Database $db, $input) {
            $this->_db = $db;

            if(is_object($input)) {
                $this->_data = $input;
                $this->_exists = true;
            } else if(is_numeric($input)) {
                $id = Sanitize::int($input);

                $this->_db->query('SELECT * FROM users WHERE id LIKE :id');
                $this->_db->bind('id', $id);
                $res = $this->_db->single();

                if($db->rowCount() >= 1) {
                    $this->_exists = true;
                    $this->_data = $res;
                }
            }
        }

        public function getId() {
            return $this->getData('id');
        }

        public function getUid() {
            return $this->getData('uid');
        }

        public function getUsername() {
            return $this->getData('username');
        }

        public function verifyPassword($pw) {
            return password_verify($pw, $this->getData('password'));
        } 
    }