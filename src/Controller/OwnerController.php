<?php

namespace App\Controller;

use App\Entity\Owner;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class OwnerController extends AbstractController
{
    #[Route('/owners', name: 'get_owner', methods: 'GET')]
    public function getOwners(EntityManagerInterface $entityManager): JsonResponse
    {
        $owners = $entityManager->getRepository(Owner::class)->findAll();

        if (!$owners)
        {
            return new JsonResponse(['error' => 'Owners not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [];
        foreach ($owners as $owner)
        {
            $wallets = [];
            foreach ($owner->getWalletList() as $wallet)
            {
                $wallets[] = [
                    'id' => $wallet->getId(),
                    'title' => $wallet->getTitle(),
                    'assets' => $wallet->getAssets(),
                    'value' => $wallet->getValue(),
                    'createdAt' => $wallet->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $wallet->getUpdatedAt()->format('Y-m-d H:i:s'),
                ];
            }

            $data[] = [
                'id' => $owner->getId(),
                'name' => $owner->getName(),
                'age' => $owner->getAge(),
                'active' => $owner->isActive(),
                'wallets' => $wallets
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }


    #[Route('/owner/{id}', name: 'get_id_owner', methods: ['GET'])]
    public function getByIDowner($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $owner = $entityManager->getRepository(Owner::class)->find($id);

        if (!$owner)
        {
            return new JsonResponse(['error' => 'Owner not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $owner->getId(),
            'name' => $owner->getName(),
            'age' => $owner->getAge(),
            'active' => $owner->isActive(),
            'wallets' => $owner->getWalletList()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/owner/create', name: 'post_owner', methods: 'POST')]
    public function createOwner(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $owner = new Owner();

        $owner->setName($data['name']);
        $owner->setAge($data['age']);
        $owner->setActive($data['active']);

        if (!isset($data['name']) || !isset($data['age']) || !isset($data['active']))
        {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($owner);
        $entityManager->flush();

        return new Response('Owner created!', Response::HTTP_CREATED);
    }

    #[Route('/owner/{id}', name: 'put_owner', methods: ['PUT'])]
    public function putOwner($id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $owner = $entityManager->getRepository(Owner::class)->find($id);

        if (!$owner)
        {
            return new JsonResponse(['error' => 'Owner not found'], Response::HTTP_NOT_FOUND);
        }

        $owner->setName($data['name'] ?? $owner->getName());
        $owner->setAge($data['age'] ?? $owner->getAge());
        $owner->setActive($data['active'] ?? $owner->isActive());

        $entityManager->flush();

        return new JsonResponse(['Message' => 'Owner updated!'], Response::HTTP_OK);
    }

    #[Route('/owner/{id}', name: 'delete_owner', methods: ['DELETE'])]
    public function deleteOwner($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $owner = $entityManager->getRepository(Owner::class)->find($id);

        if (!$owner)
        {
            return new JsonResponse(['error' => 'Owner not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($owner);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Owner deleted'], Response::HTTP_OK);
    }
}
