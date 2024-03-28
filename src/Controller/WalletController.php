<?php

namespace App\Controller;

use App\Entity\Wallet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WalletController extends AbstractController
{
    #[Route('/wallets/create', name: 'post_wallet', methods: 'POST')]
    public function createWallet(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $wallet = new Wallet();
        $wallet->setTitle($data['title']);
        $wallet->setOwner($data['owner']);
        $wallet->setAssets($data['assets']);
        $wallet->setUpdatedAt(new \DateTime());
        $wallet->setCreatedAt(new \DateTime());
        $wallet->setValue($data['value']);

        $entityManager->persist($wallet);
        $entityManager->flush();

        return new Response('Wallet created!', Response::HTTP_CREATED);
    }

    #[Route('/wallets/{id}', name: 'get_id_wallet', methods: ['GET'])]
    public function getByIdWallet($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $wallet = $entityManager->getRepository(Wallet::class)->find($id);
        if (!$wallet) {
            return new JsonResponse(['error' => 'Wallet not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $wallet->getId(),
            'title' => $wallet->getTitle(),
            'owner' => $wallet->getOwner(),
            'assets' => $wallet->getAssets(),
            'value' => $wallet->getValue(),
            'createdAt' => $wallet->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $wallet->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/wallets', name: 'get_wallets', methods: ['GET'])]
    public function getWallets(EntityManagerInterface $entityManager): JsonResponse
    {
        $wallets = $entityManager->getRepository(Wallet::class)->findAll();

        if (!$wallets) {
            return new JsonResponse(['error' => 'Wallets not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [];
        foreach ($wallets as $wallet) {
            $data[] = [
                'id' => $wallet->getId(),
                'title' => $wallet->getTitle(),
                'owner' => $wallet->getOwner(),
                'assets' => $wallet->getAssets(),
                'value' => $wallet->getValue(),
                'createdAt' => $wallet->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $wallet->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/wallets/{id}', name: 'put_wallet', methods: ['PUT'])]
    public function putWallet($id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $wallet = $entityManager->getRepository(Wallet::class)->find($id);

        if (!$wallet) {
            return new JsonResponse(['error' => 'Wallet not found'], Response::HTTP_NOT_FOUND);
        }

        $wallet->setTitle($data['title'] ?? $wallet->get());
        $wallet->setOwner($data['owner'] ?? $wallet->getOwner());
        $wallet->setAssets($data['assets'] ?? $wallet->getAssets());
        $wallet->setValue($data['value'] ?? $wallet->getValue());
        $wallet->setUpdatedAt(new \DateTime());

        $entityManager->flush();

        return new JsonResponse(['message' => 'Wallet updated successfully'], Response::HTTP_OK);
    }

    #[Route('/wallets/{id}', name: 'delete_wallet', methods: ['DELETE'])]
    public function deleteWallet($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $wallet = $entityManager->getRepository(Wallet::class)->find($id);

        if (!$wallet) {
            return new JsonResponse(['error' => 'Wallet not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($wallet);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Wallet deleted successfully'], Response::HTTP_OK);
    }
}
