<?php

namespace App\ECommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Command
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Entity\CommandRepository")
 */
class Command
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     */
    private $reference;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalPaid", type="integer")
     */
    private $totalPaid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="currentState", type="boolean")
     */
    private $currentState;


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
     * Set reference
     *
     * @param string $reference
     * @return Command
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string 
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set totalPaid
     *
     * @param integer $totalPaid
     * @return Command
     */
    public function setTotalPaid($totalPaid)
    {
        $this->totalPaid = $totalPaid;

        return $this;
    }

    /**
     * Get totalPaid
     *
     * @return integer 
     */
    public function getTotalPaid()
    {
        return $this->totalPaid;
    }

    /**
     * Set currentState
     *
     * @param boolean $currentState
     * @return Command
     */
    public function setCurrentState($currentState)
    {
        $this->currentState = $currentState;

        return $this;
    }

    /**
     * Get currentState
     *
     * @return boolean 
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }
}
