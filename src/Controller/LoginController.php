<?php

namespace App\Controller;

use App\DTO\LoginDTO;
use App\Entity\AccessToken;
use App\Entity\Company;
use App\Mapper\AdminMapper;
use App\Mapper\CompanyMapper;
use App\Mapper\StudentMapper;
use App\Service\AuthService;
use App\Service\StudentService;
use DateTime;
use DateTimeInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'login', methods: ['POST'])]
    public function login(Request         $request,
                          AuthService     $authService,
                          ManagerRegistry $doctrine,
                          StudentService  $studentService
    ): JsonResponse
    {
        $loginDTO = LoginDTO::fromRequest($request);
        $loginData = $authService->login($loginDTO);

        return $this->json(
            $loginData,
        );
    }

    #[Route('api/register/student', name: 'studentRegister', methods: ['POST'])]
    public function studentRegister(Request $request, AuthService $service): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $service->registerStudent(StudentMapper::fromRequest($request), LoginDTO::fromRequest($request));
        return $this->json([], Response::HTTP_CREATED);
    }

    #[Route('api/register/master', name: 'adminMasterRegister', methods: ['POST'])]
    public function adminMasterRegister(Request $request, AuthService $service): JsonResponse
    {
        $service->registerAdminMaster(token: $request->get("invite_token"), loginDTO: LoginDTO::fromRequest($request), admin: AdminMapper::fromRequest($request));

        return $this->json([], Response::HTTP_CREATED);
    }

    #[Route('api/register/admin', name: 'adminRegister', methods: ['POST'])]
    public function adminRegister(Request $request, AuthService $service): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $service->registerAdmin(loginDTO: LoginDTO::fromRequest($request), admin: AdminMapper::fromRequest($request));

        return $this->json([], Response::HTTP_CREATED);
    }

    #[Route('api/register/company', name: 'adminRegister', methods: ['POST'])]
    public function companyRegister(Request $request, AuthService $service): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $service->registerCompany(loginDTO: LoginDTO::fromRequest($request), company: CompanyMapper::fromRequest($request));

        return $this->json([], Response::HTTP_CREATED);
    }
}
