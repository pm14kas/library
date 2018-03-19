<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use \JMS\Serializer\SerializerBuilder;
use App\Entity\Book;

class BookController extends Controller
{
    public function bookHandler(Request $request, $id = null)
    {
        try {
            /** Validation section
             *
             */
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
            
            if ($id==null) {
                $book = new Book();
            } else {
                $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
                if (!$book) {
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
            
            if ($request->get("remove_cover")) {
                if (file_exists($book->getCover())) {
                    unlink($book->getCover());
                }
                
                $book->setCover(null);
            }
            
            
            /** Cover section
             *
             */
            
            if (!($request->files->get("cover"))) {
                if ($id==null) {
                    $book->setCover(null);
                }
            } elseif (!($request->files->get("cover")->isValid() and
                    $request->files->get("cover")->getClientSize() < 5*1024*1024)
                    ) {
                throw new AccessDeniedException("cover");
            } else {
                $file = $request->files->get("cover");
                $filename = time().substr(microtime(), 2, 3).'.'.$file->guessExtension();
                
                if (file_exists($book->getCover())) {
                    unlink($book->getCover());
                }
                
                //Удаляеят файлы обложки в случае ее изменения
                //в сабскрайбере или lifecycle callback это не реализовать (?)
                $file->move("./upload/cover/", $filename);
                $book->setCover("upload/cover/".$filename);
            }
            
            if ($request->get("remove_file")) {
                if (file_exists($book->getFile())) {
                    unlink($book->getFile());
                }
                
                $book->setFile(null);
            }
            
            
            
            /** File section
             *
             */
            if (!($request->files->get("file"))) {
                if ($id==null) {
                    $book->setFile(null);
                }
            } elseif (!($request->files->get("file")->isValid() and
                    $request->files->get("file")->getClientSize() < 5*1024*1024)
                    ) {
                throw new AccessDeniedException("file");
            } else {
                $file = $request->files->get("file");
                $filename = time().substr(microtime(), 2, 3).'.'.$file->guessExtension();
                if (file_exists($book->getFile())) {
                    unlink($book->getFile());
                }
                
                $file->move("./upload/file/", $filename);
                $book->setFile("upload/file/".$filename);
            }
            
            if ($request->get("allowed")) {
                $book->setAllowed($request->get("allowed"));
            } else {
                $book->setAllowed(false);
            }

            $manager->persist($book);
            $manager->flush();
            return $this->redirect($this->generateUrl("gallery"));
        } catch (AccessDeniedException $e) {
            if ($id==null) {
                return $this->redirect($this->generateUrl("book_create_page", ["error" => $e->getMessage()]));
            } elseif ($e->getMessage()==="id") {
                return $this-> redirect($this->generateUrl("gallery"));
            } else {
                $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
                return $this-> redirect(
                    $this->generateUrl(
                        "book_edit_page",
                        ["error" => $e->getMessage(), "id" => $id, "book" => $book]
                    )
                );
            }
        }
    }
    
    public function bookDelete($id)
    {
        if ($id) {
            $manager = $this->getDoctrine()->getManager();
            $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
            if ($book) {
                $manager->remove($book);
                $manager->flush();
            }
        }
        return $this->redirect($this->generateUrl("gallery"));
    }
    
    
    
    /**
    *API goes here
    */
            
    
    
    public function apiGetBooks(Request $request)
    {
        $serializer = SerializerBuilder::create()->build();
        try {
            if (!$request->get("api_key")) {
                throw new AccessDeniedException("No API key provided");
            } elseif ($request->get("api_key")!=$this->getParameter('api_key')) {
                throw new AccessDeniedException("Wrong API key");
            }
            
            $bookList = $this->getDoctrine()->getRepository(Book::class)->findAllOrderBy("read_at");
            $addition = $request->getSchemeAndHttpHost();
            if ($addition[strlen($addition)-1]!="/") {
                $addition = $addition."/";
            }
            foreach ($bookList as &$book) {
                if ($book->getCover()) {
                    $book->setCover($addition.$book->getCover());
                }
                if ($book->getFile() and ($book->getAllowed())) {
                    $book->setFile($addition.$book->getFile());
                } else {
                    $book->setFile(null);
                }
            }
            $json = $serializer->serialize(["result" => "ok", "books" => $bookList], "json");
            return new Response($json);
        } catch (AccessDeniedException $e) {
            $json = $serializer->serialize(["result" => "fail", "message" => $e->getMessage()], "json");
            return new Response($json);
        }
    }

    public function apiEditBook($id, Request $request)
    {
        $serializer = SerializerBuilder::create()->build();
        try {
            if (!$request->get("api_key")) {
                throw new AccessDeniedException("No API key provided");
            } elseif ($request->get("api_key")!=$this->getParameter('api_key')) {
                throw new AccessDeniedException("Wrong API key");
            }
            
            $manager = $this->getDoctrine()->getManager();
            
            $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
            if (!$book) {
                throw new AccessDeniedException("Invalid book ID");
            }
            if ($request->get("name")) {
                $book->setName($request->get("name"));
            }
            
            if ($request->get("author")) {
                $book->setAuthor($request->get("author"));
            }
            
            if ($request->get("read_at")) {
                $book->setReadAt(\DateTime::createFromFormat("Y-m-d", $request->get("read_at")));
                if ($book->getReadAt()==false) {
                    $book->setReadAt(\DateTime::createFromFormat("m-d-Y", $request->get("read_at")));
                    if ($book->getReadAt()==false) {
                        throw new AccessDeniedException("Invalid completion date");
                    }
                }
            }
            
            if ($request->get("allowed")) {
                $book->setAllowed($request->get("allowed"));
            }

            $manager->persist($book);
            $manager->flush();
            
            $json = $serializer->serialize(["result" => "ok", "message" => $book->getId()], "json");
            return new Response($json);
        } catch (AccessDeniedException $e) {
            $json = $serializer->serialize(["result" => "fail", "message" => $e->getMessage()], "json");
            return new Response($json);
        }
    }

    
    public function apiAddBook(Request $request)
    {
        $serializer = SerializerBuilder::create()->build();
        try {
            if (!$request->get("api_key")) {
                throw new AccessDeniedException("No API key provided");
            } elseif ($request->get("api_key")!=$this->getParameter('api_key')) {
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
            $book->setReadAt(\DateTime::createFromFormat("Y-m-d", $request->get("read_at")));
            
            if ($book->getReadAt()==false) {
                $book->setReadAt(\DateTime::createFromFormat("m-d-Y", $request->get("read_at")));
                if ($book->getReadAt()==false) {
                    throw new AccessDeniedException("Invalid completion date");
                }
            }
            
            if ($request->get("allowed")) {
                $book->setAllowed($request->get("allowed"));
            } else {
                $book->setAllowed(false);
            }
            
            $manager->persist($book);

            $manager->flush();
            
            $json = $serializer->serialize(["result" => "ok", "message" => $book->getId()], "json");
            return new Response($json);
        } catch (AccessDeniedException $e) {
            $json = $serializer->serialize(["result" => "fail", "message" => $e->getMessage()], "json");
            return new Response($json);
        }
    }
}
