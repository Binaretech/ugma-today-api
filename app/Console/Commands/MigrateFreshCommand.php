<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Console\Migrations\FreshCommand;

class MigrateFreshCommand extends FreshCommand
{
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return 1;
        }

        $database = $this->input->getOption('database');

        $this->call('db:wipe', array_filter([
            '--database' => $database,
            '--drop-views' => $this->option('drop-views'),
            '--drop-types' => $this->option('drop-types'),
            '--force' => true,
        ]));

        $this->call('migrate', array_filter([
            '--database' => $database,
            '--path' => $this->input->getOption('path'),
            '--realpath' => $this->input->getOption('realpath'),
            '--force' => true,
            '--step' => $this->option('step'),
        ]));

        $this->call('passport:install');

        if ($this->needsSeeding()) {
            $this->runSeeder($database);
        }

        return 0;
    }
}
