<?php
/**
 * Multilingual farms
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Jakub Skokan <jakub.skokan@vpsfree.cz>
 */

if (!defined('DOKU_INC')) die();

class helper_plugin_mlfarm extends DokuWiki_Plugin {
    public function getMethods() {
        return array(
            array(
                'name' => 'showTranslations',
                'desc' => 'return a HTML formatted string with links to translations',
                'params' => array(
                    'id (optional)' => 'string',
                ),
            ),
        );
    }

    public function showTranslations($id = null) {
        global $ACT, $INFO;

        if ($ACT != 'show')
            return '';

        if (!$id)
            $id = $INFO['id'];

        $gid = p_get_metadata($id, 'identifier');
        $translations = \dokuwiki\plugin\mlfarm\Cache::getInstance()->get($gid);

        if (!$translations)
            return '';

        ksort($translations);

        $s = '<div class="translations">';
        $s .= '<ul>';

        foreach ($translations as $lang => $opts) {
            $s .= '<li><a href="'.$opts['url'].'">'.$lang.'</a></li>';
        }

        $s .= '</ul>';
        $s .= '</div>';

        return $s;
    }
}
