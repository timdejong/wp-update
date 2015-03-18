<?php

header('Content-Type: text/plain');

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

require_once 'config.php';

require_once 'library/WPU/Cache.php';
require_once 'library/WPU/Utils/RegEx.php';
require_once 'library/WPU/Server.php';
require_once 'library/WPU/Server/Request.php';
require_once 'library/WPU/Response/ResponseAbstract.php';
require_once 'library/WPU/Response/PluginInfo.php';
require_once 'library/WPU/Response/PluginUpdate.php';
require_once 'library/WPU/Response/ThemeUpdate.php';

WPU\Server::dispatch();