<?php

namespace App\Controller;

use App\DTO\LoginDTO;
use App\Mapper\AdminMapper;
use App\Mapper\StudentMapper;
use App\Service\AuthService;
use DateTime;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'login', methods: ['POST'])]
    public function login(Request $request, AuthService $service): JsonResponse
    {
        $loginDTO = LoginDTO::fromRequest($request);
        $accessToken = $service->login($loginDTO);

        return $this->json(
            ['token' => $accessToken->getAccessToken(), "expires_at" => $accessToken->getExpiresAt()->format(DateTimeInterface::ATOM)],
        );
    }

    #[Route('api/student/register', name: 'studentRegister', methods: ['POST'])]
    public function studentRegister(Request $request, AuthService $service): JsonResponse
    {
        //TODO make link student to class
        $accessToken = $service->registerStudent(StudentMapper::fromRequest($request), LoginDTO::fromRequest($request));

        return $this->json(['message' => 'User created successfully', 'token' => ['access_token' => $accessToken->getAccessToken(), 'expires_at' => $accessToken->getExpiresAt()->format(DateTimeInterface::ATOM)]], Response::HTTP_CREATED);
    }

    #[Route('api/admin/register', name: 'adminRegister', methods: ['POST'])]
    public function adminRegister(Request $request, AuthService $service): JsonResponse
    {
        //TODO make link student to class
        $accessToken = $service->registerAdmin(token: $request->get("invite_token"), loginDTO: LoginDTO::fromRequest($request), admin: AdminMapper::fromRequest($request));

        return $this->json(['message' => 'Admin created successfully', 'token' => ['access_token' => $accessToken->getAccessToken(), 'expires_at' => $accessToken->getExpiresAt()->format(DateTimeInterface::ATOM)]], Response::HTTP_CREATED);
    }
}