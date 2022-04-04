<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CustomerController extends AbstractController
{
    #[Route('/customers', name: 'customers_show', methods: 'GET')]
    public function showCustomers(CustomerRepository $customerRepository): JsonResponse
    {
        $customers = $customerRepository->findAll();

        return $this->json($customers, 200);
    }

    #[Route('/customer/{id}', name: 'customer_show', methods: 'GET')]
    public function showCustomer(CustomerRepository $customerRepository, Customer $customer): JsonResponse
    {
        $customerShow = $customerRepository->find($customer->getId());

        return $this->json($customerShow, 200);
    }

    #[Route('/customer', name: 'customer_create', methods: 'POST')]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $json = $request->getContent();

        $customer = $serializer->deserialize($json, Customer::class, 'json');

        $em->persist($customer);
        $em->flush();

        return $this->json($customer, 201);
    }

    #[Route('/customer/{id}', name: 'customer_delete', methods: 'DELETE')]
    public function delete(EntityManagerInterface $em, Customer $customer, CustomerRepository $customerRepository): JsonResponse
    {
        $customerDelete = $customerRepository->find($customer->getId());

        $em->remove($customerDelete);
        $em->flush();

        return $this->json(null, 204);
    }
}
