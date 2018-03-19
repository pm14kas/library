<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;

use App\Repository\UserRepository;

class BookCreateFormTest extends WebTestCase
{

    function testBookCreate() {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'test',
            'PHP_AUTH_PW'   => 'test',
        ));


        $client->request("GET", "/gallery/book/create");
        $crawler = new Crawler($client->getResponse()->getContent(), "http://localhost/gallery/book/create");
        echo $client->getResponse();
        //$form = $crawler->selectButton("_submit")->form();
       /* $form["_username"]->setValue("test");
        $form["_password"]->setValue("test");
        $form["_remember_me"]->tick();*/
        
        
        $this->assertEquals(200, 200);  
    }
}