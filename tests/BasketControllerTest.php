<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasketControllerTest extends WebTestCase
{
	private $id;

	public function testAdd()
    {
    	$client = static::createClient();

    	$basketName = "Basket#". mt_rand();

    	$client->request(
		    'POST',
		    '/basket/add',
		    [],
		    [],
		    ['CONTENT_TYPE' => 'application/json'],
		    '{"name":"'. $basketName .'", "capacity": 150}'
		);

		$this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testIndex()
    {
    	$client = static::createClient();

        $client->request('GET', '/basket/');

        $this->assertResponseIsSuccessful();
    }

    public function testShow()
    {
    	$client = static::createClient();

        $client->request('GET', '/basket/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEdit()
    {
    	$client = static::createClient();

    	$updatedName = 'UpdatedBasket#'. mt_rand();

        $client->request(
        	'PUT',
        	'/basket/1/edit',
        	[],
        	[],
        	['CONTENT_TYPE' => 'application/json'],
        	'{"name": "'. $updatedName .'"}'
        );

        $this->assertEquals('{"status":true}', $client->getResponse()->getContent());
    }

    public function testAddItems()
    {
    	$client = static::createClient();

    	$body = [
    		[
    			'type_id' => 1,
    			'weight' => 20,
    		],
    		[
    			'type_id' => 2,
    			'weight' => 10,
    		],
    		[
    			'type_id' => 3,
    			'weight' => 50,
    		],
    		[
    			'type_id' => 2,
    			'weight' => 40,
    		],
    	];

        $client->request(
        	'POST',
        	'/basket/1/items/add',
        	[],
        	[],
        	['CONTENT_TYPE' => 'application/json'],
        	json_encode($body)
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testDeleteItems()
    {
    	$client = static::createClient();

    	$body = [1, 2, 3, 4];

        $client->request(
        	'DELETE',
        	'/basket/1/items/delete',
        	[],
        	[],
        	['CONTENT_TYPE' => 'application/json'],
        	json_encode($body)
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDelete()
    {
    	$client = static::createClient();

        $client->request(
        	'DELETE',
        	'/basket/1/delete',
        	[],
        	[],
        	['CONTENT_TYPE' => 'application/json'],
        	null
        );

        $this->assertEquals(202, $client->getResponse()->getStatusCode());
    }
}
