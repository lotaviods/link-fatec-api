<?php

namespace App\Service;

use App\Entity\AccessToken;
use App\Entity\Login;
use App\Entity\Student;
use App\Entity\StudentResume;
use App\Repository\StudentRepository;
use App\Repository\StudentResumeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use stdClass;

class StudentService
{
    private ObjectManager $manager;
    private StudentRepository $repository;
    private StudentResumeRepository $resumeRepository;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();
        $this->repository = $doctrine->getManager()->getRepository(Student::class);
        $this->resumeRepository = $doctrine->getManager()->getRepository(StudentResume::class);
    }

    public function setStudentResumeUri(string $uri, string $studentId): void
    {

        $student = $this->repository->findOneBy(["id" => $studentId]);
        $resume = $this->resumeRepository->findOneBy(["student" => $studentId]);

        if ($resume) {
            $this->resumeRepository->remove($resume, true);
        }

        $resume = new StudentResume();

        $resume->setUri($uri);

        $resume->setStudent($student);
        $student->setResume($resume);

        $this->manager->persist($student);
        $this->manager->persist($resume);

        $this->manager->flush();
    }

    public function getStudentByLogin(Login $user): ?Student
    {
        try {
            $student = $this->repository->findByLogin($user);
        } catch (\Exception $e) {
            return null;
        }


        return $student;
    }

    public function getStudentInformation(Student $student): array
    {
        $course = $student->getCollageClass()?->getSemester()->getCourse();

        return [
            "id" => $student->getId(),
            "name" => $student->getName(),
            "course" => $course?->toArray() ?? new stdClass(),
            "ra" => $student->getRa()
        ];
    }

}