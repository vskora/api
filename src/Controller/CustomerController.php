<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Customer;

class CustomerController extends AbstractController
{

    /**
     * @Route("/customer/{id}", name="get_customer", methods={"GET"})
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

        return new JsonResponse([ 'data' => $result]);
    }

}
