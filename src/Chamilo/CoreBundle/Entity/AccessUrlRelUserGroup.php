<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccessUrlRelUser
 *
 * @ORM\Table(name="access_url_rel_usergroup")
 * @ORM\Entity
 */
class AccessUrlRelUserGroup
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
     * @ORM\Column(name="access_url_id", type="integer")

     */
    private $accessUrlId;

    /**
     * @var integer
     *
     * @ORM\Column(name="usergroup_id", type="integer")
     */
    private $userGroupId;


    /**
     * Set accessUrlId
     *
     * @param integer $accessUrlId
     * @return AccessUrlRelUser
     */
    public function setAccessUrlId($accessUrlId)
    {
        $this->accessUrlId = $accessUrlId;

        return $this;
    }

    /**
     * Get accessUrlId
     *
     * @return integer
     */
    public function getAccessUrlId()
    {
        return $this->accessUrlId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return AccessUrlRelUserGroup
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserGroupId()
    {
        return $this->userGroupId;
    }

    /**
     * @param int $userGroupId
     * @return AccessUrlRelUserGroup
     */
    public function setUserGroupId($userGroupId)
    {
        $this->userGroupId = $userGroupId;

        return $this;
    }
}
