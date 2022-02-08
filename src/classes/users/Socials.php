<?php
    namespace app\users;

    use app\Database;
    use app\Model;
    use app\Sanitize;

    class Socials extends Model{

        public function __construct(Database $db, $input) {
            $this->_db = $db;

            if(is_object($input)) {
                $this->_data = $input;
                $this->_exists = true;
            } else if(is_numeric($input)) {
                $id = Sanitize::int($input);

                $this->_db->query('SELECT * FROM socials WHERE id LIKE :id');
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

        public function getName() {
            return $this->getData('name');
        }

        public function getUsername() {
            return $this->getData('username');
        }

        public function getLink() {
            return $this->getData('link');
        }

        public function getLogo() {
            return $this->getData('logo');
        }
    }