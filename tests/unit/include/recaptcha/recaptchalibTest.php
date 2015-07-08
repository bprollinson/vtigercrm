<?php

require_once(dirname(__FILE__).'/../../../../include/recaptcha/recaptchalib.php');

class RecaptchalibTest extends PHPUnit_Framework_TestCase {
    public function testRecaptchaQsencode() {
        $data = [
            'key1' => '1 + 2',
            'key2' => "\'string\'"
        ];
        $this->assertSame('key1=1+%2B+2&key2=%27string%27', _recaptcha_qsencode($data));
    }

    public function testRecaptchaGetHtmlWithoutSSL() {
        $expectedData = "<script type=\"text/javascript\" src=\"http://www.google.com/recaptcha/api/challenge?k=pubkey\"></script>

\t<noscript>
  		<iframe src=\"http://www.google.com/recaptcha/api/noscript?k=pubkey\" height=\"300\" width=\"500\" frameborder=\"0\"></iframe><br/>
  		<textarea name=\"recaptcha_challenge_field\" rows=\"3\" cols=\"40\"></textarea>
  		<input type=\"hidden\" name=\"recaptcha_response_field\" value=\"manual_challenge\"/>
	</noscript>";
        $this->assertSame($expectedData, recaptcha_get_html('pubkey'));
    }

    public function testRecaptchaGetHtmlWithSSL() {
        $expectedData = "<script type=\"text/javascript\" src=\"https://www.google.com/recaptcha/api/challenge?k=pubkey\"></script>

\t<noscript>
  		<iframe src=\"https://www.google.com/recaptcha/api/noscript?k=pubkey\" height=\"300\" width=\"500\" frameborder=\"0\"></iframe><br/>
  		<textarea name=\"recaptcha_challenge_field\" rows=\"3\" cols=\"40\"></textarea>
  		<input type=\"hidden\" name=\"recaptcha_response_field\" value=\"manual_challenge\"/>
	</noscript>";
        $this->assertSame($expectedData, recaptcha_get_html('pubkey', NULL, true));
    }

    public function testRecaptchaGetHtmlAppendsError() {
        $expectedData = "<script type=\"text/javascript\" src=\"https://www.google.com/recaptcha/api/challenge?k=pubkey&amp;error=errorstring\"></script>

\t<noscript>
  		<iframe src=\"https://www.google.com/recaptcha/api/noscript?k=pubkey&amp;error=errorstring\" height=\"300\" width=\"500\" frameborder=\"0\"></iframe><br/>
  		<textarea name=\"recaptcha_challenge_field\" rows=\"3\" cols=\"40\"></textarea>
  		<input type=\"hidden\" name=\"recaptcha_response_field\" value=\"manual_challenge\"/>
	</noscript>";
        $this->assertSame($expectedData, recaptcha_get_html('pubkey', 'errorstring', true));
    }

    public function testRecaptchaGetSignupUrl() {
        $this->assertSame('https://www.google.com/recaptcha/admin/create?domains=1+%2B+2&app=%27string%27', recaptcha_get_signup_url('1 + 2', "\'string\'"));
    }

    public function testRecaptchaAesPad() {
        $char = chr(15);
        $this->assertSame('12345678901234567'.str_repeat($char, 15), _recaptcha_aes_pad('12345678901234567'));
    }

    public function testRecaptchaMailhideUrlbase64() {
        // TODO: also test +/ characters
        $this->assertSame(base64_encode('mystring12345'), _recaptcha_mailhide_urlbase64('mystring12345'));
    }

    public function testRecaptchaMailhideEmailPartsForLengthAtMost4() {
        $expectedData = [
            'j',
            'domain.com'
        ];
        $this->assertSame($expectedData, _recaptcha_mailhide_email_parts('john@domain.com'));
    }

    public function testRecaptchaMailhideEmailPartsForLengthAtMost6() {
        $expectedData = [
            'joh',
            'domain.com'
        ];
        $this->assertSame($expectedData, _recaptcha_mailhide_email_parts('johndo@domain.com'));
    }

    public function testRecaptchaMailhideEmailPartsForLengthGreaterThan6() {
        $expectedData = [
            'john',
            'domain.com'
        ];
        $this->assertSame($expectedData, _recaptcha_mailhide_email_parts('johnjdo@domain.com'));
    }
}
