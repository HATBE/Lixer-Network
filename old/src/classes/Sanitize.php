<?php
    namespace app;

    class Sanitize {
        public static function int($input) {
            $r = htmlentities($input, ENT_QUOTES);
            $r = filter_var($r, FILTER_SANITIZE_NUMBER_INT);
            $r = empty($r) ? 0 : $r;
        
            return $r;
        }
        
        public static function string($input) {
            $r = htmlentities($input, ENT_QUOTES);
            $r = empty($r) ? 'null' : $r;
            
            return $r;
        }

        public static function email($input) {
            $r = filter_var($input, FILTER_SANITIZE_EMAIL);
        }
    }

