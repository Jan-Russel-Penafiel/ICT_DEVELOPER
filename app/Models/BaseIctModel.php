<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

abstract class BaseIctModel extends Model
{
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
