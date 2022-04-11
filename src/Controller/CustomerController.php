<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
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
    #[Route('/api/customers', name: 'customers_show', methods: 'GET')]
    public function showCustomers(CustomerRepository $customerRepository): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $customers = $user->getCustomers();

        return $this->json($customers, 200);
    }

    #[Route('/api/customer/{id}', name: 'customer_show', methods: 'GET')]
    public function showCustomer(CustomerRepository $customerRepository, Customer $customer): JsonResponse
    {
        $user = $this->getUser()->getId();
        $customerShow = $customerRepository->find($customer->getId());

        if($user === $customerShow->getUser()->getId()){
            return $this->json($customerShow, 200);
        } else {
            return $this->json('Acces interdit', 500);
        }

    }

    #[Route('/api/customer', name: 'customer_create', methods: 'POST')]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $json = $request->getContent();

        $customer = $serializer->deserialize($json, Customer::class, 'json');

        $customer->setUser($this->getUser());

        $em->persist($customer);
        $em->flush();

        return $this->json($customer, 201);
    }

    #[Route('/api/customer/{id}', name: 'customer_delete', methods: 'DELETE')]
    public function delete(EntityManagerInterface $em, Customer $customer, CustomerRepository $customerRepository): JsonResponse
    {
        $user = $this->getUser()->getId();

        $customerDelete = $customerRepository->find($customer->getId());

        if($user === $customerDelete->getUser()->getId()){
            $em->remove($customerDelete);
            $em->flush();
        } else {
            return $this->json('Acces interdit', 500);
        }

        return $this->json(null, 204);
    }
}
