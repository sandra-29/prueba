<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\ThemeBundle\Controller;

use Chamilo\ThemeBundle\Form\FormDemoModelType;
use Chamilo\ThemeBundle\Model\FormDemoModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class DefaultController
 *
 * @package Chamilo\ThemeBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardAction() {
        return    $this->render('ChamiloThemeBundle:Default:index.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uiGeneralAction() {
        return $this->render('ChamiloThemeBundle:Default:index.html.twig');
    }

    public function uiIconsAction() {
        return $this->render('ChamiloThemeBundle:Default:index.html.twig');
    }

    public function formAction() {
        $form =$this->createForm( new FormDemoModelType());
        return $this->render('ChamiloThemeBundle:Default:form.html.twig', array(
                'form' => $form->createView()
            ));
    }
}
