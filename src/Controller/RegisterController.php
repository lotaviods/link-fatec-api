<?php

namespace App\Controller;

use App\Constraints\CompanyConstraints;
use App\DTO\LoginDTO;
use App\Entity\Company;
use App\Entity\CompanyAddress;
use App\Form\Company\CompanyAddressForm;
use App\Form\Company\CompanyForm;
use App\Mapper\AdminMapper;
use App\Mapper\CompanyMapper;
use App\Mapper\StudentMapper;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\BlankValidator;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegisterController extends AbstractController
{
    private TranslatorInterface $translator;
    private ValidatorInterface $validator;

    public function __construct(TranslatorInterface $translator, ValidatorInterface $validator)
    {
        $this->translator = $translator;
        $this->validator = $validator;
    }

    #[Route('api/v1/register/student', name: 'studentRegister_v1', methods: ['POST'])]
    public function studentRegister(Request $request, AuthService $service): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $service->registerStudent(StudentMapper::fromRequest($request), LoginDTO::fromRequest($request));
        return $this->json([], Response::HTTP_CREATED);
    }

    #[Route('api/v1/register/master', name: 'adminMasterRegister_v1', methods: ['POST'])]
    public function adminMasterRegister(Request $request, AuthService $service): JsonResponse
    {
        $service->registerAdminMaster(token: $request->get("invite_token"), loginDTO: LoginDTO::fromRequest($request), admin: AdminMapper::fromRequest($request));

        return $this->json([], Response::HTTP_CREATED);
    }

    #[Route('api/v1/register/admin', name: 'adminRegister_v1', methods: ['POST'])]
    public function adminRegister(Request $request, AuthService $service): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $service->registerAdmin(loginDTO: LoginDTO::fromRequest($request), admin: AdminMapper::fromRequest($request));

        return $this->json([], Response::HTTP_CREATED);
    }

    #[Route('api/v1/register/company', name: 'companyRegister_v1', methods: ['POST'])]
    public function companyRegister(Request $request, AuthService $service): JsonResponse
    {
        $data = $request->request->all();

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $formAddress = $this->createForm(CompanyAddressForm::class, new CompanyAddress());
        $constraints = CompanyConstraints::getConstraints($this->translator);
        //TODO: Remove form and set constraints.
        $formAddress->submit($data);

        $violations = $this->validator->validate($request->request->all(), $constraints);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $v) {
                $errors[] = str_replace("\"", "", $v->getMessage());
            }
            return new JsonResponse([$this->translator->trans('errors') => $errors], Response::HTTP_BAD_REQUEST);

        }

        if (!$formAddress->isValid()) {
            $errors = [];
            foreach ($formAddress->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse([$this->translator->trans('errors') => $errors], Response::HTTP_BAD_REQUEST);
        }

        $service->registerCompany(
            loginDTO: LoginDTO::fromRequest($request),
            company: CompanyMapper::fromRequest($request),
            companyAddress: $formAddress->getData()
        );

        return $this->json([], Response::HTTP_CREATED);
    }
}