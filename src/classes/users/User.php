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

        public static function getLoggedInId() {
            return isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : null;
        }

        public static function updateLastActivity(Database $db) {
            if(User::isLoggedIn()) {
                $userid = User::getLoggedInId();
                $time = time();
                $db->query('SELECT * FROM last_activity WHERE id LIKE :id;');
                $db->bind('id', $userid);
                $row = $db->single();
        
                if($db->rowCount() >= 1) {
                    if($time - $row->time > 200) {
                        $db->query('UPDATE last_activity SET time = :time WHERE user_id LIKE :id;');
                        $db->bind('id', $userid);
                        $db->bind('time', $time);
                        $db->execute();
                    }
                } else {
                    $db->query('INSERT INTO last_activity (user_id, time) VALUES (:id, :time);');
                    $db->bind('id', $userid);
                    $db->bind('time', $time);
                    $db->execute();
                }
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

        public function getJoinDate() {
            return date('d.m.Y', $this->getData('joinDate'));
        }

        public function getJoinDateUnix() {
            return $this->getData('joinDate');
        }

        public function getBio() {
            return $this->_data->bio == null ? '' : $this->getData('bio');
        }

        public function getAvatarPath() {
            return "https://avatars.dicebear.com/api/jdenticon/{$this->getUsername()}.svg";
        }

        public function getSocials() {
            $id = $this->getId();
            $this->_db->query('SELECT socials.id, socials.name, socials.logo, users_socials.username, users_socials.link FROM socials, users_socials WHERE socials.id LIKE users_socials.socials_id AND users_socials.user_id LIKE :id;');
            $this->_db->bind('id', $id);
            $res = $this->_db->resultSet();

            $socials = [];
            foreach($res as $result) {
                array_push($socials, new Socials($this->_db, $result));
            }

            return $socials;
        }

        public function getLastActivity() {
            $userid = $this->getId();
            $this->_db->query('SELECT * FROM last_activity WHERE id LIKE :id;');
            $this->_db->bind('id', $userid);
            $row = $this->_db->single();

            if($this->_db->rowCount() >= 1) {
                return $row->time;
            } else {
                return null;
            }
        }

        public function getOnlineState() {
            $time = time();
            $lastActivity = $this->getLastActivity();

            if($lastActivity === null) {
                return ['name' => 'Offline', 'color' => '#bababa'];
            }

            if($time - $lastActivity >= 600) { # bigger than 10 minutes
                if(date('d.m.Y', $time) == date('d.m.Y', $lastActivity)) {
                    return ['name' => 'Was online today', 'color' => '#ddffff'];
                } else {
                    return ['name' => 'Offline', 'color' => '#bababa'];
                }
            } else if($time - $lastActivity >= 300) { # bigger than 5 minutes
                return ['name' => 'Absent', 'color' => '#edef72'];
            } else {
                return ['name' => 'Online', 'color' => '#66ff66'];
            }
        }
    }