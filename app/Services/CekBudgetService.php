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
    public function getBudget($project_id, $coa_id, $date)
    {
        $client = new Client();
        $response = $client->request('POST', 'http://172.16.1.253:8075/cashbon/apibudget/getbudget', [
            'form_params' => [
                'project' => '13',
                'coa' => '1391',
                'date' => '2023-04-28',
            ]
        ]);
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        $data = json_decode($body, true);
        return $data['data'];
    }

    public function isInBudget($budgetCoaBulan, $budgetCoaTahun, $budgetBKK)
    {
        if (($budgetCoaBulan > $budgetBKK) OR ($budgetCoaTahun > $budgetBKK)) {
            return true;
        } else {
            return false;
        }
    }
}
