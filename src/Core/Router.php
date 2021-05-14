<?php
    namespace App\Core;

    use App\Core\Container;

    class Router {

        // Preset: /Controller/View/Param/Param/...
        // default method = index (if noting isset)

        private $mvc;
        private $config;
        private $container;

        public function __construct(Container $container) {
            $this->config = $container->getConfig();
            $this->container = $container;

            $json = file_get_contents(__DIR__ . "../../../json/routes.json");
            $this->mvc = json_decode($json, true);
        }

        // nothing to change under this line! Just the Router
        public function makeRoute($path) {
            $params = explode("/", $path); 

            if(isset($this->mvc[$params[0]])) {
                // if a controller isset in the URL
                if(empty($params[1])) {
                    // if no view isset in the URL
                    // go to Index method from Controller
                    $params[0] = $params[0];
                    $params[1] = 'index';
                } else {
                    // if a view isset in the URL
                    if(isset($this->mvc[$params[0]][$params[1]]['controller'])) {
                        // if the selected view exists
                        $params[0] = $params[0];
                        $params[1] = $params[1];
                        // check for Human error in MVC Array
                    } else {
                        // if the selected view dont exists
                        // go to Index method from Controller
                        $params[0] = $params[0];
                        $params[1] = 'index';
                    }
                }

                $controller = $this->container->make($this->mvc[$params[0]][$params[1]]['controller']);
                $method = $this->mvc[$params[0]][$params[1]]['method'];

                if(method_exists($controller, $method)) {
                    // if Method exists
                    if($this->mvc[$params[0]][$params[1]]['params']) {
                        // if Params are set for this view
                        if(count($params) > 2) {
                            // if no Params are set for this view
                            // make view and pass params
                            for ($i=2; $i < count($params); $i++) { 
                                $passParams[] = e($params[$i]);
                            }
                            $controller->$method($passParams);
                        } else {
                            // if no Params are set for this view
                            $controller->$method();
                        }
                    } else {
                        // if no Params are set for this view
                        $controller->$method();
                    }
                } else {
                    // if method dont exists
                    // go to Index method from Controller
                    $controller->index();
                }
            } else {
                // if no controller isset in the URL (cant happen, index.php prevents), just for sec. reasons
                $controller = $this->container->make("errorController");
                $controller->error(404); // wtf ??
            }
        }

        public function navigate($path) {
            header("Location: https://{$_SERVER['SERVER_NAME']}/{$path}");
        }
    }