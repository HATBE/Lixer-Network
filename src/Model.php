<?php
    namespace App;

    use App\Database;

    abstract class Model {
        protected $db;

        public function __construct() {
            $this->db = new Database;
        }
    }