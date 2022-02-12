<?php
    namespace app;

    use PDO;
    use PDOException;

    class Database {
        private $_host = DB_HOST;
        private $_user = DB_USER;
        private $_password = DB_PASS;
        private $_name = DB_NAME;

        private $_stmt;
        private $_handler;
        private $_error;

        public function __construct() {
            $conn = "mysql:host={$this->_host};dbname={$this->_name};";
            $opt = array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );
            try {
                $this->_handler = new PDO($conn, $this->_user, $this->_password, $opt);
            } catch(PDOException $e) {
                $this->_error = $e->getMessage();
                error_log($this->_error, 0);
                exit();
            }
        }

        public function query($sql) {
            $this->_stmt = $this->_handler->prepare($sql);
        }

        public function bind($param, $value, $type = null) {
            switch (is_null($type)) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
            $this->_stmt->bindValue($param, $value, $type);
        }

        public function execute() {
            return $this->_stmt->execute();
        }
        
        public function resultSet() {
            $this->execute();
            return $this->_stmt->fetchAll(PDO::FETCH_OBJ);
        }

        public function  single() {
            $this->execute();
            return $this->_stmt->fetch(PDO::FETCH_OBJ);
        }

        public function rowCount() {
            return $this->_stmt->rowCount();
        }

        public function lastId() {
            return $this->_handler->lastInsertId();
        }
    }