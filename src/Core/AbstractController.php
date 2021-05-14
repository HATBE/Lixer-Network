<?php
  namespace App\Core;

  use App\Core\Container;

  abstract class AbstractController {

    public $config;
    public $router;

    public function __construct(Container $container) {
      $this->config = $container->getConfig();
      $this->router = $container->getRouter();
    }

    protected function render($view, $params) {
      // extract Params array to single variables
      extract($params);

      if($view != "Index/maintenance") { // check if view is non of the given
        include(__DIR__ . "/../../views/layout/header.php"); // Display Header
      }
      include(__DIR__ . "/../../views/{$view}.php"); // display view
      if($view != "Index/maintenance") { // check if view is non of the given
        include(__DIR__ . "/../../views/layout/footer.php"); // display footer
      }
    }

    public function index() {
      // Default Message if no Index Method is defined
      echo "<h1><b>Nothing to see here.</b></h1><br>If you think this is incorrect, please contact the webmaster (webmaster@lixer.com).".(isset($_SERVER['PATH_INFO']) ? '<br> Error occurred while calling "'.e($_SERVER['PATH_INFO']).'"' : '');
    }

  }
