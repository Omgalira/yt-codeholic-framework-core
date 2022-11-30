<?php

namespace Omgalira\TheCodeholicPhpMvc;

use Omgalira\TheCodeholicPhpMvc\Db\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}