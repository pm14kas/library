<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\Serializer;

use App\Entity\Book;

class BookController
{
	
	public function bookHandler(Request $request)
    {
		$serializer = JMS\Serializer\SerializerBuilder::create()->build();
		try
		{	
			if (!$request->input("name")) {
				throw new Exception("No name provided");
			}
			if (!$request->input("author")) {
				throw new Exception("No author's name provided");
			}
			if (!$request->input("read_at")) {
				throw new Exception("No completion date provided");
			}

			$manager = $this->getDoctrine()->getManager();
			
			if (!$request->input("id")) {
				$book = new Book();
			}
			else {
                $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
				if (!book) {
					throw new Exception("Invalid book ID");
				}
			}

			$book->setName($request->input("name"));
			$book->setAuthor($request->input("author"));
			$book->setReadAt($request->input("read_at"));
			
			if (!$request->files->get("cover")) {
				$book->setCover(null);
			}
			else {
                //upload image, check if it is valid, rename to current_timestamp
				//then put global link to db like "http://mysite/uploads/covers/156727378992000.jpg"
				//docs on symfony upload http://symfony.com/doc/current/controller/upload_file.html
			}
			
			if (!$request->files->get("file")) {
				$book->setLink(null);
			}
			else {
				//upload file, check if it is valid, rename to current_timestamp
				//then put global link to db like "http://mysite/uploads/files/156727378992640.pdf"
				//docs on symfony upload http://symfony.com/doc/current/controller/upload_file.html
			}

			$manager->persist($book);

			$manager->flush();
			
			$json = $serializer->serialize({"result" => "ok", "message" => $book->getId(), "json");
			return new Response($json);
		}
		catch(Exception $e)
		{
			$json = $serializer->serialize({"result" => "fail", "message" => e->getMessage(), "json");
			return new Response($json);
		}
    }
	
	
	
	
	
	
	
    /**
    *API goes here
    */
	
	
	
	
	public function apiGetBooks(Request $request)
	{
		$serializer = JMS\Serializer\SerializerBuilder::create()->build();
		$bookList = $this->getDoctrine()->getRepository(Book::class)->findAll();
		$json = $serializer->serialize($bookList, "json");
		return new Response($json);
	}

    public function apiEditBook($id, Request $request)
    {
		$serializer = JMS\Serializer\SerializerBuilder::create()->build();
		try
		{	
			if (!$request->input("name")) {
                throw new Exception("No name provided");
			}
			if (!$request->input("author")) {
                throw new Exception("No author's name provided");
			}
			if (!$request->input("read_at")) {
                throw new Exception("No completion date provided");
			}
			if (!$request->input("api_key")) {
                throw new Exception("No API key provided");
			}

			$manager = $this->getDoctrine()->getManager();
			
			$book = $this->getDoctrine()->getRepository(Book::class)->find($id);
			if (!book) {
                throw new Exception("Invalid book ID");
			}
			$book->setName($request->input("name"));
			$book->setAuthor($request->input("author"));
			$book->setReadAt($request->input("read_at"));

			$manager->persist($book);

			$manager->flush();
			
			$json = $serializer->serialize({"result" => "ok", "message" => $book->getId(), "json");
			return new Response($json);
		}
		catch(Exception $e)
		{
			$json = $serializer->serialize({"result" => "fail", "message" => e->getMessage(), "json");
			return new Response($json);
		}
    }

	public function apiAddBook(Request $request)
	{
		$serializer = JMS\Serializer\SerializerBuilder::create()->build();
		try
		{	
			if (!$request->input("name")) {
                throw new Exception("No name provided");
			}
			if (!$request->input("author")) {
                throw new Exception("No author's name provided");
			}
			if (!$request->input("read_at")) {
                throw new Exception("No completion date provided");
			}
			if (!$request->input("api_key")) {
                throw new Exception("No API key provided");
			}

			$manager = $this->getDoctrine()->getManager();
			
			$book = new Book();
			$book->setName($request->input("name"));
			$book->setAuthor($request->input("author"));
			$book->setReadAt($request->input("read_at"));

			$manager->persist($book);

			$manager->flush();
			
			$json = $serializer->serialize({"result" => "ok", "message" => $book->getId(), "json");
			return new Response($json);
		}
		catch(Exception $e)
		{
			$json = $serializer->serialize({"result" => "fail", "message" => e->getMessage(), "json");
			return new Response($json);
		}
	}
}
