<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\Service;

use App\Application\Service\FruitListManager;
use App\Application\Service\ImportProductsService;
use App\Application\Service\JsonToProductListService;
use App\Application\Service\ProductSplitterService;
use App\Application\Service\UnitConversionService;
use App\Application\Service\VegetableListManager;
use App\Infrastructure\Persistence\Repository\FruitRepository;
use App\Infrastructure\Persistence\Repository\VegetableRepository;
use App\Shared\DTO\FruitListDTO;
use App\Shared\DTO\ProductListDTO;
use App\Shared\DTO\VegetableListDTO;
use App\Tests\Integration\AbstractIntegrationTestCase;

/**
 * Integration test for ImportProductsService
 * 
 * This test covers the complete import workflow:
 * 1. Loading JSON file content
 * 2. Parsing JSON to ProductListDTO
 * 3. Unit conversion (kg/g to grams)
 * 4. Splitting products into fruits and vegetables
 * 
 * Test cases:
 * - processFileContentWithRequestJson(): Tests the main import workflow using request.json
 * - processFileContentWithCustomJson(): Tests with custom JSON data to verify flexibility
 * 
 * @package App\Tests\Integration\Application\Service
 */
class ImportProductsServiceIntegrationTest extends AbstractIntegrationTestCase
{
    private ImportProductsService $importProductsService;
    private JsonToProductListService $jsonToProductListService;
    private UnitConversionService $unitConversionService;
    private ProductSplitterService $productSplitterService;
    private FruitListManager $fruitListManager;
    private VegetableListManager $vegetableListManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jsonToProductListService = new JsonToProductListService(
            $this->container->get('validator')
        );
        $this->unitConversionService  = new UnitConversionService();
        $this->productSplitterService = new ProductSplitterService();
        
        $this->fruitListManager = new FruitListManager(
            new FruitRepository($this->container->get('doctrine')),
            $this->container->get('validator')
        );
        $this->vegetableListManager = new VegetableListManager(
            new VegetableRepository($this->container->get('doctrine')),
            $this->container->get('validator')
        );
        
