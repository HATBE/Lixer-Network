<?php
    namespace App;

    abstract class Controller {

        private $user;

        public function __construct($user) {
            $this->user = $user;
        }

        public function render($view, $data = array()) {
            $path = __DIR__ . '/Views/' . $view . '.php';
            if(file_exists($path)) {
                require_once($path);
            } else {
                die('View not found');
            }
        }
    }