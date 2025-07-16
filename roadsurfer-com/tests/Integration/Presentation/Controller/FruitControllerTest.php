<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Controller;

use App\Infrastructure\Persistence\Repository\FruitRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FruitControllerTest extends WebTestCase
{
    private FruitRepository $fruitRepository;
    private KernelBrowser $client;
    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client          = static::createClient();
        $this->container       = static::getContainer();
        $this->fruitRepository = new FruitRepository(
            $this->container->get('doctrine')
        );
        $this->cleanupTestData();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestData();
        parent::tearDown();
    }

    public function testGetFruitsReturnsInsertedData(): void
    {
        // Insert test fruit directly into the database
        $fruit = new \App\Infrastructure\Persistence\Entity\Fruit();
        $fruit->setName('IntegrationTestApple');
        $fruit->setQuantity(150.0);
        $this->fruitRepository->persist($fruit);
        $this->fruitRepository->flush();

        // Pozovi GET /api/fruits
        $this->client->request('GET', '/api/fruits');
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // Proveri da li se ubačeni podatak nalazi u odgovoru
        $this->assertArrayHasKey('data', $responseData);
        $names = array_column($responseData['data'], 'name');
        $this->assertContains('IntegrationTestApple', $names);
    }

    public function testPostFruitAddsToDatabase(): void
    {
        // Pripremi podatke za POST
        $fruitData = [
            'name'     => 'TestOrange',
            'quantity' => 300.0,
            'unit'     => 'g'
        ];

        // Pozovi POST /api/fruits
        $this->client->request(
            'POST',
            '/api/fruits',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($fruitData)
        );

        // Proveri response
        $this->assertResponseStatusCodeSame(201); // HTTP_CREATED
        $this->assertJson($this->client->getResponse()->getContent());
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Fruit added successfully', $responseData['message']);
        
        // Proveri da li su podaci stvarno dodati u bazu
        $allFruits = $this->fruitRepository->findAll();
        $this->assertCount(1, $allFruits);
        
        $addedFruit = $allFruits[0];
        $this->assertEquals('TestOrange', $addedFruit->getName());
        $this->assertEquals(300.0, $addedFruit->getQuantity());
    }

    public function testDeleteFruitRemovesFromDatabase(): void
    {
        // Prvo dodaj fruit u bazu
        $fruit = new \App\Infrastructure\Persistence\Entity\Fruit();
        $fruit->setName('TestDeleteBanana');
        $fruit->setQuantity(200.0);
        $this->fruitRepository->persist($fruit);
        $this->fruitRepository->flush();
        
        $fruitId = $fruit->getId();
        $this->assertNotNull($fruitId);

        // Proveri da li je fruit dodat
        $allFruits = $this->fruitRepository->findAll();
        $this->assertCount(1, $allFruits);

        // Pozovi DELETE /api/fruits/{id}
        $this->client->request('DELETE', "/api/fruits/{$fruitId}");

        // Proveri response
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Fruit removed successfully', $responseData['message']);

        // Proveri da li je fruit stvarno obrisan iz baze
        $remainingFruits = $this->fruitRepository->findAll();
        $this->assertCount(0, $remainingFruits);
        
        // Dodatna provera - pokušaj da nađeš obrisani fruit
        $deletedFruit = $this->fruitRepository->find($fruitId);
        $this->assertNull($deletedFruit);
    }

    private function cleanupTestData(): void
    {
        $fruits = $this->fruitRepository->findAll();
        foreach ($fruits as $fruit) {
            $this->fruitRepository->remove($fruit);
        }
        $this->fruitRepository->flush();
    }
}
