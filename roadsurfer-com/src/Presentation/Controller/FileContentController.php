<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Service\ImportProductsService;
use App\Shared\DTO\FruitListDTO;
use App\Shared\DTO\VegetableListDTO;
use InvalidArgumentException;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="File Content")
 */
class FileContentController extends AbstractController
{
    public function __construct(
        private readonly ImportProductsService $importProductsService
    ) {}

    /**
     * Get processed file content from request.json
     * 
     * @OA\Get(
     *     path="/api/file_content",
     *     summary="Get processed file content",
     *     description="Processes the request.json file and returns split fruits and vegetables data without importing to database",
     *     tags={"File Content"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successfully processed file content",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="fruits",
     *                 type="array",
     *                 description="List of processed fruits",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Apples"),
     *                     @OA\Property(property="type", type="string", example="fruit"),
     *                     @OA\Property(property="quantity", type="number", format="float", example=20.0),
     *                     @OA\Property(property="unit", type="string", example="kg")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="vegetables",
     *                 type="array",
     *                 description="List of processed vegetables",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Carrot"),
     *                     @OA\Property(property="type", type="string", example="vegetable"),
     *                     @OA\Property(property="quantity", type="number", format="float", example=10922.0),
     *                     @OA\Property(property="unit", type="string", example="g")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - file not found or validation error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="error", type="string", example="File not found: request.json")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="error", type="string", example="Internal server error: Processing failed")
     *         )
     *     )
     * )
     */
    #[Route('/api/file_content', methods: ['GET'])]
    public function getFileContent(): JsonResponse
    {
        try {
            $requestJsonPath = $this->getParameter('kernel.project_dir') . '/request.json';
            $splitResult     = $this->importProductsService->processFileContent($requestJsonPath);
            
            return $this->json([
                'fruits'     => $this->formatFruitList($splitResult['fruits']),
                'vegetables' => $this->formatVegetableList($splitResult['vegetables'])
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function formatFruitList(FruitListDTO $fruitListDTO): array
    {
        $fruits = [];
        foreach ($fruitListDTO->getFruits() as $fruitDTO) {
            $fruits[] = [
                'id'       => $fruitDTO->productId,
                'name'     => $fruitDTO->name,
                'type'     => $fruitDTO->type,
                'quantity' => $fruitDTO->quantity,
                'unit'     => $fruitDTO->unit
            ];
        }
        return $fruits;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function formatVegetableList(VegetableListDTO $vegetableListDTO): array
    {
        $vegetables = [];
        foreach ($vegetableListDTO->getVegetables() as $vegetableDTO) {
            $vegetables[] = [
                'id'       => $vegetableDTO->productId,
                'name'     => $vegetableDTO->name,
                'type'     => $vegetableDTO->type,
                'quantity' => $vegetableDTO->quantity,
                'unit'     => $vegetableDTO->unit
            ];
        }
        return $vegetables;
    }
}
