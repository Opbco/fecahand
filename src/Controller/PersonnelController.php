<?php

namespace App\Controller;

use App\Entity\Personnel;
use App\Repository\PersonnelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class PersonnelController extends AbstractController
{
    #[Route('api/personnels', name: 'app_personnel', methods: ['GET'])]
    public function index(PersonnelRepository $personnelRepository, SerializerInterface $serializer): JsonResponse
    {
        $personnels = $personnelRepository->findAll();

        return new JsonResponse($serializer->serialize($personnels, 'json', ['groups' => 'getPersonnels']), Response::HTTP_OK, [], true);
    }

    #[Route('api/personnels/{id}', name: 'app_personnel_details', methods: ['GET'])]
    public function getPersonnel(Personnel $personnel, SerializerInterface $serializer): JsonResponse
    {

        if ($personnel) {
            return new JsonResponse($serializer->serialize($personnel, 'json'), Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('api/personnels/{id}', name: 'app_personnel_delete', methods: ['DELETE'])]
    public function deletePersonnel(Personnel $personnel, EntityManagerInterface $em): JsonResponse
    {

        $em->remove($personnel);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('api/personnels', name: 'app_personnel_create', methods: ['POST'])]
    public function createPersonnel(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $personnel = $serializer->deserialize($request->getContent(), Personnel::class, 'json');

        $em->persist($personnel);
        $em->flush();

        $jsonPersonnel = $serializer->serialize($personnel, 'json', ['groups' => 'getPersonnels']);

        $location = $urlGenerator->generate('app_personnel_details', ['id' => $personnel->getId()], UrlGeneratorInterface::ABSOLUTE_URL);


        return new JsonResponse($jsonPersonnel, Response::HTTP_CREATED, ['location' => $location], true);
    }

    #[Route('api/personnels/{id}', name: 'app_personnel_update', methods: ['PUT'])]
    public function updatePersonnel(Request $request, SerializerInterface $serializer, Personnel $personnel, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatedPersonnel = $serializer->deserialize($request->getContent(), Personnel::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $personnel]);
        $em->persist($updatedPersonnel);
        $em->flush();
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
