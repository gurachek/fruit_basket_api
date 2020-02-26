<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Item;
use App\Entity\ItemType;
use App\Repository\BasketRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/basket", name="basket_") */
class BasketController extends AbstractController
{
    private $basketRepository;

    public function __construct(BasketRepository $basketRepository)
    {
        $this->basketRepository = $basketRepository;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index()
    {
        return $this->json($this->basketRepository->findAll());
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show($id)
    {
        $basket = $this->basketRepository->find($id);

        if (!$basket)
            throw $this->createNotFoundException('No basket found for id '. $id);

        $items = $basket->getItems();

        return $this->json([
            'id' => $basket->getId(),
            'name' => $basket->getName(),
            'items' => $items
        ], 200);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"PUT"})
     */
    public function edit(Request $request, $id)
    {
        $basket = $this->basketRepository->find($id);

        if (!$basket)
            throw $this->createNotFoundException('No basket found for id '. $id);

        $entityManager = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent(), 200);

        $name = $data['name'];

        if (empty($name)) {
            throw new NotFoundHttpException('Some parameters was missed!');
        }

        $basket->setName($name);
        // $basket->setUpdatedAt(new \DateTime());
        $entityManager->flush();

        return $this->json([
            'status' => true,
        ], 200);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"DELETE"})
     */
    public function delete($id)
    {
        $basket = $this->basketRepository->find($id);

        if (!$basket)
            throw $this->createNotFoundException('No basket found for id '. $id);

        $entityManager = $this->getDoctrine()->getManager();

        $this->basketRepository->removeItemsFromBasket($basket);

        $entityManager->remove($basket);
        $entityManager->flush();

        return $this->json([
            'status' => true
        ], 202);
    }

    /**
     * @Route("/add", name="add", methods={"POST"})
     */
    public function add(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $capacity = $data['capacity'];

        if (empty($name) || empty($capacity)) {
            throw new NotFoundHttpException('Some parameters was missed!');
        }

        $basket = new Basket();

        $basket->setName($name);
        $basket->setCapacity($capacity);

        $entityManager->persist($basket);
        $entityManager->flush();
        
        $errors = $validator->validate($basket);
        if (count($errors) > 0) {
            return $this->json([
                'status' => false,
                'errors' => $errors
            ], 400);
        }

        return $this->json([
            'status' => true,
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}/items/add", name="add_items", methods={"POST"})
     */
    public function addItems(Request $request, $id, EntityManagerInterface $entityManager)
    {
        $basket = $this->basketRepository->find($id);

        if (!$basket)
            throw $this->createNotFoundException('No basket found for id '. $id);

        $data = json_decode($request->getContent(), true);

        $requestedCapacity = array_sum(array_column($data, 'weight'));
        $remainingCapacity = $basket->getRemainingCapacity() ?? $basket->getCapacity();

        if ($requestedCapacity > $remainingCapacity) {

            throw new \Exception('Requested capacity is more than basket\' declared capacity.');
        }

        $capacityTaken = 0;

        foreach ($data as $itemData) {

            $typeId = $itemData['type_id'];
            $weight = $itemData['weight'];

            if (empty($typeId) || empty($weight)) {
                throw new NotFoundHttpException('Some parameters was missed!');
            }

            $type = $entityManager->getRepository(ItemType::class)->find($typeId);

            $item = new Item();

            $item->setWeight($weight);
            $item->setType($type);

            $entityManager->persist($item);
            $basket->addItem($item);

            $capacityTaken += $weight;
        }

        $basket->setRemainingCapacity($remainingCapacity - $capacityTaken);

        $entityManager->flush();

        return $this->json([
            'status' => true
        ], 201);
    }

    /**
     * @Route("/{id}/items/delete", name="delete_items", methods={"DELETE"})
     */
    public function deleteItems(Request $request, $id, EntityManagerInterface $entityManager)
    {
        $basket = $this->basketRepository->find($id);

        if (!$basket)
            throw $this->createNotFoundException('No basket found for id '. $id);

        $data = json_decode($request->getContent(), true);

        if (!empty($data)) {
            
            $repository = $entityManager->getRepository(Item::class);

            $capacityFreed = 0;

            foreach ($data as $itemId) {
                $item = $repository->find($itemId);

                if ($item) {
                    $capacityFreed += $item->getWeight();
                    $entityManager->remove($item);
                }
            }

            $updatedCapacity = $basket->getRemainingCapacity() + $capacityFreed;

            $basket->setRemainingCapacity($updatedCapacity);
            $entityManager->flush();
        }

        return $this->json([
            'status' => true
        ], 200);
    }

}
