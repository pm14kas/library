<?php
// src/AppBundle/Entity/User.php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
	/** @ORM\Column(type="api_key", length=255) */
	private $api_key;


    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
	
	public function getApiKey()
	{
		return $this->api_key;
	}
	
	public function setApiKey($_api_key)
	{
		$this->api_key = $_api_key;
	}
}
