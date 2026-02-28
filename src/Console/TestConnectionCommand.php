<?php

namespace Weijukeji\LaravelOpenObserve\Console;

use Illuminate\Console\Command;
use Weijukeji\LaravelOpenObserve\Facades\OpenObserve;

class TestConnectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openobserve:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the connection to OpenObserve';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Testing connection to OpenObserve...');

        $config = config('openobserve');
        
        $this->line('Configuration:');
        $this->line('  URL: ' . $config['url']);
        $this->line('  Organization: ' . $config['organization']);
        $this->line('  Stream: ' . $config['stream']);
        $this->line('  Email: ' . $config['auth']['email']);
        $this->newLine();

        try {
            if (OpenObserve::testConnection()) {
                $this->info('✓ Connection successful!');
                $this->info('A test log has been sent to OpenObserve.');
                return Command::SUCCESS;
            } else {
                $this->error('✗ Connection failed!');
                $this->error('Please check your configuration and OpenObserve server status.');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('✗ Connection error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
