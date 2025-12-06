<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarAuditLogs extends Command
{
    protected $signature = 'app:limpiar-audit-logs';
    protected $description = 'Limpiar audit logs con caracteres UTF-8 malformados';

    public function handle()
    {
        $deleted = DB::table('audit_logs')
            ->where('action', 'orden_compra_automatica')
            ->delete();

        $this->info("Se eliminaron {$deleted} registros de audit_logs");
        
        return Command::SUCCESS;
    }
}
