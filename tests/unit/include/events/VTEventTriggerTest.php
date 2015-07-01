<?php

require_once(dirname(__FILE__).'/../../../../include/events/VTEventTrigger.inc');

class VTEventTriggerTest extends PHPUnit_Framework_TestCase {
    public function testConstructor() {
        $db = new stdClass;
        $db->dbname = 'mydbname';
        $trigger = new VTEventTrigger($db, 'mytrigger');

        $this->assertSame($db, $trigger->adb);
        $this->assertSame('mytrigger', $trigger->name);
    }

    public function testIsCachedForName() {
        $this->resetCaches();
        $this->addPairToCache('key', 'value');

        $this->assertSame(true, VTEventTrigger::isCached('key'));
    }

    public function testClearCacheForName() {
        $this->resetCaches();
        $this->addPairToCache('key', 'value');
        VTEventTrigger::clearCache('key');

        $this->assertSame(false, VTEventTrigger::isCached('key'));
    }

    public function testLookupCacheForName() {
        $this->resetCaches();
        $this->addPairToCache('key', 'value');

        $this->assertSame('value', VTEventTrigger::lookupCache('key'));
    }

    public function testGetInstance() {
        $db = new stdClass;
        $db->dbname = 'mydbname';
        $instance = VTEventTrigger::getInstance($db, 'mytrigger');

        $this->assertInstanceOf('VTEventTrigger', $instance);
        $this->assertSame($db, $instance->adb);
        $this->assertSame('mytrigger', $instance->name);
    }

    public function testGetBatchInstance() {
        $db = new stdClass;
        $db->dbname = 'mydbname';
        $instance = VTEventTrigger::getInstance($db, 'mybatchtrigger');

        $this->assertInstanceOf('VTBatchEventTrigger', $instance);
        $this->assertSame($db, $instance->adb);
        $this->assertSame('mybatchtrigger', $instance->name);    
    }

    private function resetCaches() {
        $reflection = new ReflectionClass('VTEventTrigger');

        $cacheProperty = $reflection->getProperty('cache');
        $cacheProperty->setAccessible(true);
        $cacheProperty->setValue([]);

        $cacheLookupTypeProperty = $reflection->getProperty('cacheLookupType');
        $cacheLookupTypeProperty->setAccessible(true);
        $cacheLookupTypeProperty->setValue('');
    }

    private function addPairToCache($key, $value) {
        $reflection = new ReflectionClass('VTEventTrigger');

        $cacheProperty = $reflection->getProperty('cache');
        $cacheProperty->setAccessible(true);
        $values = $cacheProperty->getValue();
        $values[$key] = $value;
        $cacheProperty->setValue($values);
    }
}
