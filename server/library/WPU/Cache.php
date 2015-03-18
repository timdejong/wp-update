<?php

namespace WPU;

use WPU\Server\Request;

class Cache {

    const TIMEOUT = 3600;

    private static function getFileName($hash) {

        $request = Request::get();
        $fileName = WPU_CACHE_PATH . '/' . $request->remoteAddress . '-' . $hash;

        return $fileName;

    }

    public static function get($hash) {

        $fileName = self::getFileName($hash);

        if(file_exists($fileName)){

            $contents = file_get_contents($fileName);
            $object = unserialize($contents);

            return $object;

        }

        return null;

    }

    public static function create($slug, $type, $version) {

        $request = Request::get();

        $package = new \stdClass;
        $package->ip = $request->remoteAddress;
        $package->origin = $request->origin;
        $package->type = $type;
        $package->time = time() + self::TIMEOUT;
        $package->version = $version;
        $package->slug = $slug;
        $package->security = sha1(sha1(WPU_SALT . serialize($package)));

        $data = serialize($package);
        $hash = substr(md5($data), 22);

        $fileName = self::getFileName($hash);

        file_put_contents($fileName, $data);

        return $hash;

    }

}