<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CForumMailcue
 *
 * @ORM\Table(
 *  name="c_forum_mailcue",
 *  indexes={
 *      @ORM\Index(name="course", columns={"c_id"}),
 *      @ORM\Index(name="thread", columns={"thread_id"}),
 *      @ORM\Index(name="user", columns={"user_id"}),
 *      @ORM\Index(name="post", columns={"post_id"})
 *  }
 * )
 * @ORM\Entity
 */
class CForumMailcue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="iid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $iid;

    /**
     * @var integer
     *
     * @ORM\Column(name="c_id", type="integer")
     */
    private $cId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="thread_id", type="integer")
     */
    private $threadId;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="post_id", type="integer")
     */
    private $postId;


    /**
     * Set id
     *
     * @param integer $id
     * @return CForumMailcue
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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

    /**
     * Set cId
     *
     * @param integer $cId
     * @return CForumMailcue
     */
    public function setCId($cId)
    {
        $this->cId = $cId;

        return $this;
    }

    /**
     * Get cId
     *
     * @return integer
     */
    public function getCId()
    {
        return $this->cId;
    }

    /**
     * Set threadId
     *
     * @param integer $threadId
     * @return CForumMailcue
     */
    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;

        return $this;
    }

    /**
     * Get threadId
     *
     * @return integer
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return CForumMailcue
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set postId
     *
     * @param integer $postId
     * @return CForumMailcue
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;

        return $this;
    }

    /**
     * Get postId
     *
     * @return integer
     */
    public function getPostId()
    {
        return $this->postId;
    }
}
