<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class AuthController extends AbstractController
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    #[Route('/api/auth/check', name: 'api_auth_check', methods: ['POST', 'OPTIONS'])]
    #[OA\Post(
        path: '/api/auth/check',
        summary: 'Vérifie les credentials de l\'utilisateur',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password123')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authentification réussie',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Authentification réussie')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Authentification échouée',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Email ou mot de passe incorrect')
                    ]
                )
            )
        ]
    )]
    public function checkAuthentication(Request $request): JsonResponse
    {
        // Gestion des requêtes OPTIONS pour CORS
        if ($request->getMethod() === 'OPTIONS') {
            return new JsonResponse(null, Response::HTTP_OK, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type',
            ]);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Email et mot de passe requis'
            ], Response::HTTP_BAD_REQUEST, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type',
            ]);
        }

        // Vérification dans la base de données
        $sql = "SELECT * FROM user WHERE email = :email";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('email', $data['email']);
        $result = $stmt->executeQuery();
        $user = $result->fetchAssociative();

        dump($user);
        dump($data['password']);
        dump($user['password']);
        dump(password_verify($data['password'], $user['password']));

        if (!$user || !password_verify($data['password'], $user['password'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect'
            ], Response::HTTP_UNAUTHORIZED, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type',
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Authentification réussie',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'fname' => $user['fname'],
                'email' => $user['email']
            ]
        ], Response::HTTP_OK, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type',
        ]);
    }
} 