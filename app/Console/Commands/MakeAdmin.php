<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class MakeAdmin extends Command
{
    protected $signature = 'webinyuu:make-admin {email} {--name=Admin Webinyuu}';
    protected $description = 'Buat atau perbarui akun admin Webinyuu';

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->secret('Password admin (min. 8 karakter)');

        $v = Validator::make(
            ['email' => $email, 'password' => $password],
            ['email' => 'required|email', 'password' => 'required|min:8']
        );
        if ($v->fails()) {
            foreach ($v->errors()->all() as $e) {
                $this->error($e);
            }

            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name = $this->option('name');
        $user->password = $password; // di-hash otomatis oleh cast
        $user->role = UserRole::Admin;
        $user->email_verified_at = now();
        $user->save();

        $this->info("Admin siap: {$user->email}");

        return self::SUCCESS;
    }
}
