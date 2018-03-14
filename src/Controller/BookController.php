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
	public function getBooks(Request $request)
	{
		$serializer = JMS\Serializer\SerializerBuilder::create()->build();
		$bookList = $this->getDoctrine()->getRepository(Book::class)->findAll();
		$json = $serializer->serialize($bookList, "json");
		return new Response($json);
	}

    public function editBook($id, Request $request)
    {
		$serializer = JMS\Serializer\SerializerBuilder::create()->build();
		try
		{	
			if (!$request->input("name")) throw new Exception("No name provided");
			if (!$request->input("author")) throw new Exception("No author's name provided");
			if (!$request->input("read_at")) throw new Exception("No completion date provided");
			if (!$request->input("api_key")) throw new Exception("No API key provided");

			$manager = $this->getDoctrine()->getManager();
			
			$book = $this->getDoctrine()->getRepository(Book::class)->find($id);
			if (!book) throw new Exception("Invalid book ID");
			$book->setName($request->input("name"));
			$book->setAuthor($request->input("author"));
			$book->setReadAt($request->input("read_at"));

			$manager->persist($book);

			$manager->flush();
			
			$json = $serializer->serialize({"result"=>"ok", "message"=>$book->getId(), "json");
			return new Response($json);
		}
		catch(Exception $e)
		{
			$json = $serializer->serialize({"result"=>"fail", "message"=>e->getMessage(), "json");
			return new Response($json);
		}
    }

	public function addBook(Request $request)
	{
		$serializer = JMS\Serializer\SerializerBuilder::create()->build();
		try
		{	
			if (!$request->input("name")) throw new Exception("No name provided");
			if (!$request->input("author")) throw new Exception("No author's name provided");
			if (!$request->input("read_at")) throw new Exception("No completion date provided");
			if (!$request->input("api_key")) throw new Exception("No API key provided");

			$manager = $this->getDoctrine()->getManager();
			
			$book = new Book();
			$book->setName($request->input("name"));
			$book->setAuthor($request->input("author"));
			$book->setReadAt($request->input("read_at"));

			$manager->persist($book);

			$manager->flush();
			
			$json = $serializer->serialize({"result"=>"ok", "message"=>$book->getId(), "json");
			return new Response($json);
		}
		catch(Exception $e)
		{
			$json = $serializer->serialize({"result"=>"fail", "message"=>e->getMessage(), "json");
			return new Response($json);
		}
	}
}
