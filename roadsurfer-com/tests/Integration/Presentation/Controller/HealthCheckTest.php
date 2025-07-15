<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthCheckTest extends WebTestCase
{
    public function testHealthEndpointReturnsOk(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testHealthResponseStructure(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $responseContent = $client->getResponse()->getContent();
        $this->assertNotNull($responseContent, 'Response content should not be null');

        $response = json_decode($responseContent, true);
        $this->assertNotNull($response, 'Response should be valid JSON');
        $this->assertIsArray($response, 'Response should be an array');

        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('timestamp', $response);
        $this->assertArrayHasKey('environment', $response);
        $this->assertArrayHasKey('services', $response);
        $this->assertArrayHasKey('database', $response['services']);
        $this->assertArrayHasKey('redis', $response['services']);
        $this->assertArrayHasKey('application', $response['services']);
    }

    public function testHealthEndpointReturnsCorrectStatus(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $responseContent = $client->getResponse()->getContent();
        $this->assertNotNull($responseContent, 'Response content should not be null');

        $response = json_decode($responseContent, true);
        $this->assertNotNull($response, 'Response should be valid JSON');
        $this->assertIsArray($response, 'Response should be an array');

        $this->assertEquals('healthy', $response['status']);
        $this->assertEquals('ok', $response['services']['application']);
    }
}
