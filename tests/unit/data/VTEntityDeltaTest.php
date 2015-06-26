<?php

require_once(dirname(__FILE__).'/../../../data/VTEntityDelta.php');

class VTEntityDeltaTest extends PHPUnit_Framework_TestCase {
    private $entity;

    public function setUp() {
        $this->entity = new VTEntityDelta(); 
    }

    public function testGetEntityDeltaReturnsCachedDelta() {
        self::setEntityDelta(['module' => [1 => 2]]);

        $this->assertEquals(2, $this->entity->getEntityDelta('module', 1));
    }

    public function testGetOldValueReturnsCachedValue() {
        self::setEntityDelta(['module' => [1 => ['field' => ['oldValue' => 2]]]]);

        $this->assertEquals(2, $this->entity->getOldValue('module', 1, 'field'));
    }

    public function testGetCurrentValueReturnsCachedValue() {
        self::setEntityDelta(['module' => [1 => ['field' => ['currentValue' => 2]]]]);

        $this->assertEquals(2, $this->entity->getCurrentValue('module', 1, 'field'));
    }

    public function testGetOldEntityReturnsCachedValue() {
        self::setOldEntity(['module' => [1 => 2]]);

        $this->assertEquals(2, $this->entity->getOldEntity('module', 1));
    }

    public function testGetNewEntityReturnsCachedValue() {
        self::setNewEntity(['module' => [1 => 2]]);

        $this->assertEquals(2, $this->entity->getNewEntity('module', 1));
    }

    public function testHasChangedReturnsFalseWhenOldEntityNotSet() {
        self::setOldEntity(NULL);
        $delta = [
            'module' => [
                1 => [
                    'field' => [
                        'oldValue' => 'oldValue',
                        'currentValue' => 'newValue'
                    ]
                ]
            ]
        ];
        self::setEntityDelta($delta);

        $this->assertSame(false, $this->entity->hasChanged('module', 1, 'field'));
    }

    public function testHasChangedReturnsFalseWhenValueNotChanged() {
        self::setOldEntity(['module' => [1 => 'oldEntity']]);
        $delta = [
   	        'module' => [
   	            1 => [
   	                'field' => [
   	                    'oldValue' => 'oldValue',
                        'currentValue' => 'oldValue'
                    ]
                ]
            ]
        ];
        self::setEntityDelta($delta);

        $this->assertSame(false, $this->entity->hasChanged('module', 1, 'field'));
    }

    public function testHasChangedReturnsTrueWhenValueChanged() {
        self::setOldEntity(['module' => [1 => 'oldEntity']]);
        $delta = [
            'module' => [
                1 => [
                    'field' => [
                        'oldValue' => 'oldValue',
                        'currentValue' => 'newValue'
                    ]
                ]
            ]
        ];
        self::setEntityDelta($delta);

        $this->assertSame(true, $this->entity->hasChanged('module', 1, 'field'));
    }

    public function testHasChangedReturnsTrueWhenValueMatchesSpecifiedValue() {
        self::setOldEntity(['module' => [1 => 'oldEntity']]);
        $delta = [
            'module' => [
                1 => [
                    'field' => [
                        'oldValue' => 'oldValue',
                        'currentValue' => 'newValue'
                    ]
                ]
            ]
        ];
        self::setEntityDelta($delta);

        $this->assertSame(true, $this->entity->hasChanged('module', 1, 'field', 'newValue'));
    }

    public function testHasChangedReturnsFalseWhenValueDoesNotMatchSpecifiedValue() {
        self::setOldEntity(['module' => [1 => 'oldEntity']]);
        $delta = [
            'module' => [
                1 => [
                    'field' => [
                        'oldValue' => 'oldValue',
                        'currentValue' => 'newValue'
                    ]
                ]
            ]
        ];
        self::setEntityDelta($delta);

        $this->assertSame(false, $this->entity->hasChanged('module', 1, 'field', 'otherValue'));
    }

    private static function setEntityDelta($value) {
        $reflection = new ReflectionClass('VTEntityDelta');
        $property = $reflection->getProperty('entityDelta');
        $property->setAccessible(true);

        $property->setValue($value);
    }

    private static function setOldEntity($value) {
        $reflection = new ReflectionClass('VTEntityDelta');
        $property = $reflection->getProperty('oldEntity');
        $property->setAccessible(true);

        $property->setValue($value);
    }

    private static function setNewEntity($value) {
        $reflection = new ReflectionClass('VTEntityDelta');
        $property = $reflection->getProperty('newEntity');
        $property->setAccessible(true);

        $property->setValue($value);
    }
}
