<?php
    namespace App\Controller;

    use App\Core\AbstractController;

    class IndexController extends AbstractController {
        
        public function index() {
            $this->render("Index/index", ['config' => $this->config]);
        }
        
    }