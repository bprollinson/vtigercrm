<?php

require_once(dirname(__FILE__).'/../../../../include/events/VTEntityType.inc');

class VTEntityTypeTest extends PHPUnit_Framework_TestCase {
    public function testToArray() {
        $type = new VTFieldType();
        $type->field1 = 'value1';
        $type->field2 = 'value2';

        $expectedData = [
            'field1' => 'value1',
            'field2' => 'value2'
        ];
        $this->assertSame($expectedData, $type->toArray());
    }
}
