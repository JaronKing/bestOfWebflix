<?php

namespace Blog\MainBundle\Controller;

use Blog\AdminBundle\Entity\ContactUs;
use Blog\AdminBundle\Form\ContactUsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ContactUsController extends Controller
{
    public function newAction()
    {

        return $this->render('BlogMainBundle:ContactUs:new.html.twig', array(
                // ...
            ));    }

    public function showAction(Request $request)
    {
        $entity = new ContactUs();
        $form = $this->createForm(new ContactUsType(), $entity, array(
            'action' => $this->generateUrl('blog_main_contact_us_show'),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array(
            'label' => 'Submit Message',
            'attr' => array( 'class' => 'btn btn-primary backcolor')
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $entity->setDateCreated(new \DateTime('now'));
            $entity->setSeen(False);
            $entity->setDeleted(False);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('blog_main_homepage'));
        }
        return $this->render('BlogMainBundle:ContactUs:show.html.twig', array(
            'form' => $form->createView()
        ));    }

}
