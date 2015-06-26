<?php
require_once(dirname(__FILE__).'/../../../../include/events/SqlResultIterator.inc');

class SqlResultIteratorTest extends PHPUnit_Framework_TestCase {
    private $iterator;

    public function setUp() {
        $this->entity = new SqlResultIterator(NULL, NULL);
    }

    public function testRewindResetsPosition() {
        $this->entity->pos = 1;
        $this->entity->rewind();

        $this->assertSame(0, $this->entity->pos);
    }

    public function testNextIncrementsPosition() {
        $this->entity->next();

        $this->assertSame(1, $this->entity->pos);
    }

    public function testKeyReturnsPosition() {
        $this->entity->pos = 10;

        $this->assertSame(10, $this->entity->key());
    }
}
