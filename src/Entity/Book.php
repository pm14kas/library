<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="string", length=255) */
	private $name;

    /** @ORM\Column(type="string", length=255) */
	private $author;

    /** @ORM\Column(type="string", length=255) */
	private $cover;

    /** @ORM\Column(type="string", length=255) */
	private $link;

	/** @ORM\Column(type="datetime") */
	private $read_at;

	

    // add your own fields
}
