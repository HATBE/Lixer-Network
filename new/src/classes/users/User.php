<?php
    namespace app\users;

    use app\Database;
    use app\Model;
    use app\Sanitize;

    class User extends Model{
        public static function isAuthorized($db, $token) {
            $db->query('SELECT * FROM sessions WHERE accesstoken LIKE :token;');
            $db->bind('token', $token);
            $res = $db->single();

            if($db->rowCount() <= 0) return false;

            if($res->accesstokenexpiry - time() <= 0) return false;

            return $res->user_id;
        }

        public static function usernameExists($db, $username) {
            $db->query('SELECT id from users WHERE username LIKE :uname;');
            $db->bind('uname', $username);
            $db->single();
            return $db->rowCount() >= 1 ? true : false;
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

        public function getAsArray() {
            $array = [
                'user_id' => $this->getId(),
                'username' => $this->getUsername(),
                'joinTime' => $this->getJoinTimeUnix()
            ];
            return $array;
        }

        public function getId() {
            return $this->getData('id');
        }

        public function getUsername() {
            return $this->getData('username');
        }

        public function getJoinTime() {
            return date('d.m.Y H:i', $this->getData('joinTime'));
        }

        public function getJoinTimeUnix() {
            return $this->getData('joinTime');
        }

        public function verifyPassword($pw) {
            return password_verify($pw, $this->getData('password'));
        }

        public function isFollowing($user) {
            $this->_db->query('SELECT COUNT(id) as c FROM following WHERE source_user_id LIKE :source AND target_user_id LIKE :target;');
            $this->_db->bind('source', $this->getId());
            $this->_db->bind('target', $user->getId());
            $res = $this->_db->single()->c;
            return $res >= 1 ? true : false;
        }

    }