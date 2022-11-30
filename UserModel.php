<?php

namespace App\Core;

use App\Core\Db\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}