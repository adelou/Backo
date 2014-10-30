<?php

namespace App\ECommerceBundle\Entity\SAV;

use Doctrine\ORM\Mapping as ORM;
use App\AdminBundle\Entity\AbstractDefault;

/**
 * Message
 *
 * @ORM\Table(name="sav_message")
 * @ORM\Entity(repositoryClass="App\ECommerceBundle\Entity\SAV\MessageRepository")
 */
class Message extends AbstractDefault
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
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="\App\ECommerceBundle\Entity\SAV\Ticket", inversedBy="messages", cascade={"persist"})
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id")
     */
    private $ticket;

    /**
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", cascade={"persist"})
     */
    private $user;

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
     * Set content
     *
     * @param string $content
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set ticket
     *
     * @param \App\ECommerceBundle\Entity\SAV\Ticket $ticket
     * @return Message
     */
    public function setTicket(\App\ECommerceBundle\Entity\SAV\Ticket $ticket = null)
    {
        $this->ticket = $ticket;
        return $this;
    }

    /**
     * Get ticket
     *
     * @return \App\ECommerceBundle\Entity\SAV\Ticket 
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
}
