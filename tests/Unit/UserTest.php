<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

describe('User Model', function () {
    it('uses the HasFactory trait', function () {
        $user = new User;
        expect(in_array(HasFactory::class, class_uses($user)))->toBeTrue();
    });

    it('has the correct fillable attributes', function () {
        $user = new User;
        $fillable = ['name', 'email', 'password'];
        expect($user->getFillable())->toEqual($fillable);
    });

    it('has the correct hidden attributes', function () {
        $user = new User;
        $hidden = ['password', 'remember_token'];
        expect($user->getHidden())->toEqual($hidden);
    });

    it('has the correct casts', function () {
        $user = new User;
        $casts = [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
        expect($user->getCasts())->toMatchArray($casts);
    });

    it('can be created using the factory', function () {
        $user = User::factory()->create();
        expect($user)->toBeInstanceOf(User::class);
        expect($user->name)->not->toBeEmpty();
        expect($user->email)->not->toBeEmpty();
        expect($user->password)->not->toBeEmpty();
    });

    it('can be created as unverified using the factory', function () {
        $user = User::factory()->unverified()->create();
        expect($user->email_verified_at)->toBeNull();
    });

    it('hashes the password when created', function () {
        $password = 'password123';
        $user = User::factory()->create(['password' => $password]);
        expect(Hash::check($password, $user->password))->toBeTrue();
    });
});
