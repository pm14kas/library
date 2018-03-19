<?php
namespace App\Tests\Controller;

use App\Entity\Book;
use App\Controller\BookController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{
    public function testInsertWithBadKey()
    {
        $client = static::createClient();
        $client->request("GET", "/api/v1/books/add?name=LEGITBOOK&author=LEGITAUTHOR&read_at=2014-11-16&api_key=INVALIDAPIKEY");
        $this->assertEquals("fail", json_decode($client->getResponse()->getContent())->result);
    }
    
    public function testInsertWithGoodKey()
    {
        $client = static::createClient();
        $client->request("GET", "/api/v1/books?api_key=VALIDAPIKEY");
        $response = json_decode($client->getResponse()->getContent());
        $count_old = count($response->books);
        
        $client->request("GET", "/api/v1/books/add?name=LEGITBOOK&author=LEGITAUTHOR&read_at=2014-11-16&api_key=VALIDAPIKEY");
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals("ok", $response->result);
        
        $client->request("GET", "/api/v1/books?api_key=VALIDAPIKEY");
        $response = json_decode($client->getResponse()->getContent());
        $count_new = count($response->books);
        $this->assertEquals($count_old+1, $count_new);
    }
    
    public function testInsertMissingName()
    {
        $client = static::createClient();
        $client->request("GET", "/api/v1/books/add?author=LEGITAUTHOR&read_at=2014-11-16&api_key=VALIDAPIKEY");
        $this->assertEquals("fail", json_decode($client->getResponse()->getContent())->result);
    }
    
    public function testInsertMissingParameters()
    {
        $client = static::createClient();
        $client->request("GET", "/api/v1/books/add?api_key=VALIDAPIKEY");
        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals("fail", $response->result);
    }
}
