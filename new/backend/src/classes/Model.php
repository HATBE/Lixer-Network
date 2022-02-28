<?php
    namespace app;

    abstract class Model {
        protected $_db = null;
        protected $_data = null;
        protected $_exists = false;

        protected function getData($key, $escape = true) {
            $return = isset($this->_data->$key) ? $this->_data->$key : 'no Data';
            $return = $escape ? htmlspecialchars($return) : $return;
            return $return;
        }

        public function exists() {
            return $this->_exists;
        }
    }