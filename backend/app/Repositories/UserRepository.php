<?php

namespace App\Repositories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @extends BaseRepository<User>
 */
class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array{name: string, email: string, password: string} $attributes
     */
    public function createCustomer(array $attributes): User
    {
        /** @var User $user */
        $user = $this->create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
            'role' => UserRole::Customer,
            'branch_id' => null,
        ]);

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        /** @var User|null $user */
        $user = $this->query()
            ->where('email', $email)
            ->first();

        return $user;
    }

    /**
     * @param array{name: string, email: string} $attributes
     */
    public function createCustomerFromOAuth(array $attributes): User
    {
        /** @var User $user */
        $user = $this->create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => Str::password(40),
            'role' => UserRole::Customer,
            'branch_id' => null,
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        return $user->refresh();
    }
}
