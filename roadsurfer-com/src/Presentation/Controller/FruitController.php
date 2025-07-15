<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Service\FruitManagementService;
use App\Shared\DTO\FruitApiRequestDTO;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @OA\Tag(name="Fruits", description="Fruit management endpoints")
 */
#[Route('/api/fruits')]
class FruitController extends AbstractController
{
    public function __construct(
        private readonly FruitManagementService $fruitService,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/fruits",
     *     summary="List all fruits",
     *     description="Retrieve a list of all fruits with optional search and unit conversion",
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for fruit names",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="unit",
     *         in="query",
     *         description="Unit for quantity (g or kg)",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"g", "kg"}, default="g")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of fruits retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Fruits retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Apple"),
     *                     @OA\Property(property="quantity", type="number", example=500.0),
     *                     @OA\Property(property="unit", type="string", example="g")
     *                 )
     *             )
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
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve fruits"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    #[Route('', name: 'api_fruits_list', methods: ['GET'])]
    public function listFruits(Request $request): JsonResponse
    {
        $unit = $request->query->get('unit', 'g');

        $response = $this->fruitService->listFruits($unit);

        return $this->json($response, $response->success ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @OA\Post(
     *     path="/api/fruits",
     *     summary="Add a new fruit",
     *     description="Add a new fruit to the collection with validation",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="name", type="string", example="Apple", description="Fruit name"),
     *             @OA\Property(property="quantity", type="number", example=1.5, description="Quantity"),
     *             @OA\Property(property="unit", type="string", example="kg", description="Unit (kg or g)")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Fruit added successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Fruit added successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Apple"),
     *                 @OA\Property(property="quantity", type="number", example=1500.0),
     *                 @OA\Property(property="unit", type="string", example="g")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
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
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to add fruit"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    #[Route('', name: 'api_fruits_add', methods: ['POST'])]
    public function addFruit(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json([
                'success' => false,
                'message' => 'Invalid JSON data',
                'errors'  => ['Request body must be valid JSON'],
            ], Response::HTTP_BAD_REQUEST);
        }

        $fruitRequest = new FruitApiRequestDTO(
            $data['name'] ?? '',
            (float)($data['quantity'] ?? 0),
            $data['unit'] ?? 'kg'
        );

        $errors = $this->validator->validate($fruitRequest);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return $this->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $errorMessages,
            ], Response::HTTP_BAD_REQUEST);
        }

        $response = $this->fruitService->addFruit($fruitRequest);

        return $this->json($response, $response->success ? Response::HTTP_CREATED : Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @OA\Delete(
     *     path="/api/fruits/{id}",
     *     summary="Remove a fruit",
     *     description="Remove a fruit from the collection by ID",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Fruit ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Fruit removed successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Fruit removed successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Fruit not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Fruit not found")
     *         )
     *     )
     * )
     */
    #[Route('/{id}', name: 'api_fruits_remove', methods: ['DELETE'])]
    public function removeFruit(int $fruitId): JsonResponse
    {
        $response = $this->fruitService->removeFruit($fruitId);

        return $this->json($response, $response->success ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }
}
