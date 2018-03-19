<?php

namespace App\Controller;

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

    public function gallery(Request $request)
    {
        $bookList = $this->getDoctrine()->getRepository(Book::class)->findAllOrderBy("read_at");
        $addition = $request->getSchemeAndHttpHost();
        if ($addition[strlen($addition)-1]!="/") {
            $addition = $addition."/";
        }
        foreach ($bookList as &$book) {
            if ($book->getCover()) {
                $book->setCover($addition.$book->getCover());
            }
            if ($book->getFile() and ($book->getAllowed() or $this->getUser())) {
                $book->setFile($addition.$book->getFile());
            } else {
                $book->setFile(null);
            }
        }
        return $this->render("gallery.html.twig", ["bookList" => $bookList, "user" => $this->getUser()]);
    }
    
    public function bookCreate(Request $request)
    {
        // if ($this->getUser())
        // {
        return $this->render("bookCreate.html.twig", ["user" => $this->getUser(), "error" => $request->get("error")]);
        //}
        //else {
        //    return $this->redirect($this->generateUrl("index"));
        //}
    }
    
        
    public function bookEdit($id, Request $request)
    {
        // if ($this->getUser())
        //{
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Book is not found');
        }
        $book->setReadAt($book->getReadAt()->format("Y-m-d"));
        return $this->render("bookEdit.html.twig", ["book" => $book, "error" => $request->get("error")]);
        // }
       // else {
        //    return $this->redirect($this->generateUrl("index"));
        //}
    }
}
