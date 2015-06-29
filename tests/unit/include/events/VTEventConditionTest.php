<?php

require_once(dirname(__FILE__).'/../../../../include/events/VTEventCondition.php');

class VTEventConditionTest extends PHPUnit_Framework_TestCase {
    public function testConstructorConstructsConditionWithoutExpression() {
    	$condition = new VTEventCondition('');
        $this->assertSame(NULL, $condition->expr);
    }
}
