<?php

namespace Blog\MainBundle\Controller;

use Blog\AdminBundle\Entity\Message;
use Blog\AdminBundle\Form\Type\MessageType;
use Blog\AdminBundle\Controller\MessageController;
use Blog\MainBundle\Entity\Record;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
    public function postAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('BlogAdminBundle:Post')->find($id);
        if (!$post) {
            return $this->render('BlogMainBundle:Default:notFound.html.twig');
        }
        $recentPost = $em->getRepository('BlogAdminBundle:Post')->findBy(
            array('deleted' => false),
            array('dateCreated' => 'DESC')
        );
        $messages = $post->getMessages();
        $entity = new Message;
        $form = $this->createForm(new MessageType(), $entity, array(
            'action' => $this->generateUrl('blog_main_post', array('id' => $id)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPost($post);
            $entity->setCreatedBy($this->getUser());
            $entity->setDateCreated(new \DateTime('now'));
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('blog_main_post', array( 'id' => $id )));
        }

        return $this->render('BlogMainBundle:Post:post.html.twig', array(
            'post' => $post,
            'messages' => $messages,
            'form' => $form->createView(),
            'recentPost' => $recentPost,
        ));
    }

    public function genreAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $genre = $em->getRepository('BlogAdminBundle:Tag')->find($id);
        if (!$genre) {
            return $this->render('BlogMainBundle:Default:notFound.html.twig');
        }
        $recentPost = $em->getRepository('BlogAdminBundle:Post')->findBy(
            array('deleted' => false),
            array('dateCreated' => 'DESC')
        );
        return $this->render('BlogMainBundle:Post:genre.html.twig', array(
            'genre' => $genre,
            'posts' => $genre->getPosts(),
            'recentPost' => $recentPost,
        ));
    }


}