<?php

namespace App\Controller;

use App\Form\HumeurDescriptionType;
use App\Repository\HumeurRepository;
use App\Repository\HumeurTypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(HumeurTypeRepository $humeurTypeRepository, HumeurRepository $humeurRepository): Response
    {
        $form = $this->createForm(HumeurDescriptionType::class);

        return $this->renderForm('index/index.html.twig', [
            //On retourne les types d'humeur qu'on trie par ASC selon le niveau d'humeur
            'humeurTypes' => $humeurTypeRepository->findBy([],['hapinessLevel'=>'ASC']),
            'humeurs' => $humeurRepository->findAll(),
            'humeurDescriptionForm' => $form
        ]);
    }
}
