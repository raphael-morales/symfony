<?php
// src/Controller/WildController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{

    /**
     * @Route("/wild", name="wild_index")
     */
    public function index() :Response
    {
        return $this->render('wild/index.html.twig', [
                'website' => 'Wild Séries',
        ]);
    }

    /**
     * @Route("/wild/show/{slug<[a-z0-9-]{1,}>}", name="wild_show")
     */
    public function show(string $slug) :Response
    {
        $slug = ucwords(str_replace('-', ' ', $slug));
        return $this->render('wild/show.html.twig', ['slug' => $slug]);
    }

    /**
     * @Route("/wild/show", name="wild_noshow")
     */
    public function noShow() :Response
    {
        return $this->render('wild/show.html.twig', ['slug' => 'Aucune série sélectionnée, veuillez choisir une série']);
    }

}
