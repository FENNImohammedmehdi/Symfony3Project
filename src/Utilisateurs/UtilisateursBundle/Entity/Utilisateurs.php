<?php

namespace Utilisateurs\UtilisateursBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraint as Asset ;


class Utilisateurs extends BaseUser {

    protected $id;
    private $age;
    private $famille;
    private $race;
    private $nourriture;
    private $friendsWithMe;
    private $myFriends;

    
    
    
    
    
    public function __construct() {
        parent::__construct();
        $this->friendsWithMe = new ArrayCollection();
        $this->myFriends = new ArrayCollection();
    }

   
    
  
    public function getId() {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    
    
    
    
    public function getAge() {
        return $this->age;
    }
    
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    public function getFamille() {
        return $this->famille;
    }

    public function setFamille($famille)
    {
        $this->famille = $famille;

        return $this;
    }
    public function getRace() {
        return $this->race;
    }

    
    public function setRace($race)
    {
        $this->race = $race;

        return $this;
    }
    
    public function getNourriture() {
        return $this->nourriture;
    }

    public function setNourriture($nourriture)
    {
        $this->nourriture = $nourriture;

        return $this;
    }
    


    
    public function setMyFriends($myFriends)
    {
        $this->myFriends = $myFriends;

        return $this;
    }    
    

    
    
    public function setFriendsWithMe($friendsWithMe)
    {
        $this->friendsWithMe = $friendsWithMe;

        return $this;
    }
    
    public function removeFriendsWithMe($friendsWithMe)
    {
        $this->friendsWithMe->removeElement($friendsWithMe);
    }

    public function removeMyFriend( $myFriends)
    {
        $this->myFriends->removeElement($myFriends);
    }

    public function addFriendsWithMe($friendsWithMe)
    {
        $this->friendsWithMe[] = $friendsWithMe;
        return $this;
    }
    
    
    public function getFriendsWithMe()
    {
        return $this->friendsWithMe;
    }
    
    public function addMyFriends($myFriends)
    {
        $this->myFriends[] = $myFriends;
        return $this;
    }
   
    public function getMyFriends()
    {
        return $this->myFriends;
    }

    public function setAllFriends(){
        $this->allfriends = new ArrayCollection(
                            array_merge($this->friendsWithMe->toArray(), $this->myFriends->toArray()    )
                            ); 
        return $this;
    }
    
    
 


}
