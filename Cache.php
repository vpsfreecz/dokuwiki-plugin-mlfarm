<?php

namespace dokuwiki\plugin\mlfarm;

if (!defined('DOKU_INC')) die();

class Cache {
    private static $instance = null;

    public static function getInstance() {
        if (!$instance)
            $instance = new Cache();

        return $instance;
    }

    private function __construct() {
        $this->path = DOKU_INC.'/data/cache/mlfarm.dat';
        $this->cache = null;
    }

    public function set($id, $lang, $opts) {
        $cache = null;
        $tmp = $this->path.'.new';

        if (file_exists($this->path)) {
            $cache = unserialize(file_get_contents($this->path));

        } else {
            $cache = array();
        }
        
        $fd = fopen($tmp, 'w');
        flock($fd, LOCK_EX);

        if (!isset($cache[$id]))
            $cache[$id] = array();

        $cache[$id][$lang] = $opts;

        fwrite($fd, serialize($cache));
        fflush($fd);
        flock($fd, LOCK_UN);
        fclose($fd);

        rename($tmp, $this->path);
    }

    public function get($id, $lang = null) {
        if ($this->cache === null)
            $this->load();

        if ($lang)
            return $this->cache[$id][$lang];

        else
            return $this->cache[$id];
    }

    private function load() {
        if (!file_exists($this->path)) {
            $this->cache = array();
            return;
        }

        $this->cache = unserialize(file_get_contents($this->path));
    }
}
