<?php
    namespace App\Validation;

    class Validator {
        public static function username($username) {
            if(preg_match(USERNAME_REGEX, $username)) {
                return false;
            }

            return true;
        }

        public static function password($password) {
            if(preg_match(PASSWORD_REGEX, $password)) {
                return false;
            }

            return true;
        }
    }