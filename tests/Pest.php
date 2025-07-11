<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The `uses()` function extends PHPUnit's TestCase class to add a variety
| of Laravel-specific expectations and assertions to your tests.
|
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain
| conditions. The `expect()` function gives you access to a rich set of
| assertions to make your tests more expressive and readable.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is primarily built around callbacks and expectations, it also
| provides a few handy functions that you can use to make your tests easier
| to write.
|
*/
