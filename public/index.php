<?php
    session_start();
    require __DIR__ . "/../init.php";

    $router = $container->make('router');
    $maintenance = $container->make('maintenance');

    if($maintenance->getMaintenance()) {
        if(!isset($_SESSION['bypassMaintenance'])) {
            if(isset($_GET['unlock'])) {
                $code = $maintenance->createSession(e($_GET['unlock']));
                if($code !== false) {
                    header("Refresh:0");
                }
            }
            echo 'Maintenance';
            //$router->makeRoute("index/maintenance");
            die();
        }
    } else {
        $maintenance->deleteSession();
    }

    if(isset($_SERVER['PATH_INFO']) OR !empty($_SERVER['PATH_INFO'])) {
        $params = substr($_SERVER['PATH_INFO'], 1); // remove first slash
        if(substr($params, -1) === "/") { // check if there is a slash at the last position
            $params = substr($params, 0, -1); // remove last slash
        }
    } else {
        $params = "index/index";
    }

    
    $router->makeRoute($params);