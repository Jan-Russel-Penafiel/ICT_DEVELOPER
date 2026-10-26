<?php

declare(strict_types=1);

namespace App\Models;

final class RegistrationLinkModel extends BaseIctModel
{
    protected $table = 'registration_links';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['campus_id', 'token', 'school_year', 'opens_at', 'closes_at', 'release_date', 'is_active', 'created_by'];

    public function activeForLandingPage(): array
    {
        return $this->select('registration_links.token, registration_links.school_year, registration_links.opens_at, registration_links.closes_at, campuses.name campus_name')
            ->join('campuses', 'campuses.id = registration_links.campus_id')
            ->where('registration_links.is_active', 1)
            ->where('registration_links.opens_at <=', date('Y-m-d H:i:s'))
            ->where('registration_links.closes_at >=', date('Y-m-d H:i:s'))
            ->orderBy('campuses.name')
            ->orderBy('registration_links.opens_at', 'DESC')
            ->findAll();
    }

    public function newestWithCampus(): array
    {
        $links = $this->select('registration_links.*, campuses.name campus')
            ->join('campuses', 'campuses.id = registration_links.campus_id')
            ->orderBy('registration_links.created_at', 'DESC')
            ->findAll();

        $now = date('Y-m-d H:i:s');
        foreach ($links as &$link) {
            if (! empty($link['is_active']) && isset($link['closes_at']) && (string) $link['closes_at'] < $now) {
                $link['is_active'] = 0;
            }

            $link['status'] = (! empty($link['is_active']) && isset($link['closes_at']) && (string) $link['closes_at'] >= $now) ? 'Active' : 'Inactive';
        }
        unset($link);

        return $links;
    }

    public function findWithCampusByToken(string $token): ?array
    {
        return $this->select('registration_links.*, campuses.name campus_name')
            ->join('campuses', 'campuses.id = registration_links.campus_id')
            ->where('registration_links.token', $token)
            ->first();
    }

    public function findOpenByToken(string $token): ?array
    {
        return $this->where('token', $token)
            ->where('is_active', 1)
            ->where('opens_at <=', date('Y-m-d H:i:s'))
            ->where('closes_at >=', date('Y-m-d H:i:s'))
            ->first();
    }
}
