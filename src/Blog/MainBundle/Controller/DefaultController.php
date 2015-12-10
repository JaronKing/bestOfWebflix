<?php

namespace Blog\MainBundle\Controller;

use Blog\AdminBundle\Entity\Message;
use Blog\AdminBundle\Form\Type\MessageType;
use Blog\AdminBundle\Controller\MessageController;
use Blog\MainBundle\Entity\Record;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('BlogAdminBundle:Post')->findPostsByPage($page,48);
        $genres = $em->getRepository('BlogAdminBundle:Tag')->findAll();
        return $this->render('BlogMainBundle:Default:index.html.twig', array(
            'genres' => $genres,
            'posts' => $posts,
            'currentPage' => $page
        ));
    }

    public function sitemapAction(){
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('BlogAdminBundle:Post')->findAll();
        return $this->render('BlogMainBundle:Default:sitemap.xml.twig', array(
            'posts' => $posts,
            'hostname' => $this->getRequest()->getHost(),
        ));
    }

    public function navBarTopAction()
    {
        $em = $this->getDoctrine()->getManager();
        $genres = $em->getRepository('BlogAdminBundle:Tag')->findAll();
        return $this->render('BlogMainBundle:Default:navBarTop.html.twig', array(
            'genres' => $genres,
        ));
    }

    public function genresAction()
    {
        $em = $this->getDoctrine()->getManager();
        $genres = $em->getRepository('BlogAdminBundle:Tag')->findAll();
        return $this->render('BlogMainBundle:Default:genres.html.twig', array(
            'genres' => $genres,
        ));
    }

    public function browseAction()
    {
        $em = $this->getDoctrine()->getManager();
        $genres = $em->getRepository('BlogAdminBundle:Tag')->findAll();
        return $this->render('BlogMainBundle:Default:browse.html.twig', array(
            'genres' => $genres,
        ));
    }

    public function footerAction()
    {
        $settings = $this->getSettings();
        return $this->render('BlogMainBundle:Default:footer.html.twig', array(
            'settings' => $settings
        ));
    }

    public function sidebarAboutAction()
    {
        $settings = $this->getSettings();
        return $this->render('BlogMainBundle:Default:sidebar.html.twig', array(
            'settings' => $settings
        ));
    }

    public function mainMetaTagAction()
    {
        $settings = $this->getSettings();
        return $this->render('BlogMainBundle:Default:meta.html.twig', array(
            'settings' => $settings
        ));
    }

    public function aboutAction()
    {
        $settings = $this->getSettings();
        return $this->render('BlogMainBundle:Default:about.html.twig', array(
            'settings' => $settings
        ));
    }

    public function socialLinksAction()
    {
        $entity = $this->getSettings();
        return $this->render('BlogMainBundle:Default:socialLinks.html.twig', array(
            'entity' => $entity,
        ));
    }

    private function getSettings()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BlogAdminBundle:Settings')->findOneBy(array('id' => 1));
        if (!$entity) {
            $entity = null;
        }
        return $entity;
    }

    public function headerAction()
    {
        $entity = $this->getSettings();
        return $this->render('BlogMainBundle:Default:header.html.twig', array(
            'settings' => $entity,
        ));
    }
}
