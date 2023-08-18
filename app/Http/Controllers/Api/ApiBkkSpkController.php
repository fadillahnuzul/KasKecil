<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBkkRequest;
use App\Services\CreateBKKService;
use GuzzleHttp\Promise\Create;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiBkkSpkController extends Controller
{
    public function store(CreateBkkRequest $request)
    {
        try {
            $data = CreateBKKService::createBkkSpkApi($request->bkk_header, $request->bkk_detail);
            return (response()->json([
                'status' => "Success",
                'message' => "BKK Berhasil Dibuat",
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
