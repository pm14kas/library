<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

use \JMS\Serializer\SerializerBuilder;

use App\Entity\Book;

class BookController extends Controller
{
	
	public function bookHandler(Request $request)
    {
		try
		{	
			if (!$request->get("name")) {
				throw new AccessDeniedException("name");
			}
			if (!$request->get("author")) {
				throw new AccessDeniedException("author");
			}
			if (!$request->get("read_at")) {
				throw new AccessDeniedException("read_at");
			}

			$manager = $this->getDoctrine()->getManager();
			
			if (!$request->get("id")) {
				$book = new Book();
			}
			else {
                $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
				if (!book) {
					throw new AccessDeniedException("id");
				}
			}

			$book->setName($request->get("name"));
			$book->setAuthor($request->get("author"));
            
			$book->setReadAt(\DateTime::createFromFormat("Y-m-d", $request->get("read_at")));
            if ($book->getReadAt()==false) {
                $book->setReadAt(\DateTime::createFromFormat("m-d-Y", $request->get("read_at")));
                if ($book->getReadAt()==false) {
                    throw new AccessDeniedException("read_at");
                }
            }
			    
			if (!$request->files->get("cover") or !$request->file("localFile")->isValid()) {
				$book->setCover(null);
			}
			else {	
				$file = $request->file->input("cover");
				//if ($file->
				$ext = $file->getClientOriginalExtension();
				$mime = $file->getMimeType();
				$name = time().substr(microtime(), 2, 3);
				
				$typeok = TRUE;
				switch($mime)
				{
					case "image/gif":
					case "image/jpeg": 
					case "image/pjpeg":
					case "image/png": break;
					default: $typeok = FALSE; break;
				}
				if ($typeok)
				{
					$file->move($this->makePath()."images/upload/", $name.".".$ext);
					return json_encode(["response"=>"ok", "code"=>"200", "message" => "Success", "url" => url("/")."/images/upload/".$name.".".$ext]);
				}
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
			
            return $this->redirect($this->generateUrl("gallery"));
		}
		catch(AccessDeniedException $e)
		{
			return $this-> redirect($this->generateUrl("book_create_page", ["error" => $e->getMessage()]));
		}
    }
	
	
	
	
	
	
	
    /**
    *API goes here
    */  
            
	
	
	public function apiGetBooks(Request $request)
	{
        $serializer = SerializerBuilder::create()->build();
        try
        {	
            if (!$request->get("api_key")) {
                throw new AccessDeniedException("No API key provided");
			}
            else if ($request->get("api_key")!=$this->getParameter('api_key')) {
                throw new AccessDeniedException("Wrong API key");
            }
            
            $bookList = $this->getDoctrine()->getRepository(Book::class)->findAll();
            $json = $serializer->serialize($bookList, "json");
            return new Response($json);
        }
        catch(AccessDeniedException $e)
		{
			$json = $serializer->serialize(["result" => "fail", "message" => $e->getMessage()], "json");
			return new Response($json);
		}
	}

    public function apiEditBook($id, Request $request)
    {
		$serializer = SerializerBuilder::create()->build();
		try
		{	
            if (!$request->get("api_key")) {
                throw new AccessDeniedException("No API key provided");
			}
            else if ($request->get("api_key")!=$this->getParameter('api_key')) {
                throw new AccessDeniedException("Wrong API key");
            }
            
			if (!$request->get("name")) {
                throw new AccessDeniedException("No name provided");
			}
			if (!$request->get("author")) {
                throw new AccessDeniedException("No author's name provided");
			}
			if (!$request->get("read_at")) {
                throw new AccessDeniedException("No completion date provided");
			}
			if (!$request->get("api_key")) {
                throw new AccessDeniedException("No API key provided");
			}
            

			$manager = $this->getDoctrine()->getManager();
			
			$book = $this->getDoctrine()->getRepository(Book::class)->find($id);
			if (!book) {
                throw new AccessDeniedException("Invalid book ID");
			}
			$book->setName($request->get("name"));
			$book->setAuthor($request->get("author"));
			$book->setReadAt($request->get("read_at"));

			$manager->persist($book);

			$manager->flush();
			
			$json = $serializer->serialize(["result" => "ok", "message" => $book->getId()], "json");
			return new Response($json);
		}
		catch(AccessDeniedException $e)
		{
			$json = $serializer->serialize(["result" => "fail", "message" => $e->getMessage()], "json");
			return new Response($json);
		}
    }

	public function apiAddBook(Request $request)
	{
		$serializer = SerializerBuilder::create()->build();
		try
		{	
            if (!$request->get("api_key")) {
                throw new AccessDeniedException("No API key provided");
			}
            else if ($request->get("api_key")!=$this->getParameter('api_key')) {
                throw new AccessDeniedException("Wrong API key");
            }
            
			if (!$request->get("name")) {
                throw new AccessDeniedException("No name provided");
			}
			if (!$request->get("author")) {
                throw new AccessDeniedException("No author's name provided");
			}
			if (!$request->get("read_at")) {
                throw new AccessDeniedException("No completion date provided");
			}


			$manager = $this->getDoctrine()->getManager();
			
			$book = new Book();
			$book->setName($request->get("name"));
			$book->setAuthor($request->get("author"));
			$book->setReadAt($request->get("read_at"));

			$manager->persist($book);

			$manager->flush();
			
			$json = $serializer->serialize(["result" => "ok", "message" => $book->getId()], "json");
			return new Response($json);
		}
		catch(AccessDeniedException $e)
		{
			$json = $serializer->serialize(["result" => "fail", "message" => $e->getMessage()], "json");
			return new Response($json);
		}
	}
}
