<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;

class DatabaseTest extends WebTestCase
{
    private function auth($client)
    {
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton("Login")->form(array(
            "_username"  => "test",
            "_password"  => "test",
            ));
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();
        return $client;
    }
    
    public function testTruncate()
    {
        $client = static::createClient();
        $client = $this->auth($client);
        
        $client->request("GET", "/api/v1/books?api_key=VALIDAPIKEY");
        $response = json_decode($client->getResponse()->getContent());
        foreach ($response->books as $book) {
            $client->request("GET", "/gallery/book/".$book->id."/delete");
        }
        
        $client->request("GET", "/api/v1/books?api_key=VALIDAPIKEY");
        $response = json_decode($client->getResponse()->getContent());
        $count_new = count($response->books);
        $this->assertEquals(0, $count_new);
    }
    
    
    public function testBookFileDeletion()
    {
        $client = static::createClient();
        
        $client = $this->auth($client);

        $crawler = $client->request("GET", "/gallery/book/create");
        $form = $crawler->selectButton("Create book")->form(array(
            "name"  => "LEGITBOOK",
            "author"  => "LEGITAUTHOR",
            "read_at"  => "2010-01-01",
            ));
        $form["cover"]->upload("tests/TestFiles/cover.jpeg");
        $form["file"]->upload("tests/TestFiles/book.pdf");
        $form["allowed"]->tick();
        $client->submit($form);
        $crawler = $client->followRedirect();
        
        $client->request("GET", "/api/v1/books?api_key=VALIDAPIKEY");
        $response = json_decode($client->getResponse()->getContent());
        $book = $response->books[0];
        
        $cover = substr($book->cover, 1+strrpos($book->cover, "/"));
        $file = substr($book->file, 1+strrpos($book->file, "/"));
        
        $this->assertTrue(file_exists("./upload/cover/".$cover));
        $this->assertTrue(file_exists("./upload/file/".$file));
        
        $client->request("GET", "/gallery/book/".$book->id."/delete");
        
                
        $this->assertTrue(!file_exists("./upload/cover/".$cover));
        $this->assertTrue(!file_exists("./upload/file/".$file));
        
        rmdir("./upload/cover/");
        rmdir("./upload/file/");
        rmdir("./upload/");
    }
}
