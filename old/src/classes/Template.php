<?php
    namespace app;

    class Template {
        public static function load(string $name, array $data = array()) {
            if(file_exists(__DIR__ . '/../templates/' . $name . '.php')) {
                extract($data, EXTR_SKIP);
                require(__DIR__ . '/../templates/' . $name . '.php');
            } else {
                echo "<b>Error!</b> Template <b>\"{$name}\"</b> not found!";
            }
        }
    }