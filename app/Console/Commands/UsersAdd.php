<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "app:users-add {email} {name?}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Add a new user";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pw = Str::random(16);
        $user = User::create([
            "name" => $this->argument("name") ?? $this->argument("email"),
            "email" => $this->argument("email"),
            "password" => Hash::make($pw),
        ]);

        $this->info("created new user #{$user->id}: {$user->name}");
        $this->info("password: $pw");
        return 0;
    }
}
