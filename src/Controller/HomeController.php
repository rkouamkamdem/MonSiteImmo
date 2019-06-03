<?php
/**
 * Created by PhpStorm.
 * User: romeo
 * Date: 23/04/19
 * Time: 22:22
 */

namespace App\Controller;

use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /*
    * @var Environment
    */
    private $twig;

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

    /**
     * HomeController constructor.
     * @param $twig
     * @param PropertyRepository $repository
     * @param ObjectManager $em
     */
    public function __construct($twig, PropertyRepository $repository, ObjectManager $em)
    {
        $this->twig = $twig;
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @return Response
     */
    public function index()
    {
        $latestProperties = $this->repository->findLatest();
        //return new Response('Salut mon vieux Dev!!');
        return new Response($this->twig->render('pages/home.html.twig',[
            'latestProperties' => $latestProperties
        ]));
    }
}