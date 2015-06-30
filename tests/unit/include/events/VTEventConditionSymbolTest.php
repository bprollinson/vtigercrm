<?php

require_once(dirname(__FILE__).'/../../../../include/events/VTEventConditionSymbol.php');

class VTEventConditionSymbolTest extends PHPUnit_Framework_TestCase {
    public function testConstructor() {
        $symbol = new VTEventConditionSymbol('SYMBOL');

        $this->assertSame('SYMBOL', $symbol->name);
    }
}
