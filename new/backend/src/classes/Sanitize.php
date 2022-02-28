<?php
    namespace app;

    class Sanitize {

        // \/ sanitize stuff \/

        public static function int($input) {
            $r = htmlentities($input, ENT_QUOTES);
            $r = filter_var($r, FILTER_SANITIZE_NUMBER_INT);
            $r = empty($r) ? 0 : $r;
        
            return $r;
        }
        
        public static function string($input) {
            $r = trim(htmlentities($input, ENT_QUOTES));
            $r = empty($r) ? 'null' : $r;
            
            return $r;
        }

        public static function email($input) {
            $r = filter_var($input, FILTER_SANITIZE_EMAIL);

            return $r;
        }

        public static function url($input) {
            $r = htmlspecialchars(filter_var($input, FILTER_SANITIZE_URL));

            return $r;
        }

        // /\ sanitize stuff /\
        // \/ boolean stuff \/

        public static function checkInt($int) {
            if($int != '' && is_numeric($int)) {
                return true;
            }
            return false;
        }

        public static function checkString($str) {
            if($str != '' && is_string($str)) {
                return true;
            }
            return false;
        }

        public static function checkStringBetween($str, $a, $b) {
            if(strlen($str) >= $a && strlen($str) <= $b) {
                return true;
            }
            return false;
        }

        // /\ boolean stuff /\
    }

