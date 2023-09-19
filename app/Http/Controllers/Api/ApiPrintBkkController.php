<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrintBkkRequest;
use App\Models\BKK;
use App\Models\BKKHeader;
use App\Services\PrintBkk;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiPrintBkkController extends Controller
{
    public function create(PrintBkkRequest $request)
    {
        $bkkHeader = BKKHeader::find($request->bkk_header_id);
        $bkkDetail = BKK::where('bkk_header_id', $request->bkk_header_id)->get();
        $tipe = "spi";
        try {
            $data = (new PrintBkk)->printBkk($bkkHeader, $bkkDetail, $tipe);
            return (response()->json([
                'status' => "Success",
                'message' => "Print BKK Berhasil",
                'data' => $data,
            ]));
        } catch (QueryException $e) {
            return new JsonResponse([
                'status' => "Failed",
                'message' => "Failed to store data in database",
                'data' => $e
            ], 500);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => "Failed",
                'message' => "Unknown error",
                'data' => $e
            ], 500);
        }
    }
}
