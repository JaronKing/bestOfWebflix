<?php

namespace Blog\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Blog\AdminBundle\Entity\Post;
use Blog\AdminBundle\Form\Type\PostType;

/**
 * Post controller.
 *
 */
class PostController extends Controller
{

    /**
     * Lists all Post entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BlogAdminBundle:Post')->findAll();

        return $this->render('BlogAdminBundle:Post:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Post entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Post();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedBy($this->getUser());
            $entity->setDateCreated(new \DateTime('now'));
            $entity->setLastEdited(new \DateTime('now'));
            $entity->setDeleted(false);
            $entity->setEnabled(true);
            $entity->upload();
            if (null != $entity->getAbsolutePath()) {
                $logger = $this->get('logger');
                $bodyText = $this->handleCSV($em, $entity);
                $body = $entity->getBody();
                $body = $body . $bodyText;
                $entity->setBody($body);
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('post_edit', array('id' => $entity->getId())));
        }

        return $this->render('BlogAdminBundle:Post:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    private function handleCSV($em, $entity) {
        $bodyText = "";
        $file_handle = fopen($entity->getAbsolutePath(), "r");
        while (!feof($file_handle)) {
            $col = fgetcsv($file_handle, 1024);
            if ($col[0] == null) break;
            $bodyString = "<p><a href=\"$col[1]\" target=\"_blank\"><img src=\"$col[2]\" style=\"width: 267px; height: 374.176px;\" class=\"img-thumbnail\"></a></p><p><span style=\"font-family: arial, sans, sans-serif; font-size: 24px; line-height: 24px; white-space: pre-wrap; text-decoration: underline;\"><a href=\"$col[1]\" target=\"_blank\">$col[0]</a></span></p><p><span style=\"font-family: arial, sans, sans-serif; font-size: 18px; line-height: 18px; white-space: pre-wrap;\">$col[3]</span><br></p>";
            $bodyText = $bodyText . $bodyString;
        }

        return $bodyText;
    }

    /**
     * Creates a form to create a Post entity.
     *
     * @param Post $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Post $entity)
    {
        $form = $this->createForm(new PostType(), $entity, array(
            'action' => $this->generateUrl('post_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Post entity.
     *
     */
    public function newAction()
    {
        $entity = new Post();
        $form   = $this->createCreateForm($entity);

        return $this->render('BlogAdminBundle:Post:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Post entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogAdminBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BlogAdminBundle:Post:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogAdminBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BlogAdminBundle:Post:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Post entity.
    *
    * @param Post $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Post $entity)
    {
        $form = $this->createForm(new PostType(), $entity, array(
            'action' => $this->generateUrl('post_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Post entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogAdminBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setUpdateAt(new \DateTime('now'));
            if ($editForm->get('image')->getData()) {
                $file = $entity->getImage();
                $fileName = sha1(uniqid(mt_rand(), true). '.' .$file->guessExtension());
                $filePath = $this->container->getParameter('kernel.root_dir').'/../web/uploads/images/';
                $entityPath = '/uploads/images/'.$fileName;
                $file->move($filePath,$fileName);
                $entity->setImagePath($entityPath);
                $entity->setImage($file);
            }
            $entity->upload();
            if (null != $entity->getAbsolutePath()) {
                $logger = $this->get('logger');
                $bodyText = $this->handleCSV($em, $entity);
                $body = $entity->getBody();
                $body = $body . $bodyText;
                $entity->setBody($body);
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('post_edit', array('id' => $id)));
        }

        return $this->render('BlogAdminBundle:Post:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Post entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BlogAdminBundle:Post')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Post entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('post'));
    }

    /**
     * Creates a form to delete a Post entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
