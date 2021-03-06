<?php
final class Caches {
    private $expire = 3600;

    public $dirCache = "caches/";

    public function __construct() {
        $this->dirCache = DIR_CACHE."caches/";

        if (!file_exists($this->dirCache)) {
            mkdir($this->dirCache, 0760, true);
        }
        $this->expire = (defined('CACHE_EXPIRE')) ? CACHE_EXPIRE : 3600;

    }

    public function verify($key) {
        $files = $this->valid($key);

        if ($files) {

            return true;

        } else {

            return false;
        }
    }

    public function check($key) {
        return $this->verify($key);
    }

    public function get($key) {

        $file = $this->valid($key);

        if ($file) {
            $cache = file_get_contents($file);

            return $this->decode($cache);
        }
    }

    public function set($key, $value, $expire = true) {
        $this->delete($key);

        $file = $this->dirCache  . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.cache';

        return file_put_contents($file, $this->encode($value));

    }

    public function delete($key) {
        $files = glob($this->dirCache . preg_replace('/[^A-Z0-9\.\*_-]/i', '', $key) . '.cache');

        if ($files) {
            foreach ($files as $file) {
                
                if (file_exists($file)) {
                    
                    unlink($file);
                    
                }
            }
        }

        return (count($files));
    }

    private function encode($value){

        if(function_exists('igbinary_serialize')){
            return igbinary_serialize($value);
        } else {
            return serialize($value);
        }

    }

    private function decode($value){

        if(function_exists('igbinary_serialize')){
            return igbinary_unserialize($value);
        } else {
            return unserialize($value);
        }
    }

    private function valid($key) {
        $file = ($this->dirCache . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.cache');

        if (file_exists($file)) {

            $time = filemtime($file) + $this->expire;


            if ($time < time() and $time !== '0') {

                unlink($file);

                return false;

            } else {

                return $file;

            }
        } else {
            return false;
        }
    }

    public function deleteAll() {
        $files = glob($this->dirCache . '*.cache');

        array_map('unlink', $files);

        unset($files);

        return true;
    }

    public function clear() {
        return $this->deleteAll();
    }

    public function stats() {

        $obj = new stdClass();

        $obj->size = $this->GetDirectorySize($this->dirCache);
        $obj->info = NULL;
        $obj->rawData = NULL;
        $obj->data = NULL;

        return $obj;
    }

    private function GetDirectorySize($path){
        $bytestotal = 0;
        $path = realpath($path);
        if($path!==false){
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;

    }
}