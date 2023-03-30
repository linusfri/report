<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class LuckyControllerJson
{
    #[Route('api/lucky/number')]
    public function jsonNumber(): JsonResponse {
        $number = random_int(0, 100);
        $data = [
            'lucky-number' => $number,
            'lucky-message' => 'Hi there!',
        ];

        /** @var JsonResponse */
        return new JsonResponse($data);
    }
}
