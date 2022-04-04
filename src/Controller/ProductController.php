<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'products_show', methods: 'GET')]
    public function showProducts(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        return $this->json($products, 200);
    }

    #[Route('/api/product/{id}', name: 'product_show', methods: 'GET')]
    public function showProduct(ProductRepository $productRepository, Product $product): JsonResponse
    {
        $productShow = $productRepository->find($product->getId());

        return $this->json($productShow, 200);
    }

    #[Route('/api/product', name: 'product_create', methods: 'POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $json = $request->getContent();

        try {
            $product = $serializer->deserialize($json, Product::class, 'json');

            $em->persist($product);
            $em->flush();

            return $this->json($product, 201);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/api/product/{id}', name:'product_delete', methods: 'DELETE')]
    public function delete(EntityManagerInterface $em, Product $product, ProductRepository $productRepository): JsonResponse
    {
        $productDelete = $productRepository->find($product->getId());
        $em->remove($productDelete);
        $em->flush();

        return $this->json(null, 204);
    }
}
