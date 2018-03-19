<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use App\Repository\UserRepository;
use App\Entity\User;

class FormTest extends WebTestCase
{
    public function testBookCreateWhenLogged()
    {
        $client = $this->createClient();
        
        $client->request("GET", "/api/v1/books?api_key=VALIDAPIKEY");
        $response = json_decode($client->getResponse()->getContent());
        $count_old = count($response->books);
        
        //Сначала клиенту необходимо залогиниться, иначе будет редирект на страницу логина
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton("Login")->form(array(
            "_username"  => "test",
            "_password"  => "test",
            ));     
        $client->submit($form);
        
        $this->assertTrue($client->getResponse()->isRedirect());
        
        $crawler = $client->followRedirect();

        $crawler = $client->request("GET", "/gallery/book/create");
        $form = $crawler->selectButton("Create book")->form(array(
            "name"  => "LEGITBOOK",
            "author"  => "LEGITAUTHOR",
            "read_at"  => "2010-01-01",
            ));     
        $client->submit($form);
        
        $client->request("GET", "/api/v1/books?api_key=VALIDAPIKEY");
        $response = json_decode($client->getResponse()->getContent());
        $count_new = count($response->books);
        $this->assertEquals($count_old+1, $count_new);
    }
    
    public function testBookCreateWhenNotLogged()
    {
        $client = $this->createClient();

        $client->request("GET", "/api/v1/books?api_key=VALIDAPIKEY");
        $response = json_decode($client->getResponse()->getContent());
        $count_old = count($response->books);
        
        $client->request("GET", "/gallery/book/create");
        $this->assertTrue($client->getResponse()->isRedirect());
        
        $client->request("GET", "/api/v1/books?api_key=VALIDAPIKEY");
        $response = json_decode($client->getResponse()->getContent());
        $count_new = count($response->books);
        $this->assertEquals($count_old, $count_new);
    }
    
}
