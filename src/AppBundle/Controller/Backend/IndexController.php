<?php
namespace AppBundle\Controller\Backend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
  /**
   * @Route("/admin", name="admin_homepage")
   * @Security("has_role('SUPER-ADMIN')")
   */
  public function indexAction()
  {
    return $this->render('backend/index.html.twig');
  }
}