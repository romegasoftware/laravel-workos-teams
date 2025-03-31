<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Traits\HasTeams;
use RomegaSoftware\WorkOSTeams\Traits\HasWorkOSExternalId;

// Put this inside an `if (false) {}` or similar condition to ensure it never
// actually loads in production. The IDE and static analyzers will still see it.
// The intent is to provide a stub for the User model that can be used to
// suppress IDE errors.
if (! class_exists(User::class)) {

    class User extends Authenticatable implements ExternalId
    {
        /** @use HasFactory<\Database\Factories\UserFactory> */
        use HasFactory, HasTeams, HasWorkOSExternalId, Notifiable;

        /**
         * The attributes that are mass assignable.
         *
         * @var list<string>
         */
        protected $fillable = [
            'name',
            'email',
            'avatar',
            'custom_field',
        ];

        /**
         * The attributes that should be hidden for serialization.
         *
         * @var list<string>
         */
        protected $hidden = [
            'workos_id',
            'remember_token',
        ];

        /**
         * The relationships that should be eager loaded.
         *
         * @var array<string>
         */
        protected $with = ['currentTeam'];

        /**
         * Get the user's initials.
         */
        public function initials(): string
        {
            return Str::of($this->name)
                ->explode(' ')
                ->map(fn (string $name) => Str::of($name)->substr(0, 1))
                ->implode('');
        }

        /**
         * Get the attributes that should be cast.
         *
         * @return array<string, string>
         */
        protected function casts(): array
        {
            return [
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
            ];
        }
    }
}
