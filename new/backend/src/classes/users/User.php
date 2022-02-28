<?php
    namespace app\users;

    use app\io\Database;
    use app\Model;
    use app\Sanitize;

    class User extends Model {
        public static function getFromUid(Database $db, string $uid) {
            $db->query('SELECT * FROM users WHERE uid LIKE :uid;');
            $db->bind('uid', $uid);
            $res = $db->single();

            return $db->rowCount() >= 1 ? new User($db, $res) : null;
        }

        public static function getFromUsername(Database $db, string $username) {
            $db->query('SELECT * FROM users WHERE username LIKE :username;');
            $db->bind('username', $username);
            $res = $db->single();

            return $db->rowCount() >= 1 ? new User($db, $res) : null;
        }

        public static function getFromAccesstoken(Database $db, string $token) {
            $db->query('SELECT * FROM sessions WHERE sessions.accesstoken LIKE :token;');
            $db->bind('token', $token);
            $res = $db->single();

            if($db->rowCount() <= 0) return false;
            if($res->accesstokenexpiry - time() <= 0) return false;

            return new User($db, $res->user_id);
        }

        public static function uidExists(Database $db, string $uid) {
            $db->query('SELECT COUNT(id) as c FROM users WHERE uid LIKE :uid;');
            $db->bind('uid', $uid);

            return $db->single()->c >= 1 ? true : false;
        }

        public static function usernameExists(Database $db, string $username) {
            $db->query('SELECT COUNT(id) as c from users WHERE username LIKE :uname;');
            $db->bind('uname', $username);
            
            return $db->single()->c >= 1 ? true : false;
        }

        public function __construct(Database $db, $input) {
            $this->_db = $db;

            if(is_object($input)) {
                $this->_data = $input;
                $this->_exists = true;
            } else {
                $id = Sanitize::int($input);

                $this->_db->query('SELECT * FROM users WHERE id LIKE :id;');
                $this->_db->bind('id', $id);
                $res = $this->_db->single();

                if($this->_db->rowCount() >= 1) {
                    $this->_exists = true;
                    $this->_data = $res;
                }
            }
        }

        public function getAsArray() {
            $array = [
                'user_id' => $this->getUid(),
                'username' => $this->getUsername(),
                'joinTime' => intval($this->getJoinTime())
            ];

            return $array;
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

        public function getJoinTimeFormatted($format = 'd.m.Y H:i') {
            return date($format, $this->getData('joinTime'));
        }

        public function getJoinTime() {
            return $this->getData('joinTime');
        }

        public function verifyPassword($pw) {
            return password_verify($pw, $this->getData('password'));
        }

        public function isFollowing($uid) {
            $this->_db->query('SELECT COUNT(id) as c FROM following WHERE source_user_id LIKE :source AND target_user_id LIKE :target;');
            $this->_db->bind('source', $this->getId());
            $this->_db->bind('target', $uid);
            return $this->_db->single()->c >= 1 ? true : false;
        }
    }