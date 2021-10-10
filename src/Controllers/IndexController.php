<?php
    namespace App\Controllers;

    use App\Controller;

    class IndexController extends Controller {
        public function index() {
            $this->render('index');
        }
    }