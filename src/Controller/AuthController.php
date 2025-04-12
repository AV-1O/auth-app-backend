<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
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
        
        // TODO: Implémenter la logique d'authentification
        // Pour l'instant, on retourne une réponse temporaire
        return new JsonResponse([
            'success' => true,
            'message' => 'Authentification réussie'
        ], Response::HTTP_OK, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type',
        ]);
    }
} 