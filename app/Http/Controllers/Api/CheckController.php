<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CheckController extends Controller
{
    public function contentCheck()
    {
        $url = "python C:/Users/User/Desktop/www/abyss-hub/public/python/contentChecker/check.py 2>&1";
        $data = "C:/Users/User/Desktop/www/abyss-hub/public/python/contentChecker/tests/file.py";
        return shell_exec( $url . escapeshellarg($data) );
    }
}
