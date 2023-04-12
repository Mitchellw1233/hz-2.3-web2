<?php

namespace App\Entity;

use App\Entity\Interface\IdentifierInterface;
use App\Entity\Trait\IdentifierTrait;
use Slimfony\ORM\Entity as BaseEntity;
use Slimfony\ORM\Mapping\Column;
use Slimfony\ORM\Mapping\Entity;
use Slimfony\ORM\Mapping\FKRelation;

#[Entity('exam')]
class Exam extends BaseEntity implements IdentifierInterface
{
    use IdentifierTrait;

    #[Column(name: 'id', type: 'serial', primaryKey: true, autoIncrement: true)]
    private int $id;
    #[Column(name: 'name', type: 'varchar(255)')]
    private string $name;
    #[FKRelation(targetEntity: Teacher::class, targetReferenceColumn: 'id')]
    #[Column(name: 'teacher_id', type: 'integer')]
    private Teacher $teacher;
    #[Column(name: 'exam_date', type: 'timestamp')]
    private \DateTime $examDate;
    #[Column(name: 'credits', type: 'smallint')]
    private int $credits;

    /**
     * @param string $name
     * @param Teacher $teacher
     * @param \DateTime $examDate
     * @param int $credits
     */
    public function __construct(string $name, Teacher $teacher, \DateTime $examDate, int $credits)
    {
        $this->name = $name;
        $this->teacher = $teacher;
        $this->examDate = $examDate;
        $this->credits = $credits;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Teacher
     */
    public function getTeacher(): Teacher
    {
        return $this->teacher;
    }

    /**
     * @param Teacher $teacher
     */
    public function setTeacher(Teacher $teacher): void
    {
        $this->teacher = $teacher;
    }

    /**
     * @return \DateTime
     */
    public function getExamDate(): \DateTime
    {
        return $this->examDate;
    }

    /**
     * @param \DateTime $examDate
     */
    public function setExamDate(\DateTime $examDate): void
    {
        $this->examDate = $examDate;
    }

    /**
     * @return int
     */
    public function getCredits(): int
    {
        return $this->credits;
    }

    /**
     * @param int $credits
     */
    public function setCredits(int $credits): void
    {
        $this->credits = $credits;
    }
}
