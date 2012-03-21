<?php

namespace BRS\CoreBundle\Entity;

use BRS\CoreBundle\Core\SuperEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * BRS\CoreBundle\Entity\Member
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Member extends SuperEntity implements UserInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;
   
    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=TRUE)
     */
    public $email;
	
    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=TRUE)
     */
    public $password;
	
    /**
     * @var string $first_name
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=TRUE)
     */
    public $first_name;

    /**
     * @var string $last_name
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=TRUE)
     */
    public $last_name;

    /**
     * @var string $street_1
     *
     * @ORM\Column(name="street_1", type="string", length=255, nullable=TRUE)
     */
    public $street_1;

    /**
     * @var string $street_2
     *
     * @ORM\Column(name="street_2", type="string", length=255, nullable=TRUE)
     */
    public $street_2;

    /**
     * @var string $city
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=TRUE)
     */
    public $city;

    /**
     * @var string $state
     *
     * @ORM\Column(name="state", type="string", length=255, nullable=TRUE)
     */
    public $state;

    /**
     * @var string $zip
     *
     * @ORM\Column(name="zip", type="string", length=20, nullable=TRUE)
     */
    public $zip;

    /**
     * @var string $country
     *
     * @ORM\Column(name="country", type="string", length=40, nullable=TRUE)
     */
    public $country;

    /**
     * @var text $bio
     *
     * @ORM\Column(name="bio", type="text", nullable=TRUE)
     */
    public $bio;

    /**
     * @var date $date_added
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=TRUE)
     */
    public $date_added;

    /**
     * @var date $date_updated
     *
     * @ORM\Column(name="date_updated", type="datetime", nullable=TRUE)
     */
    public $date_updated;

	
	public function __construct(){
		
		$now = new \DateTime;
		
		$this->setDateAdded($now);
	}
	
	/*  User Interface Mapping
	 * -------------------------------------------------------------------------------*/
	
	/**
     * Get Username map of get email
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->email;
    }
	
	/**
     * Get Roles
     *
     * @return array 
     */
    public function getRoles()
    {
        return array('ROLE_ADMIN');
    }
	
	/**
     * Get Salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return "saltysaltsalt";
    }
    
    /**
     * Erase Credentials
     *
     * @return string 
     */
    public function eraseCredentials()
    {
        return;
    }
	
	/**
     * Equals
     *
     * @return string 
     */
    public function equals(UserInterface $user) {
        return;
    }
	
	

	/*  Getters & Setters 
	 * -------------------------------------------------------------------------------*/
		
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set first_name
     *
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
    }

    /**
     * Get first_name
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set street_1
     *
     * @param string $street1
     */
    public function setStreet1($street1)
    {
        $this->street_1 = $street1;
    }

    /**
     * Get street_1
     *
     * @return string 
     */
    public function getStreet1()
    {
        return $this->street_1;
    }

    /**
     * Set street_2
     *
     * @param string $street2
     */
    public function setStreet2($street2)
    {
        $this->street_2 = $street2;
    }

    /**
     * Get street_2
     *
     * @return string 
     */
    public function getStreet2()
    {
        return $this->street_2;
    }

    /**
     * Set city
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set zip
     *
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * Get zip
     *
     * @return string 
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set country
     *
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set bio
     *
     * @param text $bio
     */
    public function setBio($bio)
    {
        $this->bio = $bio;
    }

    /**
     * Get bio
     *
     * @return text 
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set date_added
     *
     * @param date $dateAdded
     */
    public function setDateAdded($dateAdded)
    {
        $this->date_added = $dateAdded;
    }

    /**
     * Get date_added
     *
     * @return date 
     */
    public function getDateAdded()
    {
        return $this->date_added;
    }

    /**
     * Set date_updated
     *
     * @param date $dateUpdated
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->date_updated = $dateUpdated;
    }

    /**
     * Get date_updated
     *
     * @return date 
     */
    public function getDateUpdated()
    {
        return $this->date_updated;
    }
}