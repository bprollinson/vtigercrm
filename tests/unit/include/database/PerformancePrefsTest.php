<?php
require_once(dirname(__FILE__).'/../../../../include/database/PearDatabase.php');

class PerformancePrefsTest extends PHPUnit_Framework_TestCase {
    private $prefs;

    public function setUp() {
        $this->prefs = new PerformancePrefs();
    }

    public function testGetReturnsFalseWhenPerformanceConfigNotSet() {
        $this->assertSame(false, $this->prefs->get('key'));
    }

    public function testGetReturnsDefaultValueWhenPerformanceConfigNotSet() {
        $this->assertSame('defaultvalue', $this->prefs->get('key', 'defaultvalue'));
    }

    public function testGetReturnsConfigValueWhenPerformanceConfigSet() {
        global $PERFORMANCE_CONFIG;
        $PERFORMANCE_CONFIG['key'] = 'value';

        $this->assertSame('value', $this->prefs->get('key'));
    }

    public function testGetBooleanReturnsBooleanConfigValue() {
        global $PERFORMANCE_CONFIG;
        $PERFORMANCE_CONFIG['key'] = true;

        $this->assertSame(true, $this->prefs->getBoolean('key'));
    }

    public function testGetIntegerReturnsIntegerConfigValue() {
        global $PERFORMANCE_CONFIG;
        $PERFORMANCE_CONFIG['key'] = 12345;

        $this->assertSame(12345, $this->prefs->getInteger('key'));
    }

    public function testGetIntegerConversConfigValueToInteger() {
        global $PERFORMANCE_CONFIG;
        $PERFORMANCE_CONFIG['key'] = '12345';

        $this->assertSame(12345, $this->prefs->getInteger('key'));
    }
}
