<?php

namespace WPU\Response;

class ResponseAbstract {

    /**
     * @param $object
     * @return ResponseAbstract
     */
    public function __construct($object) {

        $properties = get_object_vars($object);

        foreach($properties as $property => $value){
            if(property_exists($this, $property) && gettype($value) !== 'object'){
                $this->$property = $value;
            }
        }

    }

    public function __toString() {

        $output = new \stdClass;

        foreach($this as $property => $value){
            $output->$property = $value;
        }

        return serialize($output);

    }

    public function getPackageUrl($hash, $slug) {

        $protocol = 'http';

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $protocol = 'https';
        }

        return $protocol . '://' . WPU_HOSTNAME . '/download/' . $hash . '/' . $slug;


    }

}