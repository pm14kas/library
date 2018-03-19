<?php
namespace App\Tests\Controller;

use App\Entity\Book;
use App\Controller\BookController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ApiTest extends WebTestCase
{
	function testInsert() 
    {
        $client = static::createClient();
        $this->container()->setEnv("API_KEY", "LEGITAPIKEY");

        $client->request('GET', '/api/v1/books?api_key=TotallyNotAnAPIKey');
       // echo json_decode(json_encode($client->getResponse()));
        echo $client->getResponse();
        $this->assert("fail", json_encode($client->getResponse()->result));
        
	}
}