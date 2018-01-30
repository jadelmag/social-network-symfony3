<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use BackendBundle\Entity\Publication;
use AppBundle\Form\PublicationType;

class PublicationController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function indexAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // upload image
                $file = $form['image']->getData();
                if (!empty($file) && $file != null) {
                    $ext = $file->guessExtension();
                    if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
                        $file_name = $user->getId() . time() . '.' . $ext;
                        $file->move('uploads/publications/images', $file_name);

                        $publication->setImage($file_name);
                    } else {
                        $publication->setImage(null);
                    }
                } else {
                    $publication->setImage(null);
                }

                // upload document
                $doc = $form['document']->getData();
                if (!empty($doc) && $doc != null) {
                    $ext = $doc->guessExtension();
                    if ($ext == 'pdf') {
                        $doc_name = $user->getId() . time() . '.' . $ext;
                        $doc->move('uploads/publications/documents', $doc_name);

                        $publication->setDocument($doc_name);
                    } else {
                        $publication->setDocument(null);
                    }
                } else {
                    $publication->setDocument(null);
                }

                $publication->setUser($user);
                $publication->setCreatedAt(new \DateTime('now'));

                $em->persist($publication);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = 'La publicación se ha creado correctamente';
                } else {
                    $status = 'Error al añadir la publicación';
                }
            } else {
                $status = 'La publicación no se ha creado, porque el formulario no es válido';
            }

            $this->session->getFlashBag()->add('status', $status);
            return $this->redirectToRoute('home_publications');
        }

        $publications = $this->getPublications($request);

        return $this->render('AppBundle:Publication:home.html.twig', array(
                    'form' => $form->createView(),
                    'pagination' => $publications
        ));
    }

    public function getPublications(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $user_id = $user->getId();
        $publications_repo = $em->getRepository('BackendBundle:Publication');
        $following_repo = $em->getRepository('BackendBundle:Following');

        $following = $following_repo->findBy(array('user' => $user));
        $following_array = array();
        foreach ($following as $follow) {
            $following_array[] = $follow->getFollowed();
        }
        
        $query = $publications_repo->createQueryBuilder('p')
                ->where('p.user = (:user_id) OR p.user IN (:following)')
                ->setParameter('user_id', $user_id)
                ->setParameter('following', $following_array)
                ->orderBy('p.id', 'DESC')
                ->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );
        return $pagination;
    }

    public function removeAction(Request $request, $id = null) {


        $em = $this->getDoctrine()->getManager();
        $publication_repo = $em->getRepository('BackendBundle:Publication');
        $publication = $publication_repo->find($id);
        $user = $this->getUser();

        if ($publication == null) {
            return $this->redirectToRoute('home_publications');
        } else {

            if ($user->getId() == $publication->getUser()->getId()) {
                $em->remove($publication);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = 'La publicación se ha borrado correctamente';
                } else {
                    $status = 'La publicación no se ha borrado';
                }
            } else {
                $status = 'No tiene permiso para eliminar la publicación';
            }

            return new Response($status);
        }
    }

}