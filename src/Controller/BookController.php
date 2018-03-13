<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\Serializer;

class BookController
{
	public function getBooks()
	{
		return new Response("api getbooks");
	}

    public function editBook($id)
    {
		return new Response("api edit book #".$id);
    }

	public function addBook()
	{
		return new Response("api create");
	}
}
