<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Customer;

class CustomerController extends AbstractController
{

    /**
     * @Route("/customers", name="get_customers", methods={"GET"})
     */
    public function getCustomers(): JsonResponse
    {
        $serializer = $this->get('serializer');

        $customer = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->findAll();
        
        if (!$customer) {
            throw $this->createNotFoundException(
                'No customers found'
            );
        }

        $json = $serializer->serialize($customer, 'json');
        $result = json_decode($json);

        return new JsonResponse([ 'data' => $result], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/customers/{id}", name="get_customer", methods={"GET"})
     */
    public function getCustomer(int $id): JsonResponse
    {
        $serializer = $this->get('serializer');

        $customer = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->find($id);
        
        if (!$customer) {
            throw $this->createNotFoundException(
                'No customer found for id ' . $id
            );
        }

        $json = $serializer->serialize($customer, 'json');
        $result = json_decode($json);

        return new JsonResponse([ 'data' => $result], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/customers", name="create_customer", methods={"POST"})
     */
    public function createCustomer(Request $request): JsonResponse
    {
        $data = $request->getContent();

        $serializer = $this->get('serializer');

        $em = $this->getDoctrine()->getManager();
        
        $customer = $serializer->deserialize($data, Customer::class, 'json');

        if (!$customer) {
            throw $this->createNotFoundException(
                'No data found for creation'
            );
        }

        $em->persist($customer);

        $em->flush();

        $json = $serializer->serialize($customer, 'json');

        $result = json_decode($json, true);

        return new JsonResponse([ 'data' => $result], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/customers/{id}", name="update_customer", methods={"PATCH"})
     */
    public function updateCustomer(Request $request, int $id): JsonResponse
    {
        $data = $request->getContent();

        $data = json_decode($data, true);

        $serializer = $this->get('serializer');

        $em = $this->getDoctrine()->getManager();

        $customer = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->find($id);
        
        if (!$customer) {
            throw $this->createNotFoundException(
                'No customer found for id ' . $id
            );
        }

        if (!$data) {
            throw $this->createNotFoundException(
                'No data found for update'
            );
        }

        foreach ($data as $key => $value) {
            $method = "set" . ucfirst($key);
            $customer->$method($value);
        }

        $em->flush();

        $json = $serializer->serialize($customer, 'json');
        $result = json_decode($json);

        return new JsonResponse([ 'data' => $result], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/customers/{id}", name="delete_customer", methods={"DELETE"})
     */
    public function deleteCustomer(int $id): JsonResponse
    {
        $serializer = $this->get('serializer');

        $em = $this->getDoctrine()->getManager();

        $customer = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->find($id);
        
        if (!$customer) {
            throw $this->createNotFoundException(
                'No customer found for id ' . $id
            );
        }

        $em->remove($customer);

        $em->flush();

        $json = $serializer->serialize($customer, 'json');
        $result = json_decode($json);

        return new JsonResponse([ 'data' => $result], JsonResponse::HTTP_OK);
    }

}
