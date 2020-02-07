<?php

namespace App\Controller;

use App\Entity\SearchProperty;
use App\Form\SearchPropertyType;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(PaginatorInterface $paginator, Request $request)
    {
        //Ici on utilise le 'PaginatorInterface' pour faire la pagination avec la fonction paginate()
        //En paramètre de la fonction paginate(), on met la requete qui est sensée renvoyée tous les biens,
        //Le Numéro de la page, et le nombre de biens à afficher par page.

        $pagination = $paginator->paginate(
            $this->repository->findAllVisible(), //query NOT result
            $request->query->getInt('page', 1), //page number
            12 //limit per page
        );

        //Création de l'entité qui va représenter notre recherche
        $searchProperty = new SearchProperty();

        //Créer un formulaire qui va contenir les champs relatifs à notre recherche
        $formSearchProperty = $this->createForm(SearchPropertyType::class);
        $formSearchProperty->handleRequest($request);

        if( $formSearchProperty->isSubmitted() && $formSearchProperty->isValid() ) {

            //On va chercher le prix maximal et le nbre de pièce minimum
            $dataForm = $formSearchProperty->getData();
//            dump($dataForm); die();
            //$searchProperty->setNbPiece();
           // $searchProperty->setPrix();
            $pagination = $paginator->paginate(
                $this->repository->searchAvailableProperty($dataForm->getPrix(),$dataForm->getNbPiece()), //query NOT result
                $request->query->getInt('page', 1), //page number
                12 //limit per page
            );

            /*
                SearchProperty {#976 ▼
                  -id: null
                  -prix: 10500
                  -nbPiece: 52
                }
            */
        }

        //On va gérer le traitement dans 1 controller


        //Je recupère l'ensemble de mes biens(Tous mes enregistrements properties)
        //$properties = $this->repository->findAll();
        $propertiesAvailable = $this->repository->findAllVisible();
        //$property[0]->setSold(true);$this->em->flush();dump($property);

        return $this->render('property/index.html.twig', [
            'controller_name' => 'PropertyController',
            //'property' => $property,
            'current_menu' => 'properties',
            'propertiesAvailable' => $pagination, //$propertiesAvailable,
            'pagination' => $pagination,
            'formSearchProperty' => $formSearchProperty->createView()
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
