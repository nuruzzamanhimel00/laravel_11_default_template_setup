<?php

namespace App\Http\Controllers\Api\V1\Test;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    use ApiResponse;
    //
    public function index()
    {
        return $this->success([
            'test' => 'test',
            'test2' => 'test2'
        ]);
    }
}
