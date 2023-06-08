<?php

namespace App\Console\Commands;

use App\Models\Pengeluaran;
use App\Services\CekBudgetService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckBudgetCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkbudget:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $kas = Pengeluaran::where('in_budget', 1)->get();
        $budget = new CekBudgetService;
        foreach ($kas as $item) {
            //Get data se coa
            $startMonth = Carbon::createFromFormat('F Y', $item->tanggal->format('M') . ' ' . $item->tanggal->format('Y'))->firstOfMonth();
            $endMonth = Carbon::createFromFormat('F Y', $item->tanggal->format('M') . ' ' . $item->tanggal->format('Y'))->endOfMonth();
            $kasCoa = Pengeluaran::where('coa', $item->coa)->whereBetween('tanggal', [$startMonth, $endMonth])->get();

            //Checkbudget
            $budgetCOA = $budget->getBudget($item->company, $item->coa, $item->date);
            if ($budgetCOA) {
                $isInBudget = $budget->isInBudget($budgetCOA[0]['budgetbulan'], $budgetCOA[0]['budgettahun'], $kasCoa->sum('jumlah'));
            } else {
                $isInBudget=false;
            }
            ($isInBudget) ? $item->update(['in_budget' => '0']) : $item;
        }

        return Command::SUCCESS;
    }
}
