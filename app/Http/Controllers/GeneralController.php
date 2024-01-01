<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class GeneralController extends Controller
{

    /**
     * @description Health check endpoint
     * @return JsonResponse
     */
    public function health(): JsonResponse
    {
        return Response::json([
            'status' => 'ok',
            'message' => 'The application is running'
        ]);
    }
}
