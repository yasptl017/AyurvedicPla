<?php

namespace App\Console\Commands;

use App\Models\RogaPariksa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateOldUsersCommand extends Command
{
    protected $signature = 'migrate:old-users';

    protected $description = 'Command description';

    public function handle(): void
    {
        $entities = DB::table('PatientHistoryRogaPariksas')->get()->map(function ($item) {
            return [
                'PatientHistoryId' => $item->PatientHistoryId,
                'RogaPariksaId' => $item->RogaPariksaId,
            ];
        });

        foreach ($entities as $entity) {
            RogaPariksa::find($entity['RogaPariksaId'])->update(
                [
                    'patient_history_id' => $entity['PatientHistoryId'],
                ]
            );
        }
    }
}
