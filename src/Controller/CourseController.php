<?php /** @noinspection ALL */

namespace App\Controller;

use App\Entity\Course;
use App\Helper\ResponseHelper;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManager;

class CourseController extends AbstractController
{
    #[Route('/api/v1/courses/detail', name: 'couses_v1')]
    public function getAllClasses(ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $repository = $entityManager->getRepository(Course::class);

        $repository->findAll();

        $coursesResult = $repository->findAll();
        $courseArray = [];

        foreach ($coursesResult as $course) {
            $courseArray[] = $course->toArray();
        }

        return new JsonResponse($courseArray, Response::HTTP_OK, [], false);;
    }

    #[Route('/api/v1/course', name: 'create_couse_v1', methods: ['POST'])]
    public function createCourse(ManagerRegistry $doctrine, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADM');

        $name = $request->get("name");
        $description = $request->get("description");

        if($name == null) return ResponseHelper::missingParameterResponse("name");

        $entityManager = $doctrine->getManager();

        $repository = $entityManager->getRepository(Course::class);

        $course = new Course();

        $course->setName($name);
        $course->setDescription($description);

        /** @var CourseRepository $repository */
        $repository->save($course, true);


        return new JsonResponse(array(), Response::HTTP_OK, [], false);;
    }
}