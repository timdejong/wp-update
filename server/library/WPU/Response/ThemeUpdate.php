<?php

namespace WPU\Response;

use WPU\Server\Request;

class ThemeUpdate extends ResponseAbstract {

    public $url = '';
    public $new_version = '';
    public $package = '';

    public function __construct($object, $hash) {

        $request = Request::get();

        $this->url = $request->origin;
        $this->new_version = $object->version;
        $this->package = $hash;

    }

}