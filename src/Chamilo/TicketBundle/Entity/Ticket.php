<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\TicketBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\Session;
use Chamilo\TicketBundle\Entity\Project;
use Chamilo\TicketBundle\Entity\Priority;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket_ticket")
 * @ORM\Entity
 */
class Ticket
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=false)
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    protected $message;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Chamilo\TicketBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     **/
    protected $project;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Chamilo\TicketBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     **/
    protected $category;

    /**
     * @var Priority
     *
     * @ORM\ManyToOne(targetEntity="Chamilo\TicketBundle\Entity\Priority")
     * @ORM\JoinColumn(name="priority_id", referencedColumnName="id")
     **/
    protected $priority;

    /**
     * @var Course
     *
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Course")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     **/
    protected $course;

    /**
     * @var Session
     *
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Session")
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id")
     **/
    protected $session;

    /**
     * @var string
     *
     * @ORM\Column(name="personal_email", type="string", length=255, nullable=false)
     */
    protected $personalEmail;

    /**
     * @var integer
     *
     * @ORM\Column(name="assigned_last_user", type="integer", nullable=true)
     */
    protected $assignedLastUser;

     /**
     * @var Status
     *
     * @ORM\ManyToOne(targetEntity="Chamilo\TicketBundle\Entity\Status")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     **/
    protected $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_messages", type="integer", nullable=false)
     */
    protected $totalMessages;

    /**
     * @var string
     *
     * @ORM\Column(name="keyword", type="string", length=255, nullable=true)
     */
    protected $keyword;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=255, nullable=true)
     */
    protected $source;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true, unique=false)
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true, unique=false)
     */
    protected $endDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="sys_insert_user_id", type="integer", nullable=false, unique=false)
     */
    protected $insertUserId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sys_insert_datetime", type="datetime", nullable=false, unique=false)
     */
    protected $insertDateTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="sys_lastedit_user_id", type="integer", nullable=true, unique=false)
     */
    protected $lastEditUserId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sys_lastedit_datetime", type="datetime", nullable=true, unique=false)
     */
    protected $lastEditDateTime;

}
