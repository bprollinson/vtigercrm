<?php

require_once(dirname(__FILE__).'/../../../../include/events/VTEventsManager.inc');
require_once(dirname(__FILE__).'/../../../../include/events/VTEventTrigger.inc');

class VTEventsManagerTest extends PHPUnit_Framework_TestCase {
    public function testConstructor() {
        $db = new stdClass;
        $db->databaseName = 'mydatabasename';
        $manager = new VTEventsManager($db);

        $this->assertSame($db, $manager->adb);
    }

    public function testClearTriggerCacheResetsCache() {
        $reflection = new ReflectionClass('VTEventTrigger');

        $cache = $reflection->getProperty('cache');
        $cache->setAccessible(true);
        $value = [
            'key1' => 'value1',
            'key2' => 'value2'
        ];
        $cache->setValue($value);

        $cache = $reflection->getProperty('cacheLookupType');
        $cache->setAccessible(true);
        $cache->setValue(VTEventTrigger::CACHE_LOOKUP_TYPE_ALL);

        $manager = new VTEventsManager(new stdClass);
        $manager->clearTriggerCache();

        $this->assertSame(false, VTEventTrigger::isCached());
    }

    public function testClearTriggerCacheClearsOneCacheEntry() {
        $reflection = new ReflectionClass('VTEventTrigger');

        $cache = $reflection->getProperty('cache');
        $cache->setAccessible(true);
        $value = [
   	        'key1' => 'value1',
   	        'key2' => 'value2'
        ];
        $cache->setValue($value);

        $cache = $reflection->getProperty('cacheLookupType');
        $cache->setAccessible(true);
        $cache->setValue(VTEventTrigger::CACHE_LOOKUP_TYPE_ALL);

        $manager = new VTEventsManager(new stdClass);
        $manager->clearTriggerCache('key1');

        $this->assertSame(false, VTEventTrigger::isCached('key1'));
        $this->assertSame(true, VTEventTrigger::isCached('key2'));
    }

    public function testGetTrigger() {
        $db = new stdClass;
        $db->name = 'mydb';

        $manager = new VTEventsManager($db);
        $trigger = $manager->getTrigger('standardtrigger');

        $this->assertInstanceOf('VTEventTrigger', $trigger);
        $this->assertSame('standardtrigger', $trigger->name);
        $this->assertSame($db, $trigger->adb);
    }

    public function testGetBatchTrigger() {
        $db = new stdClass;
        $db->name = 'mydb';

        $manager = new VTEventsManager($db);
        $trigger = $manager->getTrigger('samplebatchtrigger');

        $this->assertInstanceOf('VTBatchEventTrigger', $trigger);
        $this->assertSame('samplebatchtrigger', $trigger->name);
        $this->assertSame($db, $trigger->adb);
    }
}
