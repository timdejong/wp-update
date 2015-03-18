<?php

namespace WPU\Server;

use WPU\Utils\RegEx;

class Request {

    const ACTION_DOWNLOAD = 'download';
    const ACTION_THEME_UPDATE = 'theme_update';
    const ACTION_PLUGIN_UPDATE = 'plugin_update';
    const ACTION_PLUGIN_INFO = 'plugin_information';

    public $uri = '';
    public $action = null;
    public $userAgent = '';
    public $origin = '';
    public $remoteAddress = '';
    public $apiKey = null;
    public $originalRequest = null;

    private static $instance;

    public function __construct() {

        $this->uri = $_SERVER['REQUEST_URI'];
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->remoteAddress = $_SERVER['REMOTE_ADDR'];
        $this->time = time();

        if(strpos($this->userAgent, 'http')){
            $this->origin = substr($this->userAgent, strpos($this->userAgent, 'http'));
        }

        if(isset($_POST['action'])){
            $this->action = strtolower($_POST['action']);
        }

        if(isset($_POST['api-key'])){
            $this->apiKey = $_POST['api-key'];
        }

        if(isset($_POST['request'])){
            $this->originalRequest = unserialize($_POST['request']);
        }

        if(RegEx::match('#^/download/#', $this->uri)){
            $this->action = self::ACTION_DOWNLOAD;
        }

        self::$instance = $this;

    }

    public static function get() {

        return self::$instance;

    }

    public function getHash() {

        return RegEx::get('#^/download/([^/]+)/#', $this->uri);

    }

}