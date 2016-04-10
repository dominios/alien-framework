<?php

use Alien\View\DOMElement;

class DOMElementTest extends PHPUnit_Framework_TestCase
{

    public function testFluidInterface()
    {
        $element = new DOMElement();
        $element
            ->emptyElement()
            ->setId('testId')
            ->setIsPairTag(true)
            ->setContent('lorem ipsum')
            ->setClass('visible')
            ->addClass('foo')
            ->toggleClass('bar')
            ->removeClass('foo')
            ->append(new DOMElement())
            ->setAttributes([
                'data-foo' => 'bar'
            ])
            ->setAttribute('foo', 'bar');
        $this->assertInstanceOf('Alien\View\DOMElement', $element);
    }

    public function testRenderPairedElement()
    {
        $child = new DOMElement('i');
        $child->setClass('ipsum');

        $element = new DOMElement('a');
        $element
            ->setIsPairTag(true)
            ->addClass('btn')
            ->attr('href', '#')
            ->attr('target', '_blank')
            ->setContent('lorem')
            ->append($child);

        $result = '<a class="btn" href="#" target="_blank">lorem<i class="ipsum"></i></a>';
        $this->assertEquals($result, $element->render());
    }

    public function testRenderNonPairedElement()
    {
        $element = new DOMElement('input');
        $element
            ->setIsPairTag(false)
            ->setClass('input')
            ->setAttributes([
                'type' => 'text',
                'value' => 'foo'
            ]);
        $result = '<input class="input" type="text" value="foo">';
        $this->assertEquals($result, $element->render());
    }

}