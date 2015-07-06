<?php

require_once(dirname(__FILE__).'/../../../../include/ListView/ListViewController.php');

class ListViewControllerTest extends PHPUnit_Framework_TestCase {
    public function testConstruct() {
        $db = $this->getDefaultDb();
        $user = $this->getDefaultUser();
        $generator = $this->getDefaultGenerator();

        $controller = new ListViewController($db, $user, $generator);

        $this->assertSame($db, $this->getControllerField($controller, 'db'));
        $this->assertSame($user, $this->getControllerField($controller, 'user'));
        $this->assertSame($generator, $this->getControllerField($controller, 'queryGenerator'));
        $this->assertSame([], $this->getControllerField($controller, 'nameList'));
        $this->assertSame([], $this->getControllerField($controller, 'typeList'));
        $this->assertSame([], $this->getControllerField($controller, 'ownerNameList'));
        $this->assertSame([], $this->getControllerField($controller, 'picklistValueMap'));
        $this->assertSame([], $this->getControllerField($controller, 'picklistRoleMap'));
        $this->assertSame(true, $this->getControllerField($controller, 'headerSortingEnabled'));
    }

    public function testIsHeaderSortingEnabledReturnsTrue() {
        $db = $this->getDefaultDb();
        $user = $this->getDefaultUser();
        $generator = $this->getDefaultGenerator();

        $controller = new ListViewController($db, $user, $generator);
        $this->setControllerField($controller, 'headerSortingEnabled', true);

        $this->assertSame(true, $controller->isHeaderSortingEnabled());
    }

    public function testIsHeaderSortingEnabledReturnsFalse() {
        $db = $this->getDefaultDb();
        $user = $this->getDefaultUser();
        $generator = $this->getDefaultGenerator();

        $controller = new ListViewController($db, $user, $generator);
        $this->setControllerField($controller, 'headerSortingEnabled', false);

        $this->assertSame(false, $controller->isHeaderSortingEnabled());
    }

    public function testSetHeaderSorting() {
        $controller = new ListViewController($db, $user, $generator);

        $controller->setHeaderSorting(true);
        $this->assertSame(true, $controller->isHeaderSortingEnabled());
        $controller->setHeaderSorting(false);
        $this->assertSame(false, $controller->isHeaderSortingEnabled());
    }

    private function getDefaultDb() {
        $db = new stdClass;
        $db->name = 'mydatabase';

        return $db;
    }

    private function getDefaultUser() {
        $user = new stdClass;
        $user->name = 'myuser';

        return $user;
    }

    private function getDefaultGenerator() {
        $generator = new stdClass;
        $generator->name = 'mygenerator';

        return $generator;
    }

    private function getControllerField($controller, $fieldName) {
        $property = new ReflectionProperty('ListViewController', $fieldName);
        $property->setAccessible(true);
        return $property->getValue($controller);
    }

    private function setControllerField($controller, $fieldName, $fieldValue) {
        $property = new ReflectionProperty('ListViewController', $fieldName);
        $property->setAccessible(true);
        $property->setValue($controller, $fieldValue);
    }
}
