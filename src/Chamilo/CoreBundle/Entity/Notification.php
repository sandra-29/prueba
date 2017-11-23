<?php

namespace Chamilo\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="notification", indexes={@ORM\Index(name="mail_notify_sent_index", columns={"sent_at"}), @ORM\Index(name="mail_notify_freq_index", columns={"sent_at", "send_freq", "created_at"})})
 * @ORM\Entity
 */
class Notification
{
    /**
     * @var integer
     *
     * @ORM\Column(name="dest_user_id", type="integer", nullable=false)
     */
    private $destUserId;

    /**
     * @var string
     *
     * @ORM\Column(name="dest_mail", type="string", length=255, nullable=true)
     */
    private $destMail;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="send_freq", type="smallint", nullable=true)
     */
    private $sendFreq;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_at", type="datetime", nullable=true)
     */
    private $sentAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set destUserId
     *
     * @param integer $destUserId
     * @return Notification
     */
    public function setDestUserId($destUserId)
    {
        $this->destUserId = $destUserId;

        return $this;
    }

    /**
     * Get destUserId
     *
     * @return integer
     */
    public function getDestUserId()
    {
        return $this->destUserId;
    }

    /**
     * Set destMail
     *
     * @param string $destMail
     * @return Notification
     */
    public function setDestMail($destMail)
    {
        $this->destMail = $destMail;

        return $this;
    }

    /**
     * Get destMail
     *
     * @return string
     */
    public function getDestMail()
    {
        return $this->destMail;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Notification
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Notification
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
     * Set sendFreq
     *
     * @param integer $sendFreq
     * @return Notification
     */
    public function setSendFreq($sendFreq)
    {
        $this->sendFreq = $sendFreq;

        return $this;
    }

    /**
     * Get sendFreq
     *
     * @return integer
     */
    public function getSendFreq()
    {
        return $this->sendFreq;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Notification
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set sentAt
     *
     * @param \DateTime $sentAt
     * @return Notification
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * Get sentAt
     *
     * @return \DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
