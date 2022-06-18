<?php

namespace App\Controller;

use App\Entity\Humeur;
use App\Repository\HumeurRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class HumeurController extends AbstractController
{
    #[Route('/todaysMood', name: 'todaysMood')]
    public function index(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, HumeurRepository $humeurRepository): Response
    {
        //Si on veut créer une nouvelle Humeur, sinon on ignore car on a cliqué sur Next
        if($request->request->get('id') !== NULL)
        {
            //On récupère l'entité
            $humeurType = $entityManager->getReference('App\Entity\HumeurType',$data = $request->request->get('id'));
            //Si ça ne contient rien alors référence invalide
            if (!$entityManager->contains($humeurType)) {
                throw new \Exception("Invalid Reference");
            }
            $humeur = new Humeur();
            $humeur->setDescription($request->request->get('description'))
                ->setUser($this->getUser())
                ->setHumeurType($humeurType)
                ->setCreatedAt(new DateTimeImmutable('now'));

            $entityManager->persist($humeur);
            $entityManager->flush();
        }
    
        //On prépare les humeurs à retourner de l'année en cours
        $beginningOfYear = new \DateTimeImmutable('first day of January this year');
        $endOfYear = new \DateTimeImmutable('first day of January this year +1 years');

        $humeurs = $humeurRepository->findByUserAndYear($this->getUser(), $beginningOfYear, $endOfYear);
        

        $data = $serializer->serialize($humeurs, 'json', ['groups' => 'humeurG']);

        $response = new Response();
        $response->setContent($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
