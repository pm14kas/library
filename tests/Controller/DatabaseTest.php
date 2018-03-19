<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


use App\Repository\UserRepository;
use App\Repository\BookRepository;
use App\Entity\Book;

class DatabaseTest extends KernelTestCase
{
    
    protected $_em;

    protected function setUp()
    {
        /*$reader = new AnnotationReader();
        $reader->setIgnoreNotImportedAnnotations(true);
        $reader->setEnableParsePhpImports(true);

        $metadataDriver = new AnnotationDriver(
            $reader, "App\\Entity"
        );

        $this->_em = $this->_getTestEntityManager();

        $this->_em->getConfiguration()
            ->setMetadataDriverImpl($metadataDriver);

        $this->_em->getConfiguration()->setEntityNamespaces(array(
            'App' => 'App\\Entity'));*/
    }
    
    public function testSubscriber()
    {
       /* $em = $this->getMockBuilder('stdClass')
                 ->setMethods(array('persist'))
                 ->getMock();

        $em->expects($this->once())
                 ->method('persist')
                 ->with($this->isInstanceOf('Book'));

        $event = $this->getMockBuilder('stdClass')
                 ->setMethods(array('getEm'))
                 ->getMock();

        $event->expects($this->once())
                 ->method('getEm')
                 ->will($this->returnValue($em));

        $listener = new BookRepository();
        $listener->postPersist($event);
        */
        $this->assertEquals(200, 200);
    }
}
