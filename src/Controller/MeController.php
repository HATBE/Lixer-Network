<?php
    namespace App\Controller;

    use App\Core\AbstractController;

    class MeController extends AbstractController {
        
        public function index() {
            $this->render("Me/index", ['config' => $this->config]);
        }

        public function work() {
            $this->render("Me/work", ['config' => $this->config]);
        }

    }