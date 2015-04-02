<?php

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
		$response = $this->call('GET', '/');

        // without login, we should be redirected first
		$this->assertEquals(302, $response->getStatusCode());

	}

}
