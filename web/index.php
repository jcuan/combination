<?php
define('jcuan_time',microtime(true));
require "../init.php";
Star\Core\Route::init(App\Urls\Main::getUrlFunc());