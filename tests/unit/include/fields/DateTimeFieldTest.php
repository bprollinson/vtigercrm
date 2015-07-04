<?php

require_once(dirname(__FILE__).'/../../../../include/fields/DateTimeField.php');

class MockLog {
    public function debug($message) {
    }
}

class DateTimeFieldTest extends PHPUnit_Framework_TestCase {
    public function testConstructorUsesProvidedDate() {
        $field = new DateTimeField('2015-01-01 10:15:00');

        $reflection = new ReflectionClass('DateTimeField');
        $dateTimeProperty = $reflection->getProperty('datetime');
        $dateTimeProperty->setAccessible(true);
        $this->assertSame('2015-01-01 10:15:00', $dateTimeProperty->getValue($field));
    }

    public function testConstructorUsesCurrentDate() {
        $minTime = time();
        $field = new DateTimeField(NULL);
        $maxTime = time();

        $reflection = new ReflectionClass('DateTimeField');
        $dateTimeProperty = $reflection->getProperty('datetime');
        $dateTimeProperty->setAccessible(true);
        $datetimeValue = $dateTimeProperty->getValue($field);
        $datetimeObject = DateTime::createFromFormat('Y-m-d H:i:s', $datetimeValue);
        $datetimeTimestamp = $datetimeObject->getTimestamp();

        $this->assertGreaterThanOrEqual($minTime, $datetimeTimestamp);
        $this->assertLessThanOrEqual($maxTime, $datetimeTimestamp);
    }

    public function testGetDBInsertDateValueCalculatesDate() {
        global $log;
        $log = new MockLog();

        $field = new DateTimeField('2015-01-31  ');
        $this->assertSame('31-01-2015', $field->getDBInsertDateValue());
    }

    public function testGetDBInsertDateIsNotAffectedByUserFormat() {
        global $log;
        global $current_user;
        $log = new MockLog();

        $current_user = new stdClass;
        $current_user->date_format = 'mm-dd-yyyy';

        $field = new DateTimeField('2015-01-31 ');
        $this->assertSame('2015-01-31', $field->getDBInsertDateValue());
    }

    public function testGetDBInsertDateValueConvertsToDBTimeZone() {
        global $log;
        global $current_user;
        $log = new MockLog();

        $this->setDatabaseTimeZone('Europe/Berlin');

        $current_user = new stdClass;
        $current_user->date_format = 'mm-dd-yyyy';
        $current_user->time_zone = 'America/New_York';

        $field = new DateTimeField('2015-01-31 23:00:00');
        $this->assertSame('2015-02-01', $field->getDBInsertDateValue());
    }

    public function testDBInsertDateTimeValue() {
        global $log;
        global $current_user;
        $log = new MockLog();

        $this->setDatabaseTimeZone('Europe/Berlin');

        $current_user = new stdClass;
        $current_user->date_format = 'mm-dd-yyyy';
        $current_user->time_zone = 'America/New_York';

        $field = new DateTimeField('2015-01-31 22:30:00');
        $this->assertSame('2015-02-01 04:30:00', $field->getDBInsertDateTimeValue());
    }

    public function testGetDisplayDateTimeValue() {
        global $log;
        global $current_user;
        $log = new MockLog();

        $this->setDatabaseTimeZone('Europe/Berlin');

        $current_user = new stdClass;
        $current_user->date_format = 'mm-dd-yyyy';
        $current_user->time_zone = 'America/New_York';

        $field = new DateTimeField('2015-01-31 10:30:00');
        $this->assertSame('01-31-2015 04:30:00', $field->getDisplayDateTimeValue());
    }

    public function testConvertToDBFormatConvertsDate() {
        global $current_user;
        $current_user = new stdClass;
        $current_user->date_format = 'mm-dd-yyyy';

        $this->assertSame('2015-01-31', DateTimeField::convertToDBFormat('01-31-2015', $user));
    }

    public function testConvertToDBFormatUsesDefaultFormat() {
        global $current_user;
        $current_user = new stdClass;
        $current_user->date_format = '';

        $this->assertSame('2015-01-31', DateTimeField::convertToDBFormat('31-01-2015', $user));
    }

    public function test__ConvertToDBFormatUsesDefaultFormat() {
        $this->assertSame('2015-01-31', DateTimeField::__convertToDBFormat('31-01-2015', ''));
    }

    public function test__ConvertToDBFormatUsesDDMMYYYYFormat() {
        $this->assertSame('2015-01-31', DateTimeField::__convertToDBFormat('31-01-2015', 'dd-mm-yyyy'));
    }

