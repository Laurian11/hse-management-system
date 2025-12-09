<?php

namespace App\Http\Controllers;

use App\Traits\ChecksPermissions;

abstract class Controller
{
    use ChecksPermissions;
}
