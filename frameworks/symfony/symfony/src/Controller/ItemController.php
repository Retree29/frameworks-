<?php

namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/items')]
class ItemController extends AbstractController
{
    #[Route('', name: 'api_items_index', methods: ['GET'])]
    public function index(ItemRepository $itemRepository): JsonResponse
    {
        $items = $itemRepository->findAll();
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'description' => $item->getDescription()
            ];
        }
        return $this->json($data);
    }

    #[Route('', name: 'api_items_store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        
        if (empty($content['name'])) {
            return $this->json(['message' => 'Name is required'], 400);
        }

        $item = new Item();
        $item->setName($content['name']);
        if (isset($content['description'])) {
            $item->setDescription($content['description']);
        }

        $entityManager->persist($item);
        $entityManager->flush();

        return $this->json([
            'id' => $item->getId(),
            'name' => $item->getName(),
            'description' => $item->getDescription()
        ], 201);
    }

    #[Route('/{id}', name: 'api_items_show', methods: ['GET'])]
    public function show(int $id, ItemRepository $itemRepository): JsonResponse
    {
        $item = $itemRepository->find($id);

        if (!$item) {
            return $this->json(['message' => 'Item not found'], 404);
        }

        return $this->json([
            'id' => $item->getId(),
            'name' => $item->getName(),
            'description' => $item->getDescription()
        ]);
    }

    #[Route('/{id}', name: 'api_items_update', methods: ['PATCH'])]
    public function update(int $id, Request $request, ItemRepository $itemRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $item = $itemRepository->find($id);

        if (!$item) {
            return $this->json(['message' => 'Item not found'], 404);
        }

        $content = json_decode($request->getContent(), true);

        if (isset($content['name'])) {
            $item->setName($content['name']);
        }
        if (array_key_exists('description', $content)) {
            $item->setDescription($content['description']);
        }

        $entityManager->flush();

        return $this->json([
            'id' => $item->getId(),
            'name' => $item->getName(),
            'description' => $item->getDescription()
        ]);
    }

    #[Route('/{id}', name: 'api_items_delete', methods: ['DELETE'])]
    public function delete(int $id, ItemRepository $itemRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $item = $itemRepository->find($id);

        if (!$item) {
            return $this->json(['message' => 'Item not found'], 404);
        }

        $entityManager->remove($item);
        $entityManager->flush();

        return $this->json(['message' => 'Item deleted']);
    }
}
