<?php
    namespace App\Models;

    use App\Model;

    class UserModel extends Model {
        public function usersCount() {
            $this->db->query('SELECT COUNT(*) as count FROM users;');
            $row = $this->db->single();

            return $row->count;
        }
        public function existsByUsername($username) {
            $this->db->query('SELECT id FROM users WHERE username LIKE :username;');
            $this->db->bind(':username', $username);
            $this->db->execute();

            return $this->db->rowCount() > 0;
        }
        public function existsById($id) {
            $this->db->query('SELECT id FROM users WHERE id LIKE :id;');
            $this->db->bind(':id', $id);
            $this->db->execute();

            return $this->db->rowCount() > 0;
        }
        public function findByUsername($username) {
            $this->db->query('SELECT * FROM users WHERE username LIKE :username;');
            $this->db->bind(':username', $username);
            $row = $this->db->single();

            return $row;
        }
        public function findById($id) {
            $this->db->query('SELECT * FROM users WHERE id LIKE :id;');
            $this->db->bind(':id', $id);
            $row = $this->db->single();

            return $row;
        }
        public function usernameToId($username) {
            $this->db->query('SELECT id FROM users WHERE username LIKE :username;');
            $this->db->bind(':username', $username);
            $row = $this->db->single();

            return $row->id;
        }
        public function getFriends($uid) {
            $this->db->query('SELECT * FROM friendships WHERE (requester LIKE :uid OR target LIKE :uid) AND accepted LIKE 1;');
            $this->db->bind(':uid', $uid);
            $results = $this->db->resultSet();

            return $this->db->rowCount() > 0 ? $results : null;
        }
        public function friendsCount($uid) {
            $this->db->query('SELECT COUNT(*) as count FROM friendships WHERE (requester LIKE :uid OR target LIKE :uid) AND accepted LIKE 1;');
            $this->db->bind(':uid', $uid);
            $row = $this->db->single();

            return $row->count;
        }
    }