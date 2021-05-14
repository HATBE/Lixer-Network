<?php
    namespace App\Controller;

    use App\Core\AbstractController;

    class IndexController extends AbstractController {
        
        public function index() {
            $this->render("Index/index", ['config' => $this->config]);
        }

        public function maintenance() {
            $this->render("Index/maintenance", ['config' => $this->config]);
        }

        public function contact() {
            $this->render("Index/contact", ['config' => $this->config]);
        }

        public function impressum() {
            $this->render("Index/impressum", ['config' => $this->config]);
        }

        public function pageinfo() {
            $this->render("Index/pageinfo", ['config' => $this->config]);
        }

        public function hatbe($params = null) {
            if(isset($params[0])) {
                echo e($params[0]);
            }
            echo '<br>testing';
        }

        
    }