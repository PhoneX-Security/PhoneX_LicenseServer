<?php

use Phonex\BusinessCode;
use Phonex\Utils\BusinessCodeUtils;

class CodeGenerationTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testParity()
	{
		$code = BusinessCodeUtils::getCode();
        $this->assertTrue(BusinessCodeUtils::parityCheck($code));

        // now change first character to a bad one
        $badCode = $code;
        $lastPos = strlen($badCode) - 1;

        $position = array_search($badCode[$lastPos], str_split(BusinessCodeUtils::$chars));
        $position = ($position + 1) % strlen(BusinessCodeUtils::$chars);
        $badCode[$lastPos] = BusinessCodeUtils::$chars[$position];
        $this->assertFalse(BusinessCodeUtils::parityCheck($badCode));

        // prefix code
        $codeMp = BusinessCodeUtils::getCode('mp');
        $this->assertTrue(BusinessCodeUtils::parityCheck($codeMp));
	}

    public function testSpecificCodes()
    {
        // some valid codes
        $codes = ['pgnkscceb', 'p2x5q7kbj', 'ujb9t9x5k'];
        foreach ($codes as $code){
            $this->assertTrue(BusinessCodeUtils::parityCheck($code));
        }
    }
}
