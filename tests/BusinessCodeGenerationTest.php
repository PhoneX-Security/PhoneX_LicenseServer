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
        $this->assertTrue(BusinessCode::parityCheck($code));

        // now change first character to a bad one
        $badCode = $code;
        $lastPos = strlen($badCode) - 1;

        $position = array_search($badCode[$lastPos], str_split(BusinessCode::$chars));
        $position = ($position + 1) % strlen(BusinessCode::$chars);
        $badCode[$lastPos] = BusinessCode::$chars[$position];
        $this->assertFalse(BusinessCode::parityCheck($badCode));

        // prefix code
        $codeMp = BusinessCode::getCode('mp');
        $this->assertTrue(BusinessCode::parityCheck($codeMp));
	}

}
