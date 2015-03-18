<?php

namespace WPU\Response;

class PluginInfo extends ResponseAbstract {

    public $name = '';
    public $version = '';
    public $slug = '';
    public $author = '';
    public $tested = '';
    public $requires = '';
    public $compatibility = array();
    public $sections = array('changelog' => 'No changelog');

    public function __construct($object) {

        parent::__construct($object);

        if (isset($object->compatibility)) {
            foreach ((array)$object->compatibility as $plugin_version => $versions) {
                foreach ($versions as $version) {
                    $this->compatibility[$plugin_version][$version] = array(100, 100, 100);
                }
            }
        }

        $fileName = WPU_DATA_PATH . '/plugins/' . $this->slug . '/changelog.txt';

        if (file_exists($fileName)) {
            $this->sections['changelog'] = file_get_contents($fileName);
        }

    }

}