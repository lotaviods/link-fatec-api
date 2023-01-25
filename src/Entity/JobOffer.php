<?php

namespace App\Entity;

use App\Repository\JobOfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobOfferRepository::class)]
class JobOffer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    /** JobExperience */
    #[ORM\Column(nullable: true, options: ['default' => 1])]
    private ?int $job_experience = 1;

    #[ORM\Column(options: ['default' => 1])]
    private bool $is_active = true;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'jobOffer')]
    #[ORM\JoinColumn(name: 'target_course_id', referencedColumnName: 'id')]
    private ?Course $targetCourse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $promotionalImageUrl = null;

    #[ORM\ManyToOne(inversedBy: 'job_offer')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToMany(targetEntity: Student::class, mappedBy: "appliedJobs")]
    private Collection $subscribedStudents;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $role = null;

    #[ORM\Column(length: 255, nullable: false, options: ['default' => 0])]
    private ?int $likeCount = 0;

    public function __construct()
    {
        $this->subscribedStudents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function setPromotionalUrl(?string $url): self
    {
        $this->promotionalImageUrl = $url;

        return $this;
    }

    public function getJobExperience(): ?string
    {
        return $this->job_experience;
    }

    public function setJobExperience(?string $job_experience): self
    {
        if ($job_experience !== null)
            $this->job_experience = $job_experience;

        return $this;
    }

    public function getTargetCourse(): ?Course
    {
        return $this->targetCourse;
    }

    public function setTargetCourse(Course $course): self
    {
        $this->targetCourse = $course;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function toArray(): array
    {
        $newArray = [
            "id" => $this->id,
            "description" => $this->description,
            "job_experience" => $this->job_experience,
            "company_id" => $this->company->getId(),
            "company_name" => $this->company->getName(),
            "is_active" => $this->is_active,
            "applied_students" => count($this->subscribedStudents)
        ];
        if ($this->promotionalImageUrl != null)
            $newArray += ["promotional_image_url" => $this->promotionalImageUrl];

        if ($this->targetCourse != null) {
            $newArray += ["target_course" => $this->targetCourse->getName()];
        }

        return $newArray;
    }

    public function subscribeStudent(Student $student): self
    {
        if ($this->subscribedStudents->contains($student)) return $this;

        $this->subscribedStudents->add($student);

        return $this;
    }

    public function unsubscribeStudent(Student $student): self
    {
        if (!$this->subscribedStudents->contains($student)) return $this;

        $this->subscribedStudents->removeElement($student);

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
