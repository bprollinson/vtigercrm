<?php

require_once(dirname(__FILE__).'/../../../../include/events/VTEntityData.inc');

//TODO: Determine where the actual class definitions come from
class ImportLead {
}

class ImportAccount {
}

class ImportContact {
}

class ImportOpportunity {
}

class ImportProduct {
}

class ImportTicket {
}

class ImportVendors {
}

class VTEntityDataTest extends PHPUnit_Framework_TestCase {
    public function testFromCRMEntityCreatesNewDataFromEntity() {
        $entity = new stdClass;
        $entity->field = 'value';
        $data = VTEntityData::fromCRMEntity($entity);

        $expectedData = new VTEntityData();
        $this->setIsNew($expectedData, true);
        $expectedData->focus = $entity;

        $this->assertEquals($expectedData, $data);
    }

    public function testFromCRMEntityCreatesExistingDataFromEntity() {
        $entity = new stdClass;
        $entity->id = 1;
        $entity->field = 'value';
        $data = VTEntityData::fromCRMEntity($entity);

        $expectedData = new VTEntityData();
        $this->setIsNew($expectedData, false);
        $expectedData->focus = $entity;

        $this->assertEquals($expectedData, $data);
    }

    public function testGetData() {
        $entity = new stdClass;
        $entity->column_fields = [
            'field1' => 'value1',
            'field2' => 'value2'
        ];
        $data = VTEntityData::fromCRMEntity($entity);

        $expectedData = [
            'field1' => 'value1',
            'field2' => 'value2'
        ];
        $this->assertSame($expectedData, $data->getData());
    }

    public function testGetId() {
        $entity = new stdClass;
        $entity->id = 1;
        $data = VTEntityData::fromCRMEntity($entity);

        $this->assertSame(1, $data->getId());
    }

    public function moduleNameProvider() {
        return [
            ['ImportLead', 'Leads'],
            ['ImportAccount', 'Accounts'],
            ['ImportContact', 'Contacts'],
            ['ImportOpportunity', 'Potentials'],
            ['ImportProduct', 'Products'],
            ['ImportTicket', 'HelpDesk'],
            ['ImportVendors', 'Vendors']
        ];
    }

    /**
     * @param string $className the "focus" class name
     * @param string $expectedModuleName the expected module name
     * @dataProvider moduleNameProvider
     */
    public function testGetModuleNameReturnsMappedName($className, $expectedModuleName) {
        //TODO: Determine where the actual class definitions come from
        $entity = new $className();
        $data = VTEntityData::fromCRMEntity($entity);

        $this->assertSame($expectedModuleName, $data->getModuleName());
    }

    public function testGetModuleNameReturnsFocusClassNameForUnknownModule() {
        $data = new VTEntityData();
        $data->focus = new stdClass;

        $this->assertSame('stdClass', $data->getModuleName('CustomModule'));
    }

    public function testGet() {
        $entity = new stdClass;
        $entity->column_fields = [
   	        'field1' => 'value1'
        ];
        $data = VTEntityData::fromCRMEntity($entity);

        $this->assertSame('value1', $data->get('field1'));
    }

    public function testSet() {
        $entity = new stdClass;
        $data = VTEntityData::fromCRMEntity($entity);
        $data->set('field1', 'value1');

        $this->assertSame('value1', $data->get('field1'));
    }

    public function testIsSavedReturnsTrueIfSaved() {
        $entity = new stdClass;
        $entity->id = 1;
        $data = VTEntityData::fromCRMEntity($entity);

        $this->assertSame(true, $data->isSaved());
    }

    public function testIsSavedReturnsFalseIfNotSaved() {
        $entity = new stdClass;
        $data = VTEntityData::fromCRMEntity($entity);

        $this->assertSame(false, $data->isSaved());
    }

    public function testIsNewReturnsTrueIfNew() {
        $entity = new stdClass;
        $data = VTEntityData::fromCRMEntity($entity);

        $this->assertSame(true, $data->isNew());
    }

    public function testIsNewReturnsFalseIfNotNew() {
        $entity = new stdClass;
        $entity->id = 1;
        $data = VTEntityData::fromCRMEntity($entity);

        $this->assertSame(false, $data->isNew());
    }

    /**
     * Sets the isNew flag on the provided entity data object
     *
     * @param VTEntityData $data the data object
     * @param boolean $isNew the isNew value
     */
    private function setIsNew($data, $isNew) {
        $reflection = new ReflectionClass('VTEntityData');
        $property = $reflection->getProperty('isNew');
        $property->setAccessible(true);
        $property->setValue($data, $isNew);
    }
}
