<?php
require_once(dirname(__FILE__).'/../../../../include/events/SqlResultIterator.inc');

class SqlResultIteratorRowTest extends PHPUnit_Framework_TestCase {
    private $row;
	
    public function setUp() {
        $this->row = new SqlResultIteratorRow(['column' => 'value']);
    }

    public function testGetReturnsColumnData() {
        $this->assertEquals('value', $this->row->get('column'));
    }

    public function testPropertyAccessReturnsColumnData() {
        $this->assertEquals('value', $this->row->column);
    }
}
