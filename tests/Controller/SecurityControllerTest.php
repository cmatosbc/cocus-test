<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Tools\SchemaTool;

class SecurityControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);

        // Create database schema
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testRegisterSuccess(): void
    {
        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'Test User'
        ];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData)
        );

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertArrayHasKey('token', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('User registered successfully', $content['message']);

        // Verify user was created in database
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userData['email']]);
        $this->assertNotNull($user);
        $this->assertEquals($userData['name'], $user->getName());
    }

    public function testRegisterWithMissingData(): void
    {
        $incompleteData = [
            'email' => 'test@example.com',
            // missing password and name
        ];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($incompleteData)
        );

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Missing required fields', $content['error']);
    }

    public function testRegisterWithDuplicateEmail(): void
    {
        // First registration
        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'Test User'
        ];

        // Create first user directly through entity manager
        $user = new User();
        $user->setEmail($userData['email']);
        $user->setName($userData['name']);
        $user->setPassword('hashed_password');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Try to register second user with same email
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData)
        );

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Email already exists', $content['error']);
    }

    public function testRegisterWithInvalidEmail(): void
    {
        $userData = [
            'email' => 'invalid-email',
            'password' => 'password123',
            'name' => 'Test User'
        ];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData)
        );

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
    }

    public function testLoginSuccess(): void
    {
        // First register a user
        $this->testRegisterSuccess();

        // Then try to login
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($loginData)
        );

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('token', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('Login successful', $content['message']);
    }

    public function testLoginWithInvalidCredentials(): void
    {
        // First register a user
        $this->testRegisterSuccess();

        // Then try to login with invalid credentials
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrong-password'
        ];

        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($loginData)
        );

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Invalid credentials', $content['error']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
