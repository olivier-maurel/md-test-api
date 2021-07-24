<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\ApplicationService;
use App\Service\ApiDbService;

class ApplicationController extends AbstractController
{
    public function __construct(ApiDbService $adbS, ApplicationService $appS)
    {
        $this->adbS     = $adbS;
        $this->appS     = $appS;
    }

    /**
     * @Route("/", name="application")
     */
    public function index(): Response
    {
        $array = $this->adbS->getApiData();
        // dump($array); exit;
        // $this->adbS->generateJsonToCsv($array);
        $this->adbS->insertToDatabase($array);

        return $this->render('application/index.html.twig', [
            'controller_name' => 'ApplicationController',
        ]);
    }
}
