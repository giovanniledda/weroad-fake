<?php

namespace App\Console\Commands;

use App\Enums\Role as RoleEnum;
use App\Models\User;
use function bcrypt;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weroad-fake:create-user
                            {name : User fullname}
                            {email : User mail, will be used as username}
                            {--A|admin : The user will take the "Admin" role. Otherwise it will be created as "Editor"}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It sounds like it is: a command to create new users.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userFullName = $this->argument('name');

        $userEmail = $this->argument('email');

        $isAdmin = $this->option('admin');

        // TODO: it's better to stop the prompt before the password if email validation fails

        $password = $this->secret('Please, type a password for this user.');

        /** @var Validator $validator */
        $validator = Validator::make([
            'name' => $userFullName,
            'email' => $userEmail,
            'password' => $password,
        ], [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->newLine();
            $this->info('User not created. See error messages below:');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return 1;
        }

        $user = User::create([
            'name' => $userFullName,
            'email' => $userEmail,
            'password' => bcrypt($password),
        ]);

        try {
            if ($isAdmin) {
                $user->assignRole(RoleEnum::Admin);
            }

            $user->assignRole(RoleEnum::Editor);
        } catch (ModelNotFoundException $e) {
            $this->newLine();
            $this->warn('No roles detected! Try to launch the seeder to create them: "php artisan db:seed --class=\'Database\Seeders\Production\RoleSeeder\'"');

            $user->delete();

            return 1;
        }

        $this->newLine();
        $this->info('User successfully created! UUID: '.$user->uuid);

        return Command::SUCCESS;
    }
}
