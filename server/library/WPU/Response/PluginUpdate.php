<?php

namespace WPU\Response;

use WPU\Server\Request;

class PluginUpdate extends ResponseAbstract {

    public $url = '';
    public $slug = '';
    public $plugin = '';
    public $new_version = '';
    public $package = '';

    public function __construct($object, $hash) {

        $request = Request::get();

        $this->url = $request->origin;
        $this->slug = $request->originalRequest['slug'];
        $this->plugin = $request->originalRequest['plugin'];
        $this->new_version = $object->version;
        $this->package = $hash;

    }

}