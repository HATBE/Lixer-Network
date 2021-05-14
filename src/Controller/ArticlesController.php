<?php
    namespace App\Controller;

    use App\Core\AbstractController;
    use App\Core\Container;

class ArticlesController extends AbstractController {

        public $config;
        private $articlesService;

        public function __construct(Container $container) {

            $this->config = $container->getConfig();
            $this->articlesService = $container->getArticlesService();
            
        }

        public function list() {

            $articles = $this->articlesService->getArticles();

            $this->render("Articles/list", ['config' => $this->config, 'articles' => $articles]);
        }

        public function article($params = null) {

            $param = paramNumeric($params[0]);
            
            $article = $this->articlesService->getPostById($param);

            $this->render("Articles/article", ['config' => $this->config, 'article' => $article]);
        }

        public function category($params = null) {

            $param = param($params[0]);

            $this->render("Articles/category", ['config' => $this->config]);
        }

    }