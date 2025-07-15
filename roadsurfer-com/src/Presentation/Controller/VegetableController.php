<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Service\VegetableManagementService;
use App\Shared\DTO\VegetableApiRequestDTO;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @OA\Tag(name="Vegetables", description="Vegetable management endpoints")
 */
#[Route('/api/vegetables')]
class VegetableController extends AbstractController
{
    public function __construct(
        private readonly VegetableManagementService $vegetableService,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/vegetables",
     *     summary="List all vegetables",
     *     description="Retrieve a list of all vegetables with optional search and unit conversion",
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for vegetable names",
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
     *         description="List of vegetables retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Vegetables retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Carrot"),
     *                     @OA\Property(property="quantity", type="number", example=300.0),
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
     *             @OA\Property(property="message", type="string", example="Failed to retrieve vegetables"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    #[Route('', name: 'api_vegetables_list', methods: ['GET'])]
    public function listVegetables(Request $request): JsonResponse
    {
        $unit = $request->query->get('unit', 'g');

        $response = $this->vegetableService->listVegetables($unit);

        return $this->json($response, $response->success ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @OA\Post(
     *     path="/api/vegetables",
     *     summary="Add a new vegetable",
     *     description="Add a new vegetable to the collection with validation",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Vegetable data to add",
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "quantity", "unit"},
     *
     *                 @OA\Property(property="name", type="string", example="Carrot"),
     *                 @OA\Property(property="quantity", type="number", example=0.5),
     *                 @OA\Property(property="unit", type="string", example="kg")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Vegetable added successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Vegetable added successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Carrot"),
     *                 @OA\Property(property="quantity", type="number", example=500.0),
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
     *             @OA\Property(property="message", type="string", example="Failed to add vegetable"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    #[Route('', name: 'api_vegetables_add', methods: ['POST'])]
    public function addVegetable(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json([
                'success' => false,
                'message' => 'Invalid JSON data',
                'errors'  => ['Request body must be valid JSON'],
            ], Response::HTTP_BAD_REQUEST);
        }

        $vegetableRequest = new VegetableApiRequestDTO(
            $data['name'] ?? '',
            (float)($data['quantity'] ?? 0),
            $data['unit'] ?? 'kg'
        );

        $errors = $this->validator->validate($vegetableRequest);

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

        $response = $this->vegetableService->addVegetable($vegetableRequest);

        return $this->json($response, $response->success ? Response::HTTP_CREATED : Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @OA\Delete(
     *     path="/api/vegetables/{id}",
     *     summary="Remove a vegetable",
     *     description="Remove a vegetable from the collection by ID",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Vegetable ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Vegetable removed successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Vegetable removed successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Vegetable not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Vegetable not found")
     *         )
     *     )
     * )
     */
    #[Route('/{id}', name: 'api_vegetables_remove', methods: ['DELETE'])]
    public function removeVegetable(int $id): JsonResponse
    {
        $response = $this->vegetableService->removeVegetable($id);

        return $this->json($response, $response->success ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }
}
