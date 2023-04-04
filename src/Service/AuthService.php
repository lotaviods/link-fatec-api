<?php

namespace App\Service;

use App\Constants\LoginType;
use App\DTO\LoginDTO;
use App\Entity\AccessToken;
use App\Entity\Administrator;
use App\Entity\AdminCreationInvite;
use App\Entity\Login;
use App\Entity\Student;
use App\Repository\AdministratorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthService
{
    private ManagerRegistry $doctrine;
    private UserPasswordHasherInterface $passwordHasher;
    private UserProviderInterface $userProvider;

    public function __construct(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, UserProviderInterface $userProvider)
    {
        $this->doctrine = $doctrine;
        $this->passwordHasher = $passwordHasher;
        $this->userProvider = $userProvider;
    }

    public function login(LoginDTO $loginDTO): AccessToken
    {
        $loginUser = $this->userProvider->loadUserByIdentifier($loginDTO->getEmail());

        if (!$this->passwordHasher->isPasswordValid($loginUser, $loginDTO->getPassword())) {
            throw new BadCredentialsException();
        }

        return $this->createTokenByUser($loginUser);
    }

    private function createTokenByUser(Login $user): AccessToken
    {
        $token = bin2hex(random_bytes(32));

        // Create a new AccessToken entity
        $accessToken = new AccessToken();
        $accessToken->setAccessToken($token);
        $accessToken->setUser($user);

        $date = new \DateTime();
        $date->add(new \DateInterval('P1D')); // add 1 day

        $accessToken->setExpiresAt($date);

        // Save the access token to the database
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($accessToken);
        $entityManager->flush();

        return $accessToken;
    }

    public function createLogin(string $email, string $password, string $name, int $type): Login
    {
        $login = new Login();
        $login->setEmail($email);

        $hashedPassword = $this->passwordHasher->hashPassword($login, $password);

        $login->setPassword($hashedPassword);
        $login->setName($name);
        $login->SetType($type);

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($login);
        $entityManager->flush();

        return $login;
    }

    public function registerStudent(Student $student, LoginDTO $dto, int $type = LoginType::STUDENT): AccessToken
    {
        //TODO Add more types of register not only student

        $login = $this->createLogin(
            $dto->getEmail(),
            $dto->getPassword(),
            $dto->getName(),
            $type,
        );

        $student->setLogin($login);

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($student);
        $entityManager->flush();

        return $this->createTokenByUser($login);
    }

    public function registerAdmin(string $token, LoginDTO $loginDTO, Administrator $admin): AccessToken
    {
        $type = LoginType::ADMIN;

        $manager = $this->doctrine->getManager();

        /** @var AdministratorRepository $adminRepo */
        $adminRepo = $manager->getRepository(AdminCreationInvite::class);

        /** @var AdminCreationInvite $invite */
        $invite = $adminRepo->findOneBy(['token' =>$token]);

        if(!$invite)
            throw new UnauthorizedHttpException('token');
        if($invite->isExpired())
            throw new UnauthorizedHttpException('token');

        $login = $this->createLogin(
            $loginDTO->getEmail(),
            $loginDTO->getPassword(),
            $loginDTO->getName(),
            $type,
        );

        $admin->setLogin($login);

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($admin);
        $entityManager->flush();

        return $this->createTokenByUser($login);
    }
}
