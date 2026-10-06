<?php

declare(strict_types=1);

namespace App\Models;

final class StudentModel extends BaseIctModel
{
    protected $table = 'students';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['student_no', 'first_name', 'last_name', 'middle_name', 'campus_id', 'program_strand', 'year_level', 'section', 'contact_no', 'email', 'photo_path', 'signature_path', 'consent_at'];

    public function findByStudentNumber(string $studentNumber, ?int $campusId = null): ?array
    {
        $query = $this->where('student_no', $studentNumber);

        if ($campusId !== null) {
            $query->where('campus_id', $campusId);
        }

        return $query->first();
    }

    public function studentNumberExistsForCampus(string $studentNumber, int $campusId, ?int $exceptId = null): bool
    {
        $query = $this->where('student_no', $studentNumber)->where('campus_id', $campusId);

        if ($exceptId !== null) {
            $query->where('id !=', $exceptId);
        }

        return $query->first() !== null;
    }

    /** @return array<int, array<int, string>> */
    public function sectionsByCampus(): array
    {
        $rows = $this->select('campus_id, section')
            ->where('section !=', '')
            ->groupBy(['campus_id', 'section'])
            ->orderBy('campus_id')
            ->orderBy('section')
            ->findAll();
        $sections = [];
        foreach ($rows as $row) {
            $sections[(int) $row['campus_id']][] = (string) $row['section'];
        }

        return $sections;
    }
}
