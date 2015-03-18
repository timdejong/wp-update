<?php

namespace WPU;

use WPU\Server\Request;

class Server {

    public $request;

    public static function dispatch() {
        new self;
    }

    public function __construct() {

        $this->request = new Request;

        $this->processRequest();

    }

    private function processRequest() {

        /**
         * If an action was defined, proceed to the switch else show an empty screen
         * Don't give hackers any clue on what to do
         */
        if(!empty($this->request->action)){

            switch($this->request->action) {
                case Request::ACTION_DOWNLOAD:
                    $this->download();
                    break;
                case Request::ACTION_THEME_UPDATE:
                case Request::ACTION_PLUGIN_UPDATE:
                    $this->update();
                    break;
                case Request::ACTION_PLUGIN_INFO:
                    $this->pluginInfo();
                    break;
            }

        }

    }

    public function readData($path) {

        if(file_exists($path . '/data.json')){

            $contents = file_get_contents($path . '/data.json');
            $object = json_decode($contents);

            return $object;

        }

        return null;

    }

    private function download() {

        $hash = $this->request->getHash();
        $object = Cache::get($hash);

        if(isset($object->type) && in_array($object->type, array('theme', 'plugin'))){

            if($object->type == 'theme'){
                $path = WPU_DATA_PATH . '/themes/' . $object->slug;
            }
            else{
                $path = WPU_DATA_PATH . '/plugins/' . $object->slug;
            }

            $data = $this->readData($path);

            if(isset($data->version)){

                $fileName = $path . '/' . $data->version . '/' . $object->slug . '.zip';

                if(file_exists($fileName)){

                    header("Content-Type: application/zip");
                    header("Content-Disposition: attachment; filename=" . $object->slug . '.zip');
                    header("Content-Length: " . filesize($fileName));

                    readfile($fileName);

                }

            }

        }

    }

    private function update() {

        if(isset($this->request->originalRequest['slug'])) {

            $slug = $this->request->originalRequest['slug'];
            $type = current(explode('_', $this->request->action));

            if($type == 'theme'){
                $path = WPU_DATA_PATH . '/themes/' . $slug;
            }
            else{
                $path = WPU_DATA_PATH . '/plugins/' . $slug;
            }

            if(file_exists($path)){

                $data = $this->readData($path);

                if(!empty($data->version)){

                    $fileName = $path . '/' . $data->version . '/' . $slug . '.zip';
                    $hash = Cache::create($slug, $type, $data->version);

                    if(file_exists($fileName)){
                        if($type == 'theme'){
                            echo new Response\ThemeUpdate($data, $hash);
                        }
                        else{
                            echo new Response\PluginUpdate($data, $hash);
                        }
                    }

                }

            }

        }

    }

    private function pluginInfo() {

        if(isset($this->request->originalRequest['slug'])){

            $slug = $this->request->originalRequest['slug'];
            $path = WPU_DATA_PATH . '/plugins/' . $slug;

            if(file_exists($path)){

                $data = $this->readData($path);

                if(!empty($data)){
                    echo new Response\PluginInfo($data);
                }

            }

        }

    }

}