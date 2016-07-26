<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UserCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user:newrole';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User command';

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
    public function fire()
    {
//        $admin = new Role;
//        $admin->name = 'Admin';
//        $admin->save();
//
//        $admin = new Role;
//        $admin->name = 'Shop';
//        $admin->save();
//
//        $admin = new Role;
//        $admin->name = 'User';
//        $admin->save();

        $user = User::where('email', '=', 'lequivn@gmail.com')->first();
        if ($user) {
            $user->attachRole(2);
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array( //array('example', InputArgument::REQUIRED, 'An example argument.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }

}
