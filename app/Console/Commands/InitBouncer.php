<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Bouncer;
use App\User;

class InitBouncer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:bouncer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        //
        // Define roles
        $admin = Bouncer::role()->create([
            'name' => 'admin',
            'title' => 'Administrator',
        ]);

        $member = Bouncer::role()->create([
            'name' => 'member',
            'title' => 'Member',
        ]);

        // Define abilities
      
        $manageSongs = Bouncer::ability()->create([
            'name' => 'manage-songs',
            'title' => 'Manager Songs',
        ]);

        $viewSongs = Bouncer::ability()->create([
            'name' => 'view-songs',
            'title' => 'View songs',
        ]);

        $managePlaylists = Bouncer::ability()->create([
            'name' => 'manage-playlists',
            'title' => 'Manager Playlists',
        ]);


        // Assign abilities to roles
      
        Bouncer::allow($admin)->to($manageSongs);
        Bouncer::allow($admin)->to($viewSongs);
        Bouncer::allow($admin)->to($managePlaylists);


        Bouncer::allow($member)->to($viewSongs);
        Bouncer::allow($member)->to($managePlaylists);


        // Assign role to users
        $user = User::where('email', 'admin@mylib.info')->first();
        Bouncer::assign($admin)->to($user);

        $user = User::where('email', 'user1@mylib.info')->first();
        Bouncer::assign($member)->to($user);

        $user = User::where('email', 'user2@mylib.info')->first();
        Bouncer::assign($member)->to($user);
    }
}