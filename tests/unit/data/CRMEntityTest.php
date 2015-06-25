<?php

require_once(dirname(__FILE__).'/../../../data/CRMEntity.php');

class CRMEntityTest extends PHPUnit_Framework_TestCase {
    private $entity;

    public function setUp() {
        $this->entity = new CRMEntity();
    }

    public function testIsBulkSaveModeReturnsTrueWhenGlobalSet() {
        global $VTIGER_BULK_SAVE_MODE;
        $VTIGER_BULK_SAVE_MODE = true;

        $this->assertSame(true, CRMEntity::isBulkSaveMode());
    }

    public function testIsBulkSaveModeReturnsFalseWhenGlobalNotSet() {
        $this->assertSame(false, CRMEntity::isBulkSaveMode());
    }

    public function testIsBulkSaveModeReturnsFalseWhenGlobalIsFalse() {
        global $VTIGER_BULK_SAVE_MODE;
        $VTIGER_BULK_SAVE_MODE = false;

        $this->assertSame(false, CRMEntity::isBulkSaveMode());
    }

    public function testTransformExportValueReturnsSameValue() {
        $this->assertSame('value', $this->entity->transform_export_value('key', 'value'));
    }

    public function testTimeDiffReturnsPositiveNumberForSequentialDates() {
        $d1 = '2015-01-01 00:00:00';
        $d2 = '2015-02-01 00:00:00';

        $this->assertSame(31*24*60*60, $this->entity->__timediff($d1, $d2));
    }

    public function testTimeDiffReturnsNegativeNumberForReverseDates() {
        $d1 = '2015-02-01 00:00:00';
        $d2 = '2015-01-01 00:00:00';

        $this->assertSame(-31*24*60*60, $this->entity->__timediff($d1, $d2));
    }

    public function testTimeDiffReturnsZeroForReverseDates() {
        $d1 = '2015-01-01 00:00:00';
        $d2 = '2015-01-01 00:00:00';

        $this->assertSame(0, $this->entity->__timediff($d1, $d2));
    }

    public function testGetJoinClauseReturnsInnerJoin() {
        $this->assertSame('INNER JOIN', $this->entity->getJoinClause('test'));
    }

    public function testGetJoinClauseReturnsLeftJoinForRelTable() {
        $this->assertSame('LEFT JOIN', $this->entity->getJoinClause('testrel'));
    }

    public function testGetNonAdminUserAccessQueryReturnsSQLForNoGroups() {
        $user = new stdClass;
        $user->id = 1;
        $query = $this->entity->getNonAdminUserAccessQuery($user, 2, []);
        $expectedQuery = "(SELECT 1 as id) UNION (SELECT vtiger_user2role.userid AS userid FROM vtiger_user2role INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid WHERE vtiger_role.parentrole like '2::%')";

        $this->assertSame($expectedQuery, $query);
    }

    public function testGetNonAdminUserAccessQueryReturnsSQLForGroups() {
        $user = new stdClass;
        $user->id = 1;
        $query = $this->entity->getNonAdminUserAccessQuery($user, 2, [3, 4]);
        $expectedQuery = "(SELECT 1 as id) UNION (SELECT vtiger_user2role.userid AS userid FROM vtiger_user2role INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid WHERE vtiger_role.parentrole like '2::%') UNION (SELECT groupid FROM vtiger_groups where groupid in (3,4))";

        $this->assertSame($expectedQuery, $query);
    }

    public function testListQueryNonAdminChangeDoesNotChangeQueryWithoutConsecutiveWhitespaceTokens() {
        $this->assertEquals('token1 token2', $this->entity->listQueryNonAdminChange('token1 token2'));
    }

    public function testListQueryCombinesConsecutiveSpaces() {
        $this->assertEquals('token1 token2', $this->entity->listQueryNonAdminChange("token1  \t\ntoken2"));
    }

    public function testSetRelationTablesReturnsTables() {
        $this->entity->table_name = 'table_name';
        $this->entity->table_index = 'table_index';

        $expectedTables = [
            'vtiger_senotesrel' => ['crmid', 'notesid'],
   	        'table_name' => 'table_index'
        ];

        $this->assertEquals($expectedTables, $this->entity->setRelationTables('Documents'));
    }
}
