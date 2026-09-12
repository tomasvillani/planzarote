<?php

namespace App\Console\Commands;

use App\Models\Plan;
use Illuminate\Console\Command;

class DeleteExpiredPlans extends Command
{
    protected $signature = 'plans:delete-expired';

    protected $description = 'Elimina los planes cuya fecha ya ha pasado';

    public function handle(): int
    {
        $planesEliminados = Plan::where('fecha', '<=', now())->delete();

        $this->info("Se han eliminado {$planesEliminados} planes expirados.");

        return Command::SUCCESS;
    }
}