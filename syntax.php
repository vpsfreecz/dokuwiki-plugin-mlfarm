<?php

if (!defined('DOKU_INC')) die();

class syntax_plugin_mlfarm extends DokuWiki_Syntax_Plugin {
    public function getType() {
        return 'substition';
    }

    public function getSort() {
        return 32;
    }

    public function getAllowedTypes() {
        return array('disabled');
    }

    public function connectTo($mode) {
        $this->Lexer->addEntryPattern('<page>(?=.*?</page>)', $mode, 'plugin_mlfarm');
    }

    public function postConnect() {
        $this->Lexer->addExitPattern('</page>', 'plugin_mlfarm');
    }
 
    public function handle($match, $state, $pos, Doku_Handler $handler){
        switch ($state) {
        case DOKU_LEXER_UNMATCHED:
            return $match;
        }

        return null;
    }
 
    public function render($mode, Doku_Renderer $renderer, $data) {
        if ($mode === 'xhtml')
            return true;

        $name = trim($data);

        if ($mode === 'metadata' && $name) {
            $renderer->meta['identifier'] = $name;
            $renderer->persistent['identifier'] = $name;
            return true;
        }

        return false;
    }
}
