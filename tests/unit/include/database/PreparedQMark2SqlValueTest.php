<?php
require_once(dirname(__FILE__).'/../../../../include/database/PearDatabase.php');

class PreparedQMark2SqlValueTest extends PHPUnit_Framework_TestCase {
    private $converter;

    public function setUp() {
        $this->converter = new PreparedQMark2SqlValue([
            'value1',
            'value2'
        ]);
    }

    public function testCallReturnsFirstMatchWhenQuestionMarkNotProvided() {
        $matches = [
            'match1',
            'match2',
            'match3',
            'match4'
        ];
        $this->assertSame('match1', $this->converter->call($matches));
    }

    public function testCallReturnsFirstValueOnFirstExecution() {
        $matches = [
            'match1',
            'match2',
            'match3',
            '?'
        ];
        $this->assertSame('value1', $this->converter->call($matches));
    }

    public function testCallReturnsSubsequentValueOnSubsequentExecution() {
        $matches = [
            'match1',
            'match2',
            'match3',
            '?'
        ];
        $this->converter->call($matches);
        $this->assertSame('value2', $this->converter->call($matches));
    }
}
