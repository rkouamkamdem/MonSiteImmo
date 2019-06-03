<?php
/**
 * Created by PhpStorm.
 * User: romeo
 * Date: 03/05/19
 * Time: 15:29
 */

namespace App\Controller\Admin;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Property;
use App\Form\PropertyType;
use Symfony\Component\HttpFoundation\Response;

class AdminPropertyController extends AbstractController
{
    //Ci dessous avec le contructeur du controller, on peut injecter l'ObjectManager pour les Persist et les flush de mon entité
    /**
     * @var ObjectManager
     */
    private $em;

    //Ci dessous avec le contructeur du controller, on peut injecter le repository de son entité directement
    /**
     * @var PropertyRepository
     */
    private $repository;

    public function __construct(PropertyRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin.property.index")
     * @return Response
     */
    public function index() {
        $properties = $this->repository->findAll();
        return $this->render('admin/property/index.html.twig',compact('properties'));
    }


    /**
     * @Route("/admin/property/{id}", name="admin.property.edit", methods="GET|POST")
     * @return Response
     */
    public function edit(Property $property, Request $request) {

        //$property->get
        $formEdit = $this->createForm(PropertyType::class,$property);
        $formEdit->handleRequest($request);

        if( $formEdit->isSubmitted() && $formEdit->isValid() ){
            $this->em->flush();
            $this->addFlash('success','Le bien d\'identifiant ['.$property->getId().'] a été modifié  avec succès !!');
            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/edit.html.twig',[
            'property' => $property,
            'form' => $formEdit->createView()
        ]);
    }

    /**
     * @Route("/admin/property/create", name="admin.property.create")
     * @return Response
     */
    public function create(Request $request) {

        $property = new Property();
        $formCreate = $this->createForm(PropertyType::class,$property);
        $formCreate->handleRequest($request);

        if( $formCreate->isSubmitted() && $formCreate->isValid() ){
            //$formCreate->getData();
            $this->addFlash('success','Le bien a été créé  avec succès !!');
            $this->em->persist($property);
            $this->em->flush();
            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/new.html.twig',[
            'property' => $property,
            'form' => $formCreate->createView()
        ]);
    }

    //Attention ma methode DELETE n'existe pas pour les navigateurs,
    //Donc on va créer un mini formulaire pour pointer vers la suppression
    /**
     * @Route("/admin/property/{id}", name="admin.property.delete", methods="DELETE")
     * @return Response
     */
    public function delete(Property $property, Request $request) {

        //pour être sur de ne pas se faire pirater on verifie que le token lié au form de suppresion est valide
        if( $this->isCsrfTokenValid('delete'.$property->getId(),$request->request->get('_token')) ) {

            //$this->em->remove($property);
            //$this->em->flush();
            $this->addFlash('success','Le bien d\'identifiant ['.$property->getId().'] a été supprimée avec succès !!');
            return $this->redirectToRoute('admin.property.index');
            //return new Response('JE supprime !! ');
        }
        //if( empty($property) ) throw HttpException::class('Attention l\'objet à supprimer n\'existe pas !!! ');
            //$this->em->remove($property);
            //$this->em->flush();
            //return new Response('JE supprime !! ');
            //return $this->redirectToRoute('admin.property.index');
    }

}