<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 * @ORM\HasLifecycleCallbacks
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

    /** @ORM\Column(type="string", nullable=true, length=255) */
	private $cover = null;

    /** @ORM\Column(type="string", nullable=true, length=255) */
	private $link = null;

	/** @ORM\Column(type="datetime") */
	private $read_at;

	/** @ORM\Column(type="datetime") */
	private $created_at;

	/** @ORM\Column(type="datetime") */
	private $updated_at;

	function __construct()
	{
		$this->setCreatedAt(new \DateTime());
	}

	/** 
	* @ORM\PrePersist
	* @ORM\PreUpdate
	*/
	public function updateTimestamps()
	{
		$this->setUpdatedAt(new \DateTime('now'));

		if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
		}
	}
	
	/** 
	* @ORM\PreRemove
	*/
	public function deleteFiles()
	{
        if ($this->getCover() == null) {
		    unlink($this->getCover());
		}
		
		if ($this->getLink() == null) {
		    unlink($this->getLink());
		}
	}

	public function getId()
	{
		return $this->id;
	}	

	public function setId($_id)
	{
		$this->id = $_id;
	}	


	public function getName()
	{
		return $this->name;
	}	

	public function setName($_name)
	{
		$this->name = $_name;
	}	


	public function getAuthor()
	{
		return $this->author;
	}	

	public function setAuthor($_author)
	{
		$this->author = $_author;
	}	


	public function getCover()
	{
		return $this->cover;
	}	

	public function setCover($_cover)
	{
		$this->cover = $_cover;
	}	


	public function getLink()
	{
		return $this->link;
	}	

	public function setLink($_link)
	{
		$this->link = $_link;
	}	
    
    
	public function getReadAt()
	{
		return $this->read_at;
	}	

	public function setReadAt($_read_at)
	{
		$this->read_at = $_read_at;
	}
    

	public function getCreatedAt()
	{
		return $this->created_at;
	}	

	public function setCreatedAt($_created_at)
	{
		$this->created_at = $_created_at;
	}	


	public function getUpdatedAt()
	{
		return $this->updated_at;
	}	

	public function setUpdatedAt($_updated_at)
	{
		$this->updated_at = $_updated_at;
	}	

}
