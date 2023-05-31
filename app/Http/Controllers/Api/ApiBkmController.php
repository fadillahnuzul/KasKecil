<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBKMRequest;
use App\Services\CreateBKMService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiBkmController extends Controller
{
    public function store(CreateBKMRequest $request)
    {
        try {
            $data = CreateBKMService::createBkmApi($request->bkm_header, $request->bkm_detail);
            return (response()->json([
                'status' => "Success",
                'message' => "BKM Berhasil Dibuat",
                'data' => $data,
            ]));
        } catch (QueryException $e) {
            return new JsonResponse(['status' => "Failed",
            'message' => "Failed to store data in database",
            'data' => $e
        ], 500);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => "Failed",
            'message' => "Unknown error",
            'data' => $e
        ], 500);
        }
    }
}
