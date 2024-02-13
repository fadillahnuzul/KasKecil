<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

/**
 * Class CekBudgetService
 * @package App\Services
 */
class CekBudgetService
{
    public function getBudget(int $company_id, int $coa_id, string $date, int $unit=null)
    {
        if (env("BUDGET_DUMMY_DATA") == true) {
            return [[
                "budgetbulan" => 999999999999,
                "budgettahun" => 999999999999,
            ]];
        }

        $client = new Client();
        $response = $client->request('POST', 'http://172.16.1.253:8075/cashbon/apibudget/getbudget', [
            'form_params' => [
                'company' => $company_id,
                'coa' => $coa_id,
                'date' => $date,
                'unit' => $unit
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        return $data['data'];
    }

    public function isInBudget($budgetCoaBulan, $budgetCoaTahun, $budgetBKK)
    {
        if (($budgetCoaBulan > $budgetBKK) or ($budgetCoaTahun > $budgetBKK)) {
            return true;
        } else {
            return false;
        }
    }
}
