<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Controller;

use App\Infrastructure\Persistence\Repository\VegetableRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class VegetableControllerTest extends WebTestCase
{
    private VegetableRepository $vegetableRepository;
    private KernelBrowser $client;
    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client              = static::createClient();
        $this->container           = static::getContainer();
        $this->vegetableRepository = new VegetableRepository(
            $this->container->get('doctrine')
        );
        $this->cleanupTestData();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestData();
        parent::tearDown();
    }

    private function cleanupTestData(): void
    {
        $vegetables = $this->vegetableRepository->findAll();
        foreach ($vegetables as $vegetable) {
            $this->vegetableRepository->remove($vegetable);
        }
        $this->vegetableRepository->flush();
    }

    public function testGetVegetablesReturnsInsertedData(): void
    {
        // Insert test vegetable directly into the database
        $vegetable = new \App\Infrastructure\Persistence\Entity\Vegetable();
        $vegetable->setName('IntegrationTestCarrot');
        $vegetable->setQuantity(123.0);
        $this->vegetableRepository->persist($vegetable);
        $this->vegetableRepository->flush();

        // Pozovi GET /api/vegetables
        $this->client->request('GET', '/api/vegetables');
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // Proveri da li se ubačeni podatak nalazi u odgovoru
        $this->assertArrayHasKey('data', $responseData);
        $names = array_column($responseData['data'], 'name');
        $this->assertContains('IntegrationTestCarrot', $names);
    }

    public function testPostVegetableAddsToDatabase(): void
    {
        // Pripremi podatke za POST
        $vegetableData = [
            'name'     => 'TestBroccoli',
            'quantity' => 250.0,
            'unit'     => 'g'
        ];

        // Pozovi POST /api/vegetables
        $this->client->request(
            'POST',
            '/api/vegetables',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($vegetableData)
        );

        // Proveri response
        $this->assertResponseStatusCodeSame(201); // HTTP_CREATED
        $this->assertJson($this->client->getResponse()->getContent());
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Vegetable added successfully', $responseData['message']);
        
        // Proveri da li su podaci stvarno dodati u bazu
        $allVegetables = $this->vegetableRepository->findAll();
        $this->assertCount(1, $allVegetables);
        
        $addedVegetable = $allVegetables[0];
        $this->assertEquals('TestBroccoli', $addedVegetable->getName());
        $this->assertEquals(250.0, $addedVegetable->getQuantity());
    }

    public function testDeleteVegetableRemovesFromDatabase(): void
    {
        // Prvo dodaj vegetable u bazu
        $vegetable = new \App\Infrastructure\Persistence\Entity\Vegetable();
        $vegetable->setName('TestDeleteCarrot');
        $vegetable->setQuantity(100.0);
        $this->vegetableRepository->persist($vegetable);
        $this->vegetableRepository->flush();
        
        $vegetableId = $vegetable->getId();
        $this->assertNotNull($vegetableId);

        // Proveri da li je vegetable dodat
        $allVegetables = $this->vegetableRepository->findAll();
        $this->assertCount(1, $allVegetables);

        // Pozovi DELETE /api/vegetables/{id}
        $this->client->request('DELETE', "/api/vegetables/{$vegetableId}");

        // Proveri response
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Vegetable removed successfully', $responseData['message']);

        // Proveri da li je vegetable stvarno obrisan iz baze
        $remainingVegetables = $this->vegetableRepository->findAll();
        $this->assertCount(0, $remainingVegetables);
        
        // Dodatna provera - pokušaj da nađeš obrisani vegetable
        $deletedVegetable = $this->vegetableRepository->find($vegetableId);
        $this->assertNull($deletedVegetable);
    }
}
