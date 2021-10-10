<?php
    namespace App;

    use App\User\User;

    class Core {
        private $currentController;
        private $currentMethod;
        private $params = [];
        private $user;

        public function __construct($currentController, $currentMethod) {
            $this->currentController = $currentController;
            $this->currentMethod = $currentMethod;

            session_start();

            $this->getConfig();

            $this->user = $this->getUser();

            $this->makeRoute();
        }

        private function makeRoute() {
            $url = $this->getUrlAsArray();
            $controller = ucfirst(strtolower($url[0]));
            if(file_exists(__DIR__ . '/Controllers/' . $controller . 'Controller.php')) {
                $this->currentController = $controller;
                unset($url[0]);
            }
            $this->currentController = 'App\\Controllers\\' . $this->currentController . 'Controller';
            $this->currentController = new $this->currentController($this->user);

            if(isset($url[1])) {
                $method = strtolower($url[1]);
                if(method_exists($this->currentController, $method)) {
                    $this->currentMethod = $method;
                    unset($url[1]);
                }
            }

            $this->params = $url ? array_values($url) : array(null);

            call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
        }

        private function getUrlAsArray() {
            if(isset($_SERVER['PATH_INFO'])) {
                $url = rtrim($_SERVER['PATH_INFO'], '/'); // remove last slash
                $url = substr($url, 1); // remove first slash
                $url = filter_var($url, FILTER_SANITIZE_URL); // sanitize URL
                $url = explode('/', $url);

                return $url;
            }
        }

        private function getConfig() {
            require_once(__DIR__ . '/../config/config.php');
        }

        private function getUser() {
            if(!User::isLoggedIn()) {
                return null;
            }

            return new User($_SESSION['loggedIn']);
        }
    }