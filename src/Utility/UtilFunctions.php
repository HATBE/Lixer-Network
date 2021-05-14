<?php
    function e($str) { // Functions removes mallisius code from String
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }

    function paramNumeric($param) {
        if(!empty($param)) {
            if(is_numeric($param)) {
                return e(floor($param));
            }
        }
        return null;
    }
    function param($param) {
        if(!empty($param)) {
            return e(floor($param));
        }
        return null;
    }