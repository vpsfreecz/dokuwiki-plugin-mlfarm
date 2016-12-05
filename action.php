<?php
/**
 * Multilingual farms
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Jakub Skokan <jakub.skokan@vpsfree.cz>
 */

if (!defined('DOKU_INC')) die();

class action_plugin_mlfarm extends DokuWiki_Action_Plugin {
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('DOKUWIKI_STARTED', 'AFTER', $this, 'onDokuStarted');
        $controller->register_hook('PARSER_METADATA_RENDER', 'AFTER', $this, 'onMetadataRender');
    }

    /**
     * Save the current page to the cache file when on master animal.
     */
    public function onDokuStarted(Doku_Event $event) {
        global $ID, $ACT, $conf;

        if (!$ID || $ACT != 'show' || !$this->getConf('master'))
            return;

        $cache = \dokuwiki\plugin\mlfarm\Cache::getInstance();
        $translations = $cache->get($ID);

        if ($translations && isset($translations[$conf['lang']]))
            return;

        $link = wl($ID);

        $cache->set($ID, $conf['lang'], array(
            'base_url' => DOKU_URL,
            'local_id' => $ID,
            'link' => $link,
            'url' => rtrim(DOKU_URL, '/').$link,
        ));
    }

    public function onMetadataRender(Doku_Event $event) {
        global $ID, $conf;

        if (!$ID)
            return;
        
        $gid = $event->data['persistent']['identifier'];

        if (!$gid)
            return;

        $link = wl($ID);

        \dokuwiki\plugin\mlfarm\Cache::getInstance()->set($gid, $conf['lang'], array(
            'base_url' => DOKU_URL,
            'local_id' => $ID,
            'link' => $link,
            'url' => rtrim(DOKU_URL, '/').$link,
        ));
    }
}
