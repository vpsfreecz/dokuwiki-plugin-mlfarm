<?php
/**
 * Multilingual farms
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Jakub Skokan <jakub.skokan@vpsfree.cz>
 */

namespace dokuwiki\plugin\mlfarm;

if (!defined('DOKU_INC')) die();

class Cache {
    private static $instance = null;

    public static function getInstance($cacheFile) {
        if (!$instance)
            $instance = new Cache($cacheFile);

        return $instance;
    }

    private function __construct($cacheFile) {
        if ($cacheFile)
            $this->path = $cacheFile;
        else
            $this->path = DOKU_INC.'/data/meta/mlfarm.map.dat';
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
        $this->cache = $cache;

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
