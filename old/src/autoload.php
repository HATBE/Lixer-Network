<?php
    namespace app;

    spl_autoload_register(function($class) {
        $prefix = 'app\\';
        $baseDir = __DIR__ . '/classes/';

        $len = strlen($prefix);
        if(strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
        if(file_exists($file)) {
            require($file);
        }
    });