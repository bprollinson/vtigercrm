<?php
require_once(dirname(__FILE__).'/../../../../include/database/PearDatabase.php');

class PearDatabaseTest extends PHPUnit_Framework_TestCase {
    private $database;

    public function setUp() {
        $this->database = new PearDatabase();
    }

    public function testIsMySQLReturnsTrue() {
        $this->database->dbType = 'mysql12345';

        $this->assertSame(true, $this->database->isMySql());
    }

    public function testIsMySQLReturnsFalse() {
        $this->database->dbType = '12345mysql';

        $this->assertSame(false, $this->database->isMySql());
    }

    public function testIsOracleReturnsTrue() {
        $this->database->dbType = 'oci8';

        $this->assertSame(true, $this->database->isOracle());
    }

    public function testIsOracleReturnsFalse() {
        $this->database->dbType = 'xoci8';

        $this->assertSame(false, $this->database->isOracle());
    }

    public function testIsPostgresReturnsTrue() {
        $this->database->dbType = 'pgsql';

        $this->assertSame(true, $this->database->isPostgres());
    }

    public function testIsPostgresReturnsFalse() {
        $this->database->dbType = 'xpgsql';

        $this->assertSame(false, $this->database->isPostgres());
    }

    public function testGetDataSourceName() {
        $this->database->dbType = 'dbtype';
        $this->database->userName = 'username';
        $this->database->userPassword = 'userpassword';
        $this->database->dbHostName = 'dbhostname';
        $this->database->dbName = 'dbname';

        $this->assertSame('dbtype://username:userpassword@dbhostname/dbname', $this->database->getDataSourceName());
    }

    public function testChangeKeyCaseChangesArrayKeysToLowerCase() {
        $data = [
            'lowercase' => 'lowercase',
            'MixedCase' => 'MixedCase',
            'UPPERCASE' => 'UPPERCASE',
            '12345!@#$' => '12345!@#$' 
        ];
        $expectedResult = [
            'lowercase' => 'lowercase',
            'mixedcase' => 'MixedCase',
            'uppercase' => 'UPPERCASE',
            '12345!@#$' => '12345!@#$'
        ];

        $this->assertSame($expectedResult, $this->database->change_key_case($data));
    }

    public function testChangeKeyCaseDoesNotModifyScalarValue() {
        $this->assertSame('MixedCase12345!@#$', $this->database->change_key_case('MixedCase12345!@#$'));
    }

    public function testFlattenArrayDoesNotModifyFlatArray() {
        $this->assertSame([1, 2, 3, 4, 5], $this->database->flatten_array([1, 2, 3, 4, 5]));
    }

    public function testFlattenArrayFlattensDeepArray() {
        $array = [
            1,
            2,
            3      => [3, 4],
            9      => [5, 6],
            'key1' => [7, 8],
            'key2' => [
                'key3' => [9, 10]
            ]
        ];
        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], $this->database->flatten_array($array));
    }

    public function testFlattenArrayConvertsEmptyInputToNull() {
        $this->assertSame(null, $this->database->flatten_array(0));
    }

    public function testFlattenArrayMergesInExistingValues() {
        $this->assertSame([1, 2, 3, 4, 5, 6], $this->database->flatten_array([4, 5, 6], [1, 2, 3]));
    }

    public function testGetEmptyBlobRetrunsNullValue() {
        //TODO: Fix definition to use $is_string variable?
        $this->assertSame('null', $this->database->getEmptyBlob(false));
    }

    public function testGetEmptyBlobRetrunsNullString() {
        $this->assertSame('null', $this->database->getEmptyBlob());
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage unhandled type: N;
     */
    public function testSqlQuoteThrowsExceptionOnInvalidType() {
        //TODO: fix nonsensical error message?
        $data = [
            'type'  => 'unknown',
            'value' => 'value'
        ];

        $this->database->sql_quote($data);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage missing table name
     */
    public function testSqlInsertDataThrowsExceptionForEmptyTableObject() {
        $this->database->sql_insert_data(null, ['entity']);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage data must be an array
     */
    public function testSqlInsertDataThrowsExceptionForNonArrayData() {
        $this->database->sql_insert_data('table', 'entity');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage not an array
     */
    public function testSqlExprDatalistThrowsExceptionForNonArrayData() {
        $this->database->sql_expr_datalist('datalist');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage empty arrays not allowed
     */
    public function testSqlExprDatalistThrowsExceptionForEmptyDataArray() {
        $this->database->sql_expr_datalist([]);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage not an array
     */
    public function testSqlExprDatalistFromRecordsThrowsExceptionForNonArrayData() {
        $this->database->sql_expr_datalist_from_records('datalist', 'field');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage empty arrays not allowed
     */
    public function testSqlExprDatalistFromRecordsThrowsExceptionForEmptyDataArray() {
        $this->database->sql_expr_datalist_from_records([], 'field');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage missing field
     */
    public function testSqlExprDatalistFromRecordsThrowsExceptionForEmptyField() {
        $this->database->sql_expr_datalist_from_records(['datalist'], NULL);
    }

    public function testSqlConcatForMysql() {
        $this->database->dbType = 'mysql';

        $this->assertSame('concat(value1,value2,value3)', $this->database->sql_concat(['value1', 'value2', 'value3']));
    }

    public function testSqlConcatForMysqli() {
        $this->database->dbType = 'mysqli';

        $this->assertSame('concat(value1,value2,value3)', $this->database->sql_concat(['value1', 'value2', 'value3']));
    }

    public function testSqlConcatForPgsql() {
        $this->database->dbType = 'pgsql';

        $this->assertSame('(value1||value2||value3)', $this->database->sql_concat(['value1', 'value2', 'value3']));
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage unsupported dbtype "db2"
     */
    public function testSqlConcatThrowsExceptionForUnsupportedDbType() {
        $this->database->dbType = 'db2';

        $this->database->sql_concat(['value1', 'value2', 'value3']);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage result is not an object
     */
    public function testQueryResultThrowsExceptionForNonObjectResult() {
        $result = [];
        $this->database->query_result($result, 0);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage result is not an object
     */
    public function testQueryResultRowdataThrowsExceptionForNonObjectResult() {
        $result = [];
        $this->database->query_result_rowdata($result, 0);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage result is not an object
     */
    public function testRawResultRowdataThrowsExceptionForNonObjectResult() {
        $result = [];
        $this->database->raw_query_result_rowdata($result, 0);
    }

    public function testSqlEscapeStringDoesNotFormatForOtherDatabase() {
        // TODO: Consider fixing the actual functionality
        $this->database->dbType = 'other';

        $this->assertSame(NULL, $this->database->sql_escape_string("SELECT * FROM TABLE WHERE a = 'b'"));
    }

    public function testEscapeDbNameUsesStoredDbName() {
        $this->database->dbType = 'other';
        $this->database->dbName = 'dbname';

        $this->assertSame('dbname', $this->database->escapeDbName());
    }

    public function testEscapeDbNameTransformsNameForMysql() {
        $this->database->dbType = 'mysql';

        $this->assertSame('`dbname`', $this->database->escapeDbName('dbname'));
    }

    public function testEscapeDbNameDoesNotTransformNameForOtherDatabase() {
        $this->database->dbType = 'pgsql';

        $this->assertSame('dbname', $this->database->escapeDbName('dbname'));
    }

    public function testCheckDbUtf8SupportReturnsTrueForPostgres() {
        //TODO: Fix method to use database->dbType?
        global $db_type;
        $db_type = 'pgsql';

        $this->assertSame(true, $this->database->check_db_utf8_support());
    }

    public function testGetDbCharsetReturnsUTF8ForPostgres() {
        //TODO: Fix method to use database->dbType?
        global $db_type;
        $db_type = 'pgsql';

        $this->assertSame('UTF8', $this->database->get_db_charset());
    }
}
