<?php

namespace Application\Models\Cms\Components\Navigation;

use DOMDocument;

class NavigationComponent extends \Alien\Mvc\Component\NavigationComponent {

    public function render()
    {
        $doc = new DOMDocument('1.0');
        $ul = $doc->createElement('ul');
        $ul->setAttribute('class', 'nav nav-justified');
        $ul->setAttribute('ng-model', 'cms');
        foreach($this->links as $name => $link) {
            $li = $doc->createElement('li');
            $a = $doc->createElement('a', $name);
            $a->setAttribute('href', $link);
            $li->appendChild($a);
            $ul->appendChild($li);
        }
        $doc->appendChild($ul);
        return $doc->saveHTML();
    }


} 