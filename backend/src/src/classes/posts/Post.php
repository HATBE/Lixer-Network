<?php
    namespace app\posts;

    use app\io\Database;
    use app\Model;
    use app\Sanitize;
    use app\users\User;

    class Post extends Model {
        public static function getFromUid(Database $db, $uid) {
            $db->query('SELECT * FROM posts WHERE uid LIKE :uid;');
            $db->bind('uid', $uid);
            $res = $db->single();

            return $db->rowCount() >= 1 ? new Post($db, $res) : null;
        }

        public static function uidExists(Database $db, string $uid) {
            $db->query('SELECT COUNT(id) as c FROM posts WHERE uid LIKE :uid;');
            $db->bind('uid', $uid);

            return $db->single()->c >= 1 ? true : false;
        }
        
        public function __construct(Database $db, $input) {
            $this->_db = $db;

            if(is_object($input)) {
                $this->_data = $input;
                $this->_exists = true;
            } else if(is_numeric($input)) {
                $id = Sanitize::int($input);

                $this->_db->query('SELECT * FROM posts WHERE id LIKE :id');
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
                'post_id' => $this->getUid(),
                'type' => $this->getType(),
                'username' => $this->getUser()->getUsername(),
                'text' => $this->getText(),
                'time' => intval($this->getTime())
            ];
            return $array;
        }

        public function getId() {
            return $this->getData('id');
        }

        public function getUid() {
            return $this->getData('uid');
        }

        public function getText() {
            return $this->getData('text');
        }

        public function getUser() {
            return new User($this->_db, $this->getData('user_id'));
        }

        public function getTimeFormatted($format = 'd.m.Y H:i') {
            return date($format, $this->getData('time'));
        }

        public function getTime() {
            return $this->getData('time');
        }

        public function getType() {
            $typeid = $this->getData('type_id');

            $this->_db->query('SELECT name FROM post_types WHERE id LIKE :id;');
            $this->_db->bind('id', $typeid);
            $type = $this->_db->single()->name;

            return $type;
        }
    }