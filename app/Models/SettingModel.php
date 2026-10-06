<?php

declare(strict_types=1);

namespace App\Models;

final class SettingModel extends BaseIctModel
{
    protected $table = 'settings';
    protected $primaryKey = 'key';
    protected $useAutoIncrement = false;
    protected $useTimestamps = false;
    protected $allowedFields = ['key', 'value'];
}
