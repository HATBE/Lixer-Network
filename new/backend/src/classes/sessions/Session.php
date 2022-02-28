<?php
    namespace app\sessions;

    use app\io\Database;
    use app\Model;
    use app\Sanitize;

    class Session extends Model {
        public static function delete(Database $db, int $id, $accesstoken) {
            $db->query('DELETE FROM sessions WHERE id LIKE :sessionid AND accesstoken LIKE :accesstoken;');
            $db->bind('sessionid', $id);
            $db->bind('accesstoken', $accesstoken);
            $db->execute();
        }

        public static function getFromUid(Database $db, string $uid) {
            $db->query('SELECT * FROM sessions WHERE uid LIKE :uid;');
            $db->bind('uid', $uid);
            $res = $db->single();

            return $db->rowCount() >= 1 ? new Session($db, $res) : null;
        }

        public static function getFromAccesstoken(Database $db, string $token) {
            $db->query('SELECT * FROM sessions WHERE sessions.accesstoken LIKE :token;');
            $db->bind('token', $token);
            $res = $db->single();

            if($db->rowCount() <= 0) return false;
            if($res->accesstokenexpiry - time() <= 0) return false;

            return new Session($db, $res->user_id);
        }

        public static function uidExists(Database $db, string $uid) {
            $db->query('SELECT COUNT(id) as c FROM sessions WHERE uid LIKE :uid;');
            $db->bind('uid', $uid);

            return $db->single()->c >= 1 ? true : false;
        }

        public function __construct(Database $db, $input) {
            $this->_db = $db;

            if(is_object($input)) {
                $this->_data = $input;
                $this->_exists = true;
            } else {
                $id = Sanitize::int($input);

                $this->_db->query('SELECT * FROM sessions WHERE id LIKE :id;');
                $this->_db->bind('id', $id);
                $res = $this->_db->single();

                if($this->_db->rowCount() >= 1) {
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

        public function getCreationTime() {
            return $this->getData('creationtime');
        }

        public function getAccesstokenExpiry() {
            return $this->getData('accesstokenexpiry');
        }

        public function getAccesstoken() {
            return $this->getData('accesstoken');
        }

        public function getRefreshtokenExpiry() {
            return $this->getData('refreshtokenexpiry');
        }

        public function getRefreshtoken() {
            return $this->getData('refreshtoken');
        }
    }