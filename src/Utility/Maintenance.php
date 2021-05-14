<?php
    Namespace App\Utility;

    use App\Core\Container;

    Class Maintenance {

        private $maintenance;
        private $config;

        public function __construct(Container $container) {
            $this->config = $container->getConfig();
            $this->maintenance = $this->config['maintenance'];
        }

        public function getMaintenance() {
            return $this->maintenance;
        }

        public function createSession($get) {
            if($get == $this->config['maintenanceBypassCode']) {
                $sessionname = time().'T'.random_int(100,999);
                $_SESSION['bypassMaintenance'] = $sessionname;
                return true;
            } else {
                return false;
            }
        }

        public function deleteSession() {
            unset($_SESSION['bypassMaintenance']);
        }

    }