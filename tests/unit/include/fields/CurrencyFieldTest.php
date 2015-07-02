<?php

require_once(dirname(__FILE__).'/../../../../include/events/SqlResultIterator.inc');
require_once(dirname(__FILE__).'/../../../../vtlib/Vtiger/Functions.php');

class CurrencyFieldTest extends PHPUnit_Framework_TestCase {
    public function testConstruct() {
        $field = new CurrencyField(100);

        $this->assertSame(100, $field->value);
    }

    public function testInitializeInitializesDataFromProvidedUser() {
        global $current_user;

        $this->initializeStaticData('X', '2.00000');

        $current_user = new stdClass;
        $current_user->no_of_currency_decimals = 10;

        $user = new stdClass;
        $user->currency_grouping_pattern = '12345,67890';
        $user->currency_grouping_separator = '!';
        $user->currency_decimal_separator = '?';
        $user->currency_id = 1;
        $user->currency_symbol_placement = 9;
        $field = new CurrencyField(100);
        $field->initialize($user);

        $this->assertSame('12345,67890', $field->currencyFormat);
        $this->assertSame('!', $field->currencySeparator);
        $this->assertSame('?', $field->decimalSeparator);
        $this->assertSame(1, $field->currencyId);
        $this->assertSame('X', $field->currencySymbol);
        $this->assertSame('2.00000', $field->conversionRate);
        $this->assertSame(9, $field->currencySymbolPlacement);
        $this->assertSame(10, $field->numberOfDecimal);
    }

    public function testInitializeInitializesDataFromGlobalUser() {
        global $current_user;

        $this->initializeStaticData('X', '2.00000');

        $current_user = new stdClass;
        $current_user->no_of_currency_decimals = 10;
        $current_user->currency_grouping_pattern = '12345,67890';
        $current_user->currency_grouping_separator = '!';
        $current_user->currency_decimal_separator = '?';
        $current_user->currency_id = 1;
        $current_user->currency_symbol_placement = 9;
        $field = new CurrencyField(100);
        $field->initialize();

        $this->assertSame('12345,67890', $field->currencyFormat);
        $this->assertSame('!', $field->currencySeparator);
        $this->assertSame('?', $field->decimalSeparator);
        $this->assertSame(1, $field->currencyId);
        $this->assertSame('X', $field->currencySymbol);
        $this->assertSame('2.00000', $field->conversionRate);
        $this->assertSame(9, $field->currencySymbolPlacement);
        $this->assertSame(10, $field->numberOfDecimal);
    }

    public function testInitializeDoesNotInitializesFieldsFromUserWhenNotSet() {
        global $current_user;

        $this->initializeStaticData('X', '2.00000');

        $current_user = new stdClass;
        $current_user->no_of_currency_decimals = 10;

        $user = new stdClass;
        $user->currency_id = 1;
        $user->currency_symbol_placement = 9;
        $field = new CurrencyField(100);
        $field->initialize($user);

        $this->assertSame('123,456,789', $field->currencyFormat);
        $this->assertSame(',', $field->currencySeparator);
        $this->assertSame('.', $field->decimalSeparator);
        $this->assertSame(1, $field->currencyId);
        $this->assertSame('X', $field->currencySymbol);
        $this->assertSame('2.00000', $field->conversionRate);
        $this->assertSame(9, $field->currencySymbolPlacement);
        $this->assertSame(10, $field->numberOfDecimal);
    }

    public function testGetCurrencySymbol() {
        $field = new CurrencyField(100);
        $field->currencySymbol = '!';

        $this->assertSame('!', $field->getCurrencySymbol());
    }

    public function testConvertToUserFormatForPositiveValue() {
        $user = new stdClass;
        $user->currency_id = 1;

        $this->assertSame('123,456.78', CurrencyField::convertToUserFormat(123456.78, $user, true, false));
    }

    public function testConvertToUserFormatForNegativeValue() {
        $user = new stdClass;
        $user->currency_id = 1;

        $this->assertSame('-123,456.78', CurrencyField::convertToUserFormat(-123456.78, $user, true, false));
    }

    public function testGetDisplayValueReturnsUnmodifiedValue() {
        $user = new stdClass;
        $user->currency_id = 1;

        $field = new CurrencyField(123456.78);
        $this->assertSame(123456.78, $field->getDisplayValue($user, true, true));
    }

    public function testGetDisplayValuePerformsCurrencyConversion() {
        $user = new stdClass;
        $user->currency_id = 1;

        $field = new CurrencyField(111111.11);
        $field->conversionRate = 2.0;
        $this->assertSame(222222.22, $field->getDisplayValue($user, false, true));
    }

    public function testGetDisplayValueFormatsCurrencyValue() {
        $user = new stdClass;
        $user->currency_id = 1;

        $field = new CurrencyField(123456.78);
        $this->assertSame('123,456.78', $field->getDisplayValue($user, true, false));
    }

    public function testGetDisplayValueWithSymbolAppendsCurrencySymbol() {
        $this->initializeStaticData('!', '1.00000');

        $user = new stdClass;
        $user->currency_id = 1;
        $user->currency_symbol_placement = '1.0$';

        $field = new CurrencyField(123456.78);

        $this->assertSame('123,456.78!', $field->getDisplayValueWithSymbol($user, true));
    }

    public function testAppendCurrencySymbolAppendsDollarSignAtEnd() {
        $display = CurrencyField::appendCurrencySymbol(123.45, '$', '1.0$');

        $this->assertSame('123.45$', $display);
    }

    public function testAppendCurrencySymbolAppendsDollarSignAtStart() {
        $display = CurrencyField::appendCurrencySymbol(123.45, '$', '$1.0');

        $this->assertSame('$123.45', $display);
    }

    public function testAppendCurrencySymbolAppendsDollarSignAtStartForUnknownFormat() {
        $display = CurrencyField::appendCurrencySymbol(123.45, '$', 'unknown');

        $this->assertSame('$123.45', $display);
    }

    public function testAppendCurrencySymbolReadsPlacementFromUserGlobal() {
        global $current_user;
        $current_user = new stdClass;
        $current_user->currency_symbol_placement = '1.0$';
        $display = CurrencyField::appendCurrencySymbol(123.45, '$');

        $this->assertSame('123.45$', $display);
    }

    public function testGetDBInsertedValueCalculatesValue() {
        $this->initializeStaticData('$', '1.00000');

        $field = new CurrencyField('123!456?78');
        $field->currencySeparator = '!';
        $field->decimalSeparator = '?';

        $user = new stdClass;
        $user->currency_id = 1;
        $this->assertSame('123456.78', $field->getDBInsertedValue($user, true));
    }

    public function testGetDBInsertedValueUsesCurrentUser() {
        global $current_user;
        $this->initializeStaticData('$', '1.00000');

        $field = new CurrencyField('123!456?78');
        $field->currencySeparator = '!';
        $field->decimalSeparator = '?';

        $current_user = new stdClass;
        $current_user->currency_id = 1;
        $this->assertSame('123456.78', $field->getDBInsertedValue(NULL, true));
    }

    public function testGetDBInsertedValueUsesDefaultCurrencyAndDecimalSeparators() {
        $this->initializeStaticData('$', '1.00000');

        $field = new CurrencyField('123,456.78');

        $user = new stdClass;
        $user->currency_id = 1;
        $this->assertSame('123456.78', $field->getDBInsertedValue($user, true));
    }

    public function testGetDBInsertedValueConvertsToDollar() {
        $this->initializeStaticData('$', '2.00000');

        $field = new CurrencyField('222,222.22');

        $user = new stdClass;
        $user->currency_id = 1;
        $this->assertSame(111111.11, $field->getDBInsertedValue($user, false));
    }

    public function testConvertToDBFormat() {
        $this->initializeStaticData('$', '1.00000');

        $user = new stdClass;
        $user->currency_id = 1;
        $this->assertSame('123456.78', CurrencyField::convertToDBFormat('123,456.78', $user, true));
    }

    public function testConvertToDollarHandlesZeroConversionRate() {
        $this->assertSame(0, CurrencyField::convertToDollar(100, 0));
    }

