<?php
/**
 * Created by PhpStorm.
 * User: romeo
 * Date: 23/04/19
 * Time: 22:22
 */

namespace App\Controller;

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//require __DIR__ . '/vendor/autoload.php';

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
        //$this->register();
        //echo "<br/>-------------------- Mon DIR  ---------------<br/>";echo __DIR__;die();

        $this->uploadPortalZip();
        return new Response($this->twig->render('pages/home.html.twig',[
            'latestProperties' => $latestProperties
        ]));

    }

    public function uploadPortalZip(){

        $guzzle = new \GuzzleHttp\Client([
            'base_uri' => "https://sphere.nomosphere.fr/api/",
            'cookies'=> true,
            'exceptions' => false
        ]);


        //$arrayFiles = scandir("/home/romeo/Documents/DOCS_NOMOSPHERE/ENCAPTO/PORTAL_ZIP");
        //dump($arrayFiles); die();

        $directory = '/home/romeo/Documents/DOCS_NOMOSPHERE/ENCAPTO/PORTAL_ZIP';
        if($dossier = opendir($directory)){
            $cpt=0;

            while( false !== ($fichier = readdir($dossier))){

                if( $fichier != '.' && $fichier != '..' ) {
                    $cpt++;
                    $portal_name = "NSPH-VINCI-TEST_ZIP_".$cpt;
                    echo "Fichier traité N°:".$cpt." ==> ".$fichier."<br/>";
                    $portal_file_name = $directory."/".$fichier;

                    $response = $guzzle->request(
                        "POST",
                        "portal-groups",
                        [
                            "json" => [
                                "name" => $portal_name,
                                "description" => $portal_name
                            ],
                            'headers' => [
                                'Accept'     => 'application/json',
                                //'Content-Type' => 'multipart/form-data',
                                'X-API-Key' => '93fb01e0-2907-4728-9ceb-90d50da949cc',
                            ]
                        ]
                    );

                    //dump($response->getBody());

                    $body = json_decode((string) $response->getBody(), true);
                    //dump($body);
                    //die();
// if fail, print body and check error message
                    $portal_group_id = $body['result']['portal_group_id'];

                    $response = $guzzle->request(
                        "POST",
                        "portal-groups/".$portal_group_id."/portals",
                        [
                            "multipart" => [
                                [
                                    "name" => "translation_id",
                                    "contents" => 2 // id 2  is french
                                ],
                                [
                                    "name" => "type",
                                    "contents" => "custom"
                                ],
                                [
                                    "name" => "theme",
                                    "contents" => '{"background_type":"RWP","brand_primary":"#0F8FDF","section_background":"#CCCCCC","content_background":"#FFFFFF","footer_background":"#FFFFFF","links_color":"#337AB7","headings_color":"#000000","navbar_background":"#FFFFFF","border_radius":0,"navbar_background_opacity":100,"section_item_background_opacity":100,"footer_background_opacity":100,"has_section_item_border":false,"has_section_item_spacing":true,"translate":"none"}'
                                ],
                                [
                                    "name" => "custom_template",
                                    "contents" => fopen($portal_file_name,"r"),//fopen(__DIR__."/portals/{$portal_file_name}","r"),
                                    "headers" => [
                                        "Content-Type" => "application/zip",
                                    ]
                                ]
                            ],

                            'headers' => [
                                'Accept'     => 'application/json',
                                //'Content-Type' => 'multipart/form-data',
                                'X-API-Key' => '93fb01e0-2907-4728-9ceb-90d50da949cc',
                            ]
                        ]
                    );

                    //dump($response);
                }
            }
        } die('------------ Fin du script de création de portail Zippé par lot --------------');

    /*
        $portal_name = "NSPH-VINCI-NANTES-ZIP-TEST";
        $portal_file_name = __DIR__."/VinciNantesCustom.zip";

        $response = $guzzle->request(
            "POST",
            "portal-groups",
            [
                "json" => [
                    "name" => $portal_name,
                    "description" => $portal_name
                ],
                'headers' => [
                    'Accept'     => 'application/json',
                    //'Content-Type' => 'multipart/form-data',
                    'X-API-Key' => '93fb01e0-2907-4728-9ceb-90d50da949cc',
                ]
            ]
        );

        dump($response->getBody());

        $body = json_decode((string) $response->getBody(), true);
        dump($body);
        //die();
        // if fail, print body and check error message
        $portal_group_id = $body['result']['portal_group_id'];

        $response = $guzzle->request(
            "POST",
            "portal-groups/".$portal_group_id."/portals",
            [
                "multipart" => [
                    [
                        "name" => "translation_id",
                        "contents" => 2 // id 2  is french
                    ],
                    [
                        "name" => "type",
                        "contents" => "custom"
                    ],
                    [
                        "name" => "theme",
                        "contents" => '{"background_type":"RWP","brand_primary":"#0F8FDF","section_background":"#CCCCCC","content_background":"#FFFFFF","footer_background":"#FFFFFF","links_color":"#337AB7","headings_color":"#000000","navbar_background":"#FFFFFF","border_radius":0,"navbar_background_opacity":100,"section_item_background_opacity":100,"footer_background_opacity":100,"has_section_item_border":false,"has_section_item_spacing":true,"translate":"none"}'
                    ],
                    [
                        "name" => "custom_template",
                        "contents" => fopen($portal_file_name,"r"),//fopen(__DIR__."/portals/{$portal_file_name}","r"),
                        "headers" => [
                            "Content-Type" => "application/zip",
                        ]
                    ]
                ],

                'headers' => [
                    'Accept'     => 'application/json',
                    //'Content-Type' => 'multipart/form-data',
                    'X-API-Key' => '93fb01e0-2907-4728-9ceb-90d50da949cc',
                ]
            ]
        );

        dump($response);
        die();
    */
    }


    /*
    public function register(
        $civilite = null,
        $prenom = null,
        $nom = null,
        $email = null,
        $tel = null,
        $adresse = null,
        $zipcode = null,
        $ville = null,
        $pays = null,
        $dateNaissance = null
    ) {
        $data = array(
            'type' => "enregistrement",
            'pe' => "100197600",
            'areaId' => "399",
            'macAddress' => "sgs547654747",
            'detail' => array(
                'civilite' => "M",
                'prenom' => "romeo",
                'nom' => "koumfgfalo",
                'email' =>  "rkouamgfy@nomosphere.fr",
               'telephone' => "0676214014",
               'adresse' => null,//"78 rue du Bonheur 78370 Plaisir",
               'cp' => null,//"75018",
               'ville' => null,//"Plaisir",
               'pays' => null,//"france",
               'date_naissance' => null//"1978-08-15"
           )
       );

        return $this->call($data);
    }

    public function checkPresence()
    {
        $data = array(
            'type' => "connexion",
            'pe' => $this->passEtape,
            'macAddress' => $this->userMac,
            'areaId' => $this->areaId,
        );

        return $this->call($data);
    }

    private function call($data)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_URL, "https://api.campingcarpark.com/wifi/");//$this->ccpUrl
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $replyCCP = (string)curl_exec($curl);
        var_dump($data); var_dump(json_encode($data));
        var_dump($replyCCP);
        die();
        Prado::log(
            'message : ' . prado::vardump($data) . 'reply : ' . $replyCCP,
            TLogger::INFO,
            "Portail.CCP"
        );
        return $replyCCP === "1";
    }
    */

}