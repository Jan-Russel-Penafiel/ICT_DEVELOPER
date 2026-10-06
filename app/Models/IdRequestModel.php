<?php

declare(strict_types=1);

namespace App\Models;

final class IdRequestModel extends BaseIctModel
{
    protected $table = 'id_requests';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['student_id', 'registration_link_id', 'reference_no', 'type', 'status', 'or_number', 'paid_at', 'printed_at', 'released_at', 'received_by'];

    public function accessibleRequests(?int $campusId, bool $registrarReleaseQueueOnly = false, ?string $type = null): array
    {
        $query = $this->detailsQuery($campusId, $registrarReleaseQueueOnly);

        if ($type !== null) {
            $query->where('id_requests.type', $type);
        }

        return $query->orderBy('id_requests.created_at', 'DESC')->findAll();
    }

    public function accessibleRequest(int $id, ?int $campusId, bool $registrarReleaseQueueOnly = false, ?string $type = null): ?array
    {
        $query = $this->detailsQuery($campusId, $registrarReleaseQueueOnly)->where('id_requests.id', $id);

        if ($type !== null) {
            $query->where('id_requests.type', $type);
        }

        return $query->first();
    }

    public function findForTracking(string $reference): ?array
    {
        return $this->select('id_requests.id, id_requests.reference_no, id_requests.type, id_requests.status, id_requests.created_at, id_requests.printed_at, id_requests.released_at, campuses.name campus')
            ->join('students', 'students.id = id_requests.student_id')
            ->join('campuses', 'campuses.id = students.campus_id')
            ->where('id_requests.reference_no', $reference)
            ->first();
    }

    private function detailsQuery(?int $campusId, bool $registrarReleaseQueueOnly): self
    {
        $query = $this->select('id_requests.*, students.student_no, students.first_name, students.last_name, students.middle_name, students.campus_id, students.program_strand, students.year_level, students.section, students.contact_no, students.email, campuses.name campus, registration_links.release_date')
            ->join('students', 'students.id = id_requests.student_id')
            ->join('campuses', 'campuses.id = students.campus_id')
            ->join('registration_links', 'registration_links.id = id_requests.registration_link_id', 'left');

        if ($campusId !== null) {
            $query->where('students.campus_id', $campusId);
        }

        if ($registrarReleaseQueueOnly) {
            $query->where('id_requests.status', 'printed')
                ->groupStart()
                    ->where('registration_links.release_date <=', date('Y-m-d'))
                    ->orWhere('id_requests.type', 'replacement')
                ->groupEnd();
        }

        return $query;
    }
}
