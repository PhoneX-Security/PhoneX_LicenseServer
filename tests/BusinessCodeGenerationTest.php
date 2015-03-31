<?php

use Phonex\BusinessCode;

class BusinessCodeGenerationTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testParity()
	{
		$code = BusinessCode::getCode();
        $this->assertTrue(BusinessCode::verifyCode($code));
        echo "$code \n";

        // now change first character to a bad one
        $badCode = $code;
        $lastPos = strlen($badCode) - 1;

        $position = array_search($badCode[$lastPos], str_split(BusinessCode::$chars));
        $position = ($position + 1) % strlen(BusinessCode::$chars);
        $badCode[$lastPos] = BusinessCode::$chars[$position];
        echo "$badCode \n";
        $this->assertFalse(BusinessCode::verifyCode($badCode));

        // prefix code
        $codeMp = BusinessCode::getCode('mp');
        $this->assertTrue(BusinessCode::verifyCode($codeMp));
        echo "$codeMp \n";
	}

}
