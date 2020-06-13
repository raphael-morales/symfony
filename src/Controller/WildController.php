<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/wild")
 */
class WildController extends AbstractController
{

    /**
     * @Route("/", name="wild_index")
     */
    public function index() :Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render('wild/index.html.twig', [
                'website' => 'Wild Séries',
                'programs' => $programs
        ]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="wild_show")
     * @return Response
     */
    public function show(?string $slug):Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }
        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);
    }

    /**
     * @Route("/show", name="wild_noshow")
     */
    public function noShow() :Response
    {
        return $this->render('wild/show.html.twig', ['slug' => 'Aucune série sélectionnée, veuillez choisir une série']);
    }

    /**
     * @param string $categoryName
     * @Route ("/category/{categoryName}", name="show_category")
     */
    public function showByCategory(string $categoryName) :Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category],
                     ['id' => 'asc'],
                     3);

        return $this->render('wild/category.html.twig', [
            'programs' => $program
        ]);
    }

    /**
     * @param string $programName
     * @Route ("/{programName}", name="show_program")
     */
    public function showByProgram(string $programName) :Response
    {
        $programName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($programName)), "-")
        );

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => $programName]);

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program]);

        return $this->render('wild/program.html.twig', [
            'seasons' => $season,
            'program' => $program
        ]);
    }

    /**
     * @param int $id
     * @param string $programName
     * @Route ("/{programName}/{id}", name="wild_season")
     * @return Response
     */
    public function showBySeason(int $id, string $programName) :Response
    {
        $programName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($programName)), "-")
        );

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $id]);

        $program = $season->getProgram();
        $episodes = $season->getEpisodes();

        return $this->render('wild/season.html.twig', ['episodes' => $episodes,
            'program' => $program,
            'season' => $season
        ]);
    }

    /**
     * @param Episode $episode
     * @Route ("/{programName}/{season}/episode/{episode}", name="wild_episode")
     * @return Response
     */
    public function showEpisode(Episode $episode) :Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/episode.html.twig', ['episode'=> $episode,
                                                        'program' => $program,
                                                        'season' => $season]);
    }

}
