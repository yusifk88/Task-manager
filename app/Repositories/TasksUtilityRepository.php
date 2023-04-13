<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TasksUtilityRepository
{

    public static function getPriority(): int
    {
        $lastPriority = Task::select(DB::raw("max(priority) as max_priority"))->first();

        if ($lastPriority) {

            return $lastPriority->max_priority + 1;

        }

        return 1;


    }

}
