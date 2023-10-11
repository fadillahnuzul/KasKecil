<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrintBkkRequest;
use App\Models\BKK_SPK;
use App\Models\BKKHeader_SPK;
use App\Services\PrintBkk;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class ApiPrintBkkSpkController extends Controller
{
    public function create(PrintBkkRequest $request) {
        $bkkHeader = BKKHeader_SPK::find($request->bkk_header_id);
        $bkkDetail = BKK_SPK::where('bkk_header_id', $request->bkk_header_id)->get();
        $tipe = "spk";
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
