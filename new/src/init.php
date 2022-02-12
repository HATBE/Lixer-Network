<?php
    require_once(__DIR__ . '/../config/config.php');
    require_once(__DIR__ . '/../config/auth.php');
    require_once(__DIR__ . '/autoload.php');

    use app\Database;
    use app\Sanitize;

    $db = new Database();

    $url = null;
    if(isset($_SERVER['PATH_INFO'])) {
        $url = rtrim($_SERVER['PATH_INFO'], '/'); // remove last slash
        $url = substr($url, 1); // remove first slash
        $url = htmlspecialchars(filter_var($url, FILTER_SANITIZE_URL)); // sanitize URL
        $url = explode('/', $url);
        if(!str_contains(explode('/', substr(rtrim($_SERVER['REQUEST_URI']), 1))[0], '.php')) array_shift($url);
        if(empty($url) || $url[0] == '') $url = '';
    }

    $itemsPerPage = ITEMS_PER_PAGE;
    $page = isset($_GET['page']) ? Sanitize::int($_GET['page']) : 1;