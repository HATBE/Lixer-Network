<?php
    namespace App;

    class Template {
        public static function load(string $name, array $data = array()) {
            $template = __DIR__ . '/Templates/' . $name . '.php';
            if(file_exists($template)) {
                extract($data, EXTR_SKIP);
                require($template);
            } else {
                echo "Template not found";
            }
        }
    }