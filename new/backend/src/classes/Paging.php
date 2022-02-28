<?php
    namespace app;

    class Paging {
        private $_page;
        private $_itemsCount;
        private $_itemsPerPage;
        private $_maxPages;

        public function __construct($itemsCount, $page, $itemsPerPage) {
            $this->_itemsCount = intval($itemsCount);
            $this->_page = intval($page);
            $this->_itemsPerPage = intval($itemsPerPage);
            $this->_maxPages = ceil($this->_itemsCount / $this->_itemsPerPage);
        }

        public function pageExists() {
            if($this->_page > $this->_maxPages || $this->_page <= 0) {
                return false;
            }
            return true;
        }

        public function getOffset() {
            return ($this->_page === 1 ? 0 : $this->_itemsPerPage * ($this->_page - 1));
        }

        public function getRData() {
            $data = [];
            $data['rows_returned'] = $this->_page == $this->_maxPages ? $this->_itemsCount / $this->_maxPages : $this->_itemsPerPage;
            $data['total_rows'] = $this->_itemsCount;
            $data['total_pages'] = $this->_maxPages;
            $data['has_next_page'] = $this->_page >= $this->_maxPages ? false : true;
            $data['has_last_page'] = $this->_page >= 2 ? true : false;

            return $data;
        }
    }