<?php
    namespace App\Controller;

    use App\Core\AbstractController;

    class ErrorsController extends AbstractController {

            private $errors;

            public function __construct($config) {
                $this->config = $config;

                $json = file_get_contents(__DIR__ . "../../../../json/errors.json");
                $this->errors = json_decode($json, true);
            }

            public function error($error = 404) {
                if(!empty($this->errors[$error])) {
                    echo $this->errors[$error]['text'];
                } else {
                    echo "undefined Error";
                }
                die();
            }
        }