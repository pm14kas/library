<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Book;

class PageController extends Controller
{
    public function index()
    {
         return $this->render('index.html.twig', ["user" => $this->getUser()]);
    }

	public function gallery()
	{
		$bookList = $this->getDoctrine()->getRepository(Book::class)->findAll();

		return $this->render('gallery.html.twig', ["bookList" => $bookList]);
	}
}