        $this->importProductsService = new ImportProductsService(
            $this->jsonToProductListService,
            $this->unitConversionService,
            $this->productSplitterService,
            $this->fruitListManager,
            $this->vegetableListManager
        );
    }

    /**
     * Test processing request.json file with expected hardcoded entities
     * 
     * This test verifies the complete import workflow:
     * - File loading and validation
     * - JSON parsing to ProductListDTO
     * - Unit conversion (kg/g to grams)
     * - Product splitting into fruits and vegetables
     * 
     * Expected results are based on the request.json content:
     * - Fruits: 8 items (Apples, Pears, Melons, Berries, Bananas, Oranges, Avocado, Kiwi)
     * - Vegetables: 12 items (Carrot, Beans, Beetroot, Broccoli, Tomatoes, Celery, Cabbage, Onion, Cucumber, Lettuce, Kumquat, Pepper)
     */
    public function testProcessFileContentWithRequestJson(): void
    {
        // Process the request.json file
        $filePath = __DIR__ . '/../../../../request.json';
        $result   = $this->importProductsService->processFileContent($filePath);

        // Assert return structure
        $this->assertIsArray($result);
        $this->assertArrayHasKey('fruits', $result);
        $this->assertArrayHasKey('vegetables', $result);
        $this->assertInstanceOf(FruitListDTO::class, $result['fruits']);
        $this->assertInstanceOf(VegetableListDTO::class, $result['vegetables']);

        // Verify fruits (10 items from request.json)
        $fruits = $result['fruits'];
        $this->assertCount(10, $fruits->getFruits());

        // Verify specific fruits with converted quantities
        $expectedFruits = [
            ['name' => 'Apples', 'quantity' => 20000.0], // 20 kg -> 20000 g
            ['name' => 'Pears', 'quantity' => 3500.0],   // 3500 g -> 3500 g
            ['name' => 'Melons', 'quantity' => 120000.0], // 120 kg -> 120000 g
            ['name' => 'Berries', 'quantity' => 10000.0], // 10000 g -> 10000 g
            ['name' => 'Bananas', 'quantity' => 100000.0], // 100 kg -> 100000 g
            ['name' => 'Oranges', 'quantity' => 24000.0], // 24 kg -> 24000 g
            ['name' => 'Avocado', 'quantity' => 10000.0], // 10 kg -> 10000 g
            ['name' => 'Kiwi', 'quantity' => 10000.0],   // 10 kg -> 10000 g
            ['name' => 'Lettuce', 'quantity' => 20830.0], // 20830 g -> 20830 g
            ['name' => 'Kumquat', 'quantity' => 90000.0], // 90000 g -> 90000 g
        ];

        foreach ($expectedFruits as $expectedFruit) {
            $found = false;
            foreach ($fruits->getFruits() as $fruit) {
                if ($fruit->name === $expectedFruit['name']) {
                    $this->assertEquals($expectedFruit['quantity'], $fruit->quantity);
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, "Fruit {$expectedFruit['name']} should be present");
        }

        // Verify vegetables (10 items from request.json)
        $vegetables = $result['vegetables'];
        $this->assertCount(10, $vegetables->getVegetables());

        // Verify specific vegetables with converted quantities
        $expectedVegetables = [
            ['name' => 'Carrot', 'quantity' => 10922.0],     // 10922 g -> 10922 g
            ['name' => 'Beans', 'quantity' => 65000.0],      // 65000 g -> 65000 g
            ['name' => 'Beetroot', 'quantity' => 950.0],     // 950 g -> 950 g
            ['name' => 'Broccoli', 'quantity' => 3000.0],    // 3 kg -> 3000 g
            ['name' => 'Tomatoes', 'quantity' => 5000.0],    // 5 kg -> 5000 g
            ['name' => 'Celery', 'quantity' => 20000.0],     // 20 kg -> 20000 g
            ['name' => 'Cabbage', 'quantity' => 500000.0],   // 500 kg -> 500000 g
            ['name' => 'Onion', 'quantity' => 50000.0],      // 50 kg -> 50000 g
            ['name' => 'Cucumber', 'quantity' => 8000.0],    // 8 kg -> 8000 g
            ['name' => 'Pepper', 'quantity' => 150000.0],    // 150 kg -> 150000 g
        ];

        foreach ($expectedVegetables as $expectedVegetable) {
            $found = false;
            foreach ($vegetables->getVegetables() as $vegetable) {
                if ($vegetable->name === $expectedVegetable['name']) {
                    $this->assertEquals($expectedVegetable['quantity'], $vegetable->quantity);
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, "Vegetable {$expectedVegetable['name']} should be present");
        }
    }

    /**
     * Test processing custom JSON data to verify service flexibility
     * 
     * This test uses custom JSON data to verify that the service can handle:
     * - Different product types (fruit/vegetable)
     * - Mixed units (kg and g)
     * - Various quantities
     * - Proper unit conversion
     */
    public function testProcessFileContentWithCustomJson(): void
    {
        // Create custom JSON data
        $customJson = [
            [
                'id'       => 1,
                'name'     => 'Custom Apple',
                'type'     => 'fruit',
                'quantity' => 2.5,
                'unit'     => 'kg'
            ],
            [
                'id'       => 2,
                'name'     => 'Custom Banana',
                'type'     => 'fruit',
                'quantity' => 1500,
                'unit'     => 'g'
            ],
            [
                'id'       => 3,
                'name'     => 'Custom Carrot',
                'type'     => 'vegetable',
                'quantity' => 1.2,
                'unit'     => 'kg'
            ],
            [
                'id'       => 4,
                'name'     => 'Custom Tomato',
                'type'     => 'vegetable',
                'quantity' => 800,
                'unit'     => 'g'
            ],
            [
                'id'       => 5,
                'name'     => 'Custom Orange',
                'type'     => 'fruit',
                'quantity' => 3.0,
                'unit'     => 'kg'
            ]
        ];

        // Create temporary file with custom JSON
        $tempFile = tempnam(sys_get_temp_dir(), 'custom_products_');
        file_put_contents($tempFile, json_encode($customJson, JSON_PRETTY_PRINT));

        try {
            // Process the custom JSON file
            $result = $this->importProductsService->processFileContent($tempFile);

            // Assert return structure
            $this->assertIsArray($result);
            $this->assertArrayHasKey('fruits', $result);
            $this->assertArrayHasKey('vegetables', $result);
            $this->assertInstanceOf(FruitListDTO::class, $result['fruits']);
            $this->assertInstanceOf(VegetableListDTO::class, $result['vegetables']);

            // Verify fruits (3 items)
            $fruits = $result['fruits'];
            $this->assertCount(3, $fruits->getFruits());

            // Verify specific fruits with converted quantities
            $expectedFruits = [
                ['name' => 'Custom Apple', 'quantity' => 2500.0],   // 2.5 kg -> 2500 g
                ['name' => 'Custom Banana', 'quantity' => 1500.0],  // 1500 g -> 1500 g
                ['name' => 'Custom Orange', 'quantity' => 3000.0],  // 3.0 kg -> 3000 g
            ];

            foreach ($expectedFruits as $expectedFruit) {
                $found = false;
                foreach ($fruits->getFruits() as $fruit) {
                    if ($fruit->name === $expectedFruit['name']) {
                        $this->assertEquals($expectedFruit['quantity'], $fruit->quantity);
                        $found = true;
                        break;
                    }
                }
                $this->assertTrue($found, "Fruit {$expectedFruit['name']} should be present");
            }

            // Verify vegetables (2 items)
            $vegetables = $result['vegetables'];
            $this->assertCount(2, $vegetables->getVegetables());

            // Verify specific vegetables with converted quantities
            $expectedVegetables = [
                ['name' => 'Custom Carrot', 'quantity' => 1200.0],  // 1.2 kg -> 1200 g
                ['name' => 'Custom Tomato', 'quantity' => 800.0],   // 800 g -> 800 g
            ];

            foreach ($expectedVegetables as $expectedVegetable) {
                $found = false;
                foreach ($vegetables->getVegetables() as $vegetable) {
                    if ($vegetable->name === $expectedVegetable['name']) {
                        $this->assertEquals($expectedVegetable['quantity'], $vegetable->quantity);
                        $found = true;
                        break;
                    }
                }
                $this->assertTrue($found, "Vegetable {$expectedVegetable['name']} should be present");
            }

        } finally {
            // Clean up temporary file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * Test file validation with non-existent file
     */
    public function testProcessFileContentWithNonExistentFile(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File not found: /non/existent/file.json');

        $this->importProductsService->processFileContent('/non/existent/file.json');
    }

    /**
     * Test file validation with invalid JSON
     */
    public function testProcessFileContentWithInvalidJson(): void
    {
        // Create temporary file with invalid JSON
        $tempFile = tempnam(sys_get_temp_dir(), 'invalid_json_');
        file_put_contents($tempFile, '{"invalid": json}');

        try {
            $this->expectException(\JsonException::class);
            $this->expectExceptionMessage('Syntax error');

            $this->importProductsService->processFileContent($tempFile);
        } finally {
            // Clean up temporary file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }
}
