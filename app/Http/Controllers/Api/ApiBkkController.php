<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBKKRequest;
use App\Services\CreateBKKService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiBkkController extends Controller
{
    public function store(CreateBKKRequest $request)
    {
        try {
            $data = CreateBKKService::createBKK($request->bkk_header, $request->bkk_detail);
            return (response()->json([
                'status' => "Success",
                'message' => "BKK Berhasil Dibuat",
                'data' => $data,
            ]));
        } catch (QueryException $e) {
            return new JsonResponse(['error' => 'Database error'], 500);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Unknown error'], 500);
        }
    }
}
