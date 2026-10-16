<?php

declare(strict_types=1);

namespace App\Models;

final class CampusModel extends BaseIctModel
{
    protected $table = 'campuses';
    protected $allowedFields = ['code', 'name'];

    public function alphabetical(): array
    {
        return $this->where('code !=', 'SHS')->orderBy('name')->findAll();
    }

    public function exists(int $campusId): bool
    {
        return $this->find($campusId) !== null;
    }
}
