<?php

namespace App\Console\Commands\monitor;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\QueuedInvoices;

class QueuedInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:queued:invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $from = Carbon::now()->subDays(1)->toIso8601String();
        $to = Carbon::now()->toIso8601String();

        $servers = [
            ['name' => 'bels', 'threshold' => 1000],
            ['name' => 'igs', 'threshold' => 1000]
        ];

        foreach($servers as $server) {
            $result = DB::connection($server['name'])->select("select COUNT(*) as queued
                        from pos.pos_c_documento
                        where fecha_emision BETWEEN '" . $from . "' AND '" . $to . "'
                        and estado_sinc = 'N'");
            if(intval($result[0]->queued) > intval($server['threshold'])) {
                Mail::to('dev@ynk.cl')->send(new QueuedInvoices(strtoupper($server['name']), intval($result[0]->queued)));
            }
        }

        return Command::SUCCESS;
    }
}
