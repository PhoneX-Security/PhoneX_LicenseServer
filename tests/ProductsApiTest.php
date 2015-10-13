<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductsApiTest extends TestCase {
    // this wraps all tests in a transaction
    use DatabaseTransactions;

    const URL_GOOGLE = '/api/auth/products/google';
    const URL_APPLE = '/api/auth/products/apple';

    public function setUp(){
        // has to do this here before the framework is started because phpunit prints something before headers are sent
        @session_start();
        parent::setUp();
    }

    public function testRetrieveProducts(){
        // test that api actually works (assuming we have at least one product and at least one product for apple platform)

        $response2 = $this->call('GET', self::URL_APPLE, [])->getContent();
        $this->assertNotEmpty(json_decode($response2));

        $response3 = $this->call('GET', self::URL_APPLE . "_bullshit_google_bullshit", [])->getContent();
        $this->assertEmpty(json_decode($response3));
    }
}