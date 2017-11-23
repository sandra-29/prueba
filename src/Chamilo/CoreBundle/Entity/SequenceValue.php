<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Sequence
 *
 * @ORM\Table(name="sequence_value")
 * @ORM\Entity
 */
class SequenceValue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="SequenceRowEntity")
     * @ORM\JoinColumn(name="sequence_row_entity_id", referencedColumnName="id")
     **/
    private $entity;

    /**
     * @var integer
     *
     * @ORM\Column(name="advance", type="float")
     */
    private $advance;

    /**
     * @var integer
     *
     * @ORM\Column(name="complete_items", type="integer")
     */
    private $completeItems;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_items", type="integer")
     */
    private $totalItems;

    /**
     * @var integer
     *
     * @ORM\Column(name="success", type="boolean")
     */
    private $success;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="success_date", type="datetime", nullable=true)
     */
    private $successDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="available", type="boolean")
     */
    private $available;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="available_start_date", type="datetime", nullable=true)
     */
    private $availableStartDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="available_end_date", type="datetime", nullable=true)
     */
    private $availableEndDate;

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
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return SequenceValue
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     *
     * @return SequenceValue
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return int
     */
    public function getAdvance()
    {
        return $this->advance;
    }

    /**
     * @param int $advance
     *
     * @return SequenceValue
     */
    public function setAdvance($advance)
    {
        $this->advance = $advance;

        return $this;
    }

    /**
     * @return int
     */
    public function getCompleteItems()
    {
        return $this->completeItems;
    }

    /**
     * @param int $completeItems
     *
     * @return SequenceValue
     */
    public function setCompleteItems($completeItems)
    {
        $this->completeItems = $completeItems;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }

    /**
     * @param int $totalItems
     *
     * @return SequenceValue
     */
    public function setTotalItems($totalItems)
    {
        $this->totalItems = $totalItems;

        return $this;
    }

    /**
     * @return int
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @param int $success
     *
     * @return SequenceValue
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSuccessDate()
    {
        return $this->successDate;
    }

    /**
     * @param \DateTime $successDate
     *
     * @return SequenceValue
     */
    public function setSuccessDate($successDate)
    {
        $this->successDate = $successDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * @param int $available
     *
     * @return SequenceValue
     */
    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAvailableStartDate()
    {
        return $this->availableStartDate;
    }

    /**
     * @param \DateTime $availableStartDate
     *
     * @return SequenceValue
     */
    public function setAvailableStartDate($availableStartDate)
    {
        $this->availableStartDate = $availableStartDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAvailableEndDate()
    {
        return $this->availableEndDate;
    }

    /**
     * @param \DateTime $availableEndDate
     *
     * @return SequenceValue
     */
    public function setAvailableEndDate($availableEndDate)
    {
        $this->availableEndDate = $availableEndDate;

        return $this;
    }


}
