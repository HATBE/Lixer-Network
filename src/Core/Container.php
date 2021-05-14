<?php
    namespace App\Core;

    use PDO;
    use PDOException;
    
    use App\Utility\Maintenance;

    // Services


    // Controller
    use App\Controller\IndexController;

    // Repositorys

    class Container {
        
        private $receipts;
        private $instances;
        private $config;

        public function __construct($config) {
            $this->config = $config;
            $this->receipts = [
                'indexController' => function() {
                    return new IndexController($this);
                }
                'router' => function() {
                    return new Router($this);
                },
                'maintenance' => function() {
                    return new Maintenance($this);
                },
                'pdo' => function() {
                    try {
                        $pdo = new PDO(
                        "mysql:host={$this->config['dbHost']};dbname={$this->config['dbDb']};charset=utf8",
                        "{$this->config['dbUser']}",
                        "{$this->config['dbPw']}"
                        );
                    } catch (PDOException $e) {
                        echo "Verbindung zur Datenbank fehlgeschlagen";
                        die();
                    }
                    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                    return $pdo;
                }
            ];
        }
        
        public function make($name) {
            if (!empty($this->instances[$name])) {
                return $this->instances[$name];
            }
        
            if (isset($this->receipts[$name])) {
                $this->instances[$name] = $this->receipts[$name]();
            }
        
            return $this->instances[$name];
        }

        // GETTERS / SETTERS

        public function getConfig() {
            return $this->config;
        }
        
        public function getRouter() {
            return $this->make('router');
        }

        public function getPDO() {
            return $this->make('pdo');
        }
    }
