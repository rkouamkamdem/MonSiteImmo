<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Property;
use Symfony\Component\HttpFoundation\Response;

class PropertyController extends AbstractController
{

    //Ci-dessous avec le contructeur du controller, on peut injecter l'ObjectManager pour les Persist et les flush de mon entité
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
     * @Route("/biens", name="property.index")
     * @return Response
     */
    public function index()
    {
        /*
        $property = new Property();
        $property->setTitle("Mon premier bien");
        $property->setDescription("Description de mon premier Appartement");
        $property->setPrice(200000);
        $property->setBedrooms(4);
        $property->setSurface(80);
        $property->setFloor(2);
        $property->setAddress("23 Rue Labat");
        $property->setPostalCode("75018");
        $property->setCity("Paris");
        $property->setSold(false);
        //On ne met pas de setteur sur la date car à chaque instanciation de l'entité
       // $property->setCreatedAt(\DateTime('now'));
        $property->setHeat(50);
        $property->setRooms(5);

        $em = $this->getDoctrine()->getManager();
        $em->persist($property);
        $em->flush(); */

        //Ici on va chercher le repo de son Entité, mais on peut aussi l'injecter par le controller
        // $repository = $this->getDoctrine()->getRepository(Property::class);
        // dump($repository);

        //Je recupère l'ensemble de mes biens(Tous mes enregistrements properties)
        //$properties = $this->repository->findAll();
        $property = $this->repository->findAllVisible();
        $property[0]->setSold(true);
        $this->em->flush();
        dump($property);

        return $this->render('property/index.html.twig', [
            'controller_name' => 'PropertyController',
            'property' => $property,
            'current_menu' => 'properties'
        ]);
    }

    /**
     * @Route("/biens/{slug}/{id}", name="property.show" , requirements={ "slug": "[a-z0-9\_]*" } )
     * @return Response
     */
    //Synthaxe basique: public function show($slug,$id)
    public function show(Property $property, string $slug)
    {
        //Cette façon de faire est très bien pour le référencement
        if( $property->getSlug() !== $slug ) {
            return $this->redirectToRoute('property.show',[
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ],301);
        }

        //Je recupère un bien en particulier
        //Synthaxe basique: $property = $this->repository->find($id);

        return $this->render('property/show.html.twig', [
            'controller_name' => 'PropertyController',
            'property' => $property,
           'current_menu' => 'properties'
        ]);
    }

}
