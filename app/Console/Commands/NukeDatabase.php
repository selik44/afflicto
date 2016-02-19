<?php

namespace Friluft\Console\Commands;

use DB;
use Illuminate\Console\Command;

class NukeDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:nuke';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nuke the database.';

	/**
	 * Create a new command instance.
	 *
	 */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	    $database = 'friluft';
	    DB::connection()->getPdo()->exec('drop database if exists ' .$database .'; create database ' .$database .';');
    }
}
