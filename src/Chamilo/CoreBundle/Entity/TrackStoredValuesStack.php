<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrackStoredValuesStack
 *
 * @ORM\Table(
 *  name="track_stored_values_stack",
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="user_id_2", columns={"user_id", "sco_id", "course_id", "sv_key", "stack_order"})
 *  },
 *  indexes={
 *      @ORM\Index(name="user_sco_course_sv_stack", columns={"user_id", "sco_id", "course_id", "sv_key", "stack_order"})
 *  }
 * )
 * @ORM\Entity
 */
class TrackStoredValuesStack
{    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="sco_id", type="integer", nullable=false)
     */
    private $scoId;

    /**
     * @var integer
     *
     * @ORM\Column(name="stack_order", type="integer", nullable=false)
     */
    private $stackOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="course_id", type="string", length=40, nullable=false)
     */
    private $courseId;

    /**
     * @var string
     *
     * @ORM\Column(name="sv_key", type="string", length=64, nullable=false)
     */
    private $svKey;

    /**
     * @var string
     *
     * @ORM\Column(name="sv_value", type="text", nullable=false)
     */
    private $svValue;

    /**
     * Set userId
     *
     * @param integer $userId
     * @return TrackStoredValuesStack
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
     * Set scoId
     *
     * @param integer $scoId
     * @return TrackStoredValuesStack
     */
    public function setScoId($scoId)
    {
        $this->scoId = $scoId;

        return $this;
    }

    /**
     * Get scoId
     *
     * @return integer
     */
    public function getScoId()
    {
        return $this->scoId;
    }

    /**
     * Set stackOrder
     *
     * @param integer $stackOrder
     * @return TrackStoredValuesStack
     */
    public function setStackOrder($stackOrder)
    {
        $this->stackOrder = $stackOrder;

        return $this;
    }

    /**
     * Get stackOrder
     *
     * @return integer
     */
    public function getStackOrder()
    {
        return $this->stackOrder;
    }

    /**
     * Set courseId
     *
     * @param string $courseId
     * @return TrackStoredValuesStack
     */
    public function setCourseId($courseId)
    {
        $this->courseId = $courseId;

        return $this;
    }

    /**
     * Get courseId
     *
     * @return string
     */
    public function getCourseId()
    {
        return $this->courseId;
    }

    /**
     * Set svKey
     *
     * @param string $svKey
     * @return TrackStoredValuesStack
     */
    public function setSvKey($svKey)
    {
        $this->svKey = $svKey;

        return $this;
    }

    /**
     * Get svKey
     *
     * @return string
     */
    public function getSvKey()
    {
        return $this->svKey;
    }

    /**
     * Set svValue
     *
     * @param string $svValue
     * @return TrackStoredValuesStack
     */
    public function setSvValue($svValue)
    {
        $this->svValue = $svValue;

        return $this;
    }

    /**
     * Get svValue
     *
     * @return string
     */
    public function getSvValue()
    {
        return $this->svValue;
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