    public function testConvertToDollarHandlesConversionRateLessThanOne() {
        $this->assertSame(200.0, CurrencyField::convertToDollar(100, 0.5));
    }

    public function testConvertToDollarHandlesConversionRateGreaterThanOne() {
        $this->assertSame(50.0, CurrencyField::convertToDollar(100, 2.0));
    }

    public function testConvertFromDollarHandlesZeroConversionRate() {
        $this->assertSame(0.0, CurrencyField::convertFromDollar(100, 0));
    }
    
    public function testConvertFromDollarHandlesConversionRateLessThanOne() {
        $this->assertSame(50.0, CurrencyField::convertFromDollar(100, 0.5));
    }
    
    public function testConvertFromDollarHandlesNegativeConversionGreaterThanOne() {
        $this->assertSame(200.0, CurrencyField::convertFromDollar(100, 2.0));
    }
    
    public function testConvertFromDollarRoundsResult() {
        $this->assertSame(12.34568, CurrencyField::convertFromDollar(100, 0.123456789));
    }

    public function testConvertFromMasterCurrencyHandlesZeroConversionRate() {
        $this->assertSame(0, CurrencyField::convertFromMasterCurrency(1, 0));
    }

    public function testConvertFromMasterCurrencyHandlesConversionRateLessThanOne() {
        $this->assertSame(0.5, CurrencyField::convertFromMasterCurrency(1, 0.5));
    }
    
    public function testConvertFromMasterCurrencyHandlesConversionRateGreaterThanOne() {
        $this->assertSame(2.0, CurrencyField::convertFromMasterCurrency(1, 2.0));
    }

    public function testCurrencyDecimalFormatDoesNotModifyValueIfNotTruncatingTrailingZeros() {
        $field = new CurrencyField(0);
        $user = new stdClass;
        $user->truncate_trailing_zeros = false;

        $this->assertSame('123,456.7890', $field->currencyDecimalFormat('123,456.7890', $user));
    }

    public function testCurrencyDecimalFormatHandlesWholeNumberValue() {
        $field = new CurrencyField(0);
        $user = $this->getUserForTruncation();

        $this->assertSame('123,456', $field->currencyDecimalFormat('123,456', $user));
    }

    public function testCurrencyDecimalFormatHandlesValueWithOneDecimal() {
        $field = new CurrencyField(0);
        $user = $this->getUserForTruncation();

        $this->assertSame('123,456.7', $field->currencyDecimalFormat('123,456.7', $user));
    }

    public function testCurrencyDecimalFormatHandlesValueWithSingleTrailingZeroDecimal() {
        $field = new CurrencyField(0);
        $user = $this->getUserForTruncation();

        $this->assertSame('123,456', $field->currencyDecimalFormat('123,456.0', $user));
    }

    public function testCurrencyDecimalFormatHandlesValueWithTwoDecimals() {
        $field = new CurrencyField(0);
        $user = $this->getUserForTruncation();

        $this->assertSame('123,456.78', $field->currencyDecimalFormat('123,456.78', $user));
    }

    public function testCurrencyDecimalFormatHandlesValueWithOneDecimalAndTrailingZero() {
        $field = new CurrencyField(0);
        $user = $this->getUserForTruncation();

        $this->assertSame('123,456.7', $field->currencyDecimalFormat('123,456.70', $user));
    }

    public function testCurrencyDecimalFormatUsesUserGlobal() {
        global $current_user;
        $current_user = $this->getUserForTruncation();

        $field = new CurrencyField(0);
        $this->assertSame('123,456.7', $field->currencyDecimalFormat('123,456.70'));
    }

    private function initializeStaticData($symbol, $rate) {
        $reflection = new ReflectionClass('Vtiger_Functions');
        $property = $reflection->getProperty('currencyInfoCache');
        $property->setAccessible(true);
        $values = [
            'currency_symbol' => $symbol,
            'conversion_rate' => $rate
        ];
        $property->setValue([1 => $values]);
    }

    private function getUserForTruncation() {
        $user = new stdClass;
        $user->truncate_trailing_zeros = true;
        $user->currency_decimal_separator = '.';

        return $user;
    }
}
