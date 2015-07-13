<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phonex\Company;

class ExampleTest extends TestCase
{
	use DatabaseTransactions;

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

//	public function testDbTransaction()
//	{
//		$comp = new Company();
//		$comp->name = "BanaCigel";
//		$comp->save();
//	}

}