    public function test__ConvertToDBFormatUsesMMDDYYYYFormat() {
        $this->assertSame('2015-01-31', DateTimeField::__convertToDBFormat('01-31-2015', 'mm-dd-yyyy'));
    }

    public function test__ConvertToDBFormatUsesYYYYMMDDFormat() {
        $this->assertSame('2015-01-31', DateTimeField::__convertToDBFormat('2015-01-31', 'yyyy-mm-dd'));
    }

    public function test__ConvertToDBFormatReturnsEmptyStringForUnknownFormat() {
        $this->assertSame('', DateTimeField::__convertToDBFormat('2015-01-31', 'unknown'));
    }

    public function testConvertToInternalFormatConvertsArray() {
        $this->assertSame(['2015', '01', '31'], DateTimeField::convertToInternalFormat('2015 01 31'));
    }

    public function testConvertToInternalFormatDoesNotModifyArray() {
        $this->assertSame(['2015', '01', '31'], DateTimeField::convertToInternalFormat(['2015', '01', '31']));
    }

    public function testConvertToUserFormatConvertsDate() {
        $user = new stdClass;
        $user->date_format = 'mm-dd-yyyy';

        $this->assertSame('01-31-2015', DateTimeField::convertToUserFormat('2015-01-31', $user));
    }

    public function testConvertToUserFormatUsesCurrentUser() {
        global $current_user;
        $current_user = new stdClass;
        $current_user->date_format = 'mm-dd-yyyy';

        $this->assertSame('01-31-2015', DateTimeField::convertToUserFormat('2015-01-31'));
    }

    public function testConvertToUserFormatUsesDefaultFormat() {
        $user = new stdClass;
        $user->date_format = NULL;

        $this->assertSame('31-01-2015', DateTimeField::convertToUserFormat('2015-01-31', $user));
    }

    public function test__ConvertToUserFormatUsesDDMMYYYYFormat() {
        $this->assertSame('31-01-2015 12:30:00', DateTimeField::__convertToUserFormat('2015-01-31 12:30:00', 'dd-mm-yyyy'));
    }

    public function test__ConvertToUserFormatUsesMMDDYYYYFormat() {
        $this->assertSame('01-31-2015 12:30:00', DateTimeField::__convertToUserFormat('2015-01-31 12:30:00', 'mm-dd-yyyy'));
    }

    public function test__ConvertToUserFormatUsesYYYYMMDDFormat() {
        $this->assertSame('2015-01-31 12:30:00', DateTimeField::__convertToUserFormat('2015-01-31 12:30:00', 'yyyy-mm-dd'));
    }

    public function test__ConvertToUserFormatDoesNotModifyDateForUnknownFormat() {
        $this->assertSame('2015-01-31 12:30:00', DateTimeField::__convertToUserFormat('2015-01-31 12:30:00', 'unknown'));
    }

    public function test__ConvertToUserFormatHandlesDateWithoutTime() {
        $this->assertSame('01-31-2015', DateTimeField::__convertToUserFormat('2015-01-31', 'mm-dd-yyyy'));
    }

    public function test__ConvertToUserFormatHandlesArray() {
        $this->assertSame('01-31-2015 12:30:00', DateTimeField::__convertToUserFormat(['2015-01-31', '12:30:00'], 'mm-dd-yyyy'));
    }

    public function testConvertToUserTimeZoneConvertsDate() {
        $this->setDatabaseTimeZone('Europe/Berlin');

        $user = new stdClass;
        $user->time_zone = 'America/New_York';

        $date = DateTimeField::convertToUserTimeZone('2015-01-31 10:30:00', $user);
        $this->assertSame('2015-01-31 04:30:00', $date->format('Y-m-d H:i:s'));
    }
    
    public function testConvertToUserTimeZoneUsesCurrentUser() {
        global $current_user;

        $this->setDatabaseTimeZone('Europe/Berlin');

        $current_user = new stdClass;
        $current_user->time_zone = 'America/New_York';

        $date = DateTimeField::convertToUserTimeZone('2015-01-31 10:30:00');
        $this->assertSame('2015-01-31 04:30:00', $date->format('Y-m-d H:i:s'));
    }

    public function testConvertToUserTimeZoneUsesDefaultTimezone() {
        global $default_timezone;

        $this->setDatabaseTimeZone('Europe/Berlin');

        $user = new stdClass;
        $user->time_zone = NULL;
        $default_timezone = 'America/New_York';

        $date = DateTimeField::convertToUserTimeZone('2015-01-31 10:30:00', $user);
        $this->assertSame('2015-01-31 04:30:00', $date->format('Y-m-d H:i:s'));
    }

    public function testConvertToDBTimeZoneConvertsDate() {
        $this->setDatabaseTimeZone('Europe/Berlin');

        $user = new stdClass;
        $user->time_zone = 'America/New_York';

        $date = DateTimeField::convertToDBTimeZone('2015-01-31 04:30:00', $user);
        $this->assertSame('2015-01-31 10:30:00', $date->format('Y-m-d H:i:s'));
    }
    
    public function testConvertToDBTimeZoneUsesCurrentUser() {
        global $current_user;

        $this->setDatabaseTimeZone('Europe/Berlin');

        $current_user = new stdClass;
        $current_user->time_zone = 'America/New_York';

        $date = DateTimeField::convertToDBTimeZone('2015-01-31 04:30:00');
        $this->assertSame('2015-01-31 10:30:00', $date->format('Y-m-d H:i:s'));
    }
    
    public function testConvertToDBTimeZoneUsesDefaultTimezone() {
        global $default_timezone;

        $this->setDatabaseTimeZone('Europe/Berlin');

        $user = new stdClass;
        $user->time_zone = NULL;
        $default_timezone = 'America/New_York';

        $date = DateTimeField::convertToDBTimeZone('2015-01-31 04:30:00', $user);
        $this->assertSame('2015-01-31 10:30:00', $date->format('Y-m-d H:i:s'));
    }

    public function testConvertTimeZoneConvertsTimeZone() {
        $date = DateTimeField::convertTimeZone('2015-01-31 10:30:00', 'Europe/Berlin', 'America/New_York');
        $this->assertSame('2015-01-31 04:30:00', $date->format('Y-m-d H:i:s'));
    }

    public function testConvertTimeZoneHandlesEndOfDay() {
        $date = DateTimeField::convertTimeZone('24:00', 'America/New_York', 'Europe/Berlin');
        $timePart = $date->format('H:i:s');

        $this->assertSame('06:00:00', $timePart);
    }

    public function testGetDBInsertTimeValue() {
        global $log;
        $log = new MockLog();

        $this->setDatabaseTimeZone('Europe/Berlin');
        
        $user = new stdClass;
        $user->time_zone = 'America/New_York';

        $field = new DateTimeField('2015-01-31 04:30:00');
        $this->assertSame('10:30:00', $field->getDBInsertTimeValue($user));
    }

    public function testGetDisplayDateConvertsDate() {
        global $log;
        $log = new MockLog();

        $user = new stdClass;
        $user->date_format = 'mm-dd-yyyy';

        $field = new DateTimeField('2015-01-31');
        $this->assertSame('01-31-2015', $field->getDisplayDate($user));
    }

    public function testGetDisplayDateConvertsTimeZone() {
        global $log;
        $log = new MockLog();
        
        $this->setDatabaseTimeZone('Europe/Berlin');

        $user = new stdClass;
        $user->date_format = 'mm-dd-yyyy';
        $user->time_zone = 'America/New_York';

        $field = new DateTimeField('2015-02-01 04:30:00');
        $this->assertSame('01-31-2015', $field->getDisplayDate($user));
    }

    public function testGetDisplayTime() {
        global $log;
        $log = new MockLog();

        $this->setDatabaseTimeZone('Europe/Berlin');
        
        $user = new stdClass;
        $user->time_zone = 'America/New_York';

        $field = new DateTimeField('2015-01-01 10:30:00');
        $this->assertSame('04:30:00', $field->getDisplayTime($user));
    }

    public function testGetDBTimeZoneReturnsCachedValue() {
        $this->setDatabaseTimeZone('Europe/Berlin');

        $this->assertSame('Europe/Berlin', DateTimeField::getDBTimeZone());
    }

    public function testGetPHPDateFormatFormatsDate() {
        $user = new stdClass;
        $user->date_format = 'mm-dd-yyyy';

        $this->assertSame('m-d-Y', DateTimeField::getPHPDateFormat($user));
    }

    public function testGetPHPDateFormatUsesUserGlobal() {
        global $current_user;
        $current_user = new stdClass;
        $current_user->date_format = 'mm-dd-yyyy';

        $this->assertSame('m-d-Y', DateTimeField::getPHPDateFormat());
    }

    private function setDatabaseTimeZone($timezone) {
        $reflection = new ReflectionProperty('DateTimeField', 'databaseTimeZone');
        $reflection->setAccessible(true);
        $reflection->setValue(NULL, $timezone);
    }
}
