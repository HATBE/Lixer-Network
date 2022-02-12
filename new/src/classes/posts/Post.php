<?php
    namespace app\posts;

    use app\Database;
    use app\Model;
    use app\Sanitize;
    use app\users\User;

    class Post extends Model{
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
                'post_id' => $this->getId(),
                'type' => $this->getType(),
                'username' => $this->getUser()->getUsername(),
                'text' => $this->getText(),
                'time' => $this->getTimeUnix()
            ];
            return $array;
        }

        public function getId() {
            return $this->getData('id');
        }

        public function getText() {
            return $this->getData('text');
        }

        public function getUser() {
            return new User($this->_db, $this->getData('user_id'));
        }

        public function getTime() {
            return date('d.m.Y H:i', $this->getData('time'));
        }

        public function getTimeUnix() {
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