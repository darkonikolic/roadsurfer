<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Service\HealthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HealthController extends AbstractController
{
    public function __construct(
        private readonly HealthService $healthService
    ) {
    }

    #[Route('/health', name: 'app_health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        $healthStatus = $this->healthService->getSystemHealth();

        return $this->json(
            $healthStatus->toArray(),
            $healthStatus->isHealthy() ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE
        );
    }
}
