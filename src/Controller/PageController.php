<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Book;

class PageController extends Controller
{
    public function index()
    {
         return $this->render('index.html.twig', ["user" => $this->getUser()]);
    }
    
    public function apiHelpPage()
    {
         return $this->render('apiHelp.html.twig', ["api_key" => $this->getParameter("api_key")]);
    }

    public function gallery()
    {
        $bookList = $this->getDoctrine()->getRepository(Book::class)->findAllOrderBy("updated_at");

        return $this->render("gallery.html.twig", ["bookList" => $bookList, "user" => $this->getUser()]);
    }
    
    public function bookCreate(Request $request)
    {
        if ($this->getUser())
        {
            return $this->render("bookCreate.html.twig", ["user" => $this->getUser(), "error" => $request->get("error")]);
        }
        else {
            return $this->redirect($this->generateUrl("index"));
        }
    }
    
        
    public function bookEdit($id, Request $request)
    {
        if ($this->getUser())
        {
			$book = $this->getDoctrine()->getRepository(Book::class)->find($id);
			if (!$book) {
                throw new NotFoundException();
			}
            $book->setReadAt($book->getReadAt()->format("Y-m-d"));
            return $this->render("bookEdit.html.twig", ["book" => $book, "error" => $request->get("error")]);
        }
        else {
            return $this->redirect($this->generateUrl("index"));
        }
    }
}
