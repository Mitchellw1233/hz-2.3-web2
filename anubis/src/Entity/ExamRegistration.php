<?php

namespace App\Entity;

use App\Entity\Interface\IdentifierInterface;
use App\Entity\Trait\IdentifierTrait;
use Slimfony\ORM\Entity as BaseEntity;
use Slimfony\ORM\Mapping\Column;
use Slimfony\ORM\Mapping\Entity;
use Slimfony\ORM\Mapping\FKRelation;

#[Entity('exam_registration')]
class ExamRegistration extends BaseEntity implements IdentifierInterface
{
    use IdentifierTrait;

    #[Column(name: 'id', type: 'serial', primaryKey: true, autoIncrement: true)]
    private int $id;
    #[FKRelation(targetEntity: Exam::class, targetReferenceColumn: 'id')]
    #[Column(name: 'exam_id', type: 'integer')]
    private Exam $exam;
    #[FKRelation(targetEntity: Student::class, targetReferenceColumn: 'id')]
    #[Column(name: 'student_id', type: 'integer')]
    private Student $student;
    #[Column(name: 'registration_date', type: 'timestamp')]
    private \DateTime $registrationDate;
    #[Column(name: 'grade', type: 'decimal(3, 1)', nullable: true)]
    private ?float $grade;
    #[Column(name: 'graded_at', type: 'timestamp', nullable: true)]
    private ?\DateTime $gradedAt;

    /**
     * @param Exam $exam
     * @param Student $student
     * @param \DateTime $registrationDate
     * @param float|null $grade
     * @param \DateTime|null $gradedAt
     */
    public function __construct(Exam $exam, Student $student, \DateTime $registrationDate,
                                ?float $grade, ?\DateTime $gradedAt)
    {
        $this->setExam($exam);
        $this->setStudent($student);
        $this->setRegistrationDate($registrationDate);
        $this->setGrade($grade);
        $this->setGradedAt($gradedAt);
    }

    /**
     * @return Exam
     */
    public function getExam(): Exam
    {
        return $this->exam;
    }

    /**
     * @param Exam $exam
     */
    public function setExam(Exam $exam): void
    {
        $this->exam = $exam;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student
    {
        return $this->student;
    }

    /**
     * @param Student $student
     */
    public function setStudent(Student $student): void
    {
        $this->student = $student;
    }

    /**
     * @return \DateTime
     */
    public function getRegistrationDate(): \DateTime
    {
        return $this->registrationDate;
    }

    /**
     * @param \DateTime $registrationDate
     */
    public function setRegistrationDate(\DateTime $registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    /**
     * @return float|null
     */
    public function getGrade(): ?float
    {
        return $this->grade;
    }

    /**
     * @param float|null $grade
     */
    public function setGrade(?float $grade): void
    {
        $this->grade = $grade;
    }

    /**
     * @return \DateTime|null
     */
    public function getGradedAt(): ?\DateTime
    {
        return $this->gradedAt;
    }

    /**
     * @param \DateTime|null $gradedAt
     */
    public function setGradedAt(?\DateTime $gradedAt): void
    {
        $this->gradedAt = $gradedAt;
    }
}
