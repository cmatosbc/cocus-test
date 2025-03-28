<?php

namespace App\Controller;

use App\Repository\RefreshTokenRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TokenController extends AbstractController
{
    public function __construct(
        private RefreshTokenRepository $refreshTokenRepository,
        private JWTTokenManagerInterface $jwtManager
    ) {
    }

    #[Route('/api/token/refresh', name: 'token_refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        try {
            $content = json_decode($request->getContent(), true);
            $refreshToken = $content['refresh_token'] ?? null;

            if (!$refreshToken) {
                throw new AuthenticationException('Refresh token is required');
            }

            $token = $this->refreshTokenRepository->findValidToken($refreshToken);

            if (!$token) {
                throw new AuthenticationException('Invalid refresh token');
            }

            // Generate new JWT token
            $user = $token->getUser();
            $jwt = $this->jwtManager->create($user);

            // Generate new refresh token
            $newRefreshToken = bin2hex(random_bytes(32));
            $token->setToken($newRefreshToken);
            $token->setExpiresAt(new \DateTimeImmutable('+30 days'));
            
            $this->refreshTokenRepository->save($token, true);

            return $this->json([
                'token' => $jwt,
                'refresh_token' => $newRefreshToken
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 401);
        }
    }
}
