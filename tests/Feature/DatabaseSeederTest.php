<?php

use App\Models\Book;
use App\Models\Publisher;
use App\Models\User;
use Database\Seeders\BookDemoSeeder;
use Database\Seeders\ManagerSeeder;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('the demo seeder adds its custom catalog idempotently', function () {
    $this->seed(BookDemoSeeder::class);
    $this->seed(BookDemoSeeder::class);

    expect(Book::query()->count())->toBe(4)
        ->and(Publisher::query()->count())->toBe(3);

    $this->assertDatabaseMissing('books', [
        'title' => 'Learning Web Layout',
    ]);
});

test('the manager seeder prompts for a hidden password and hashes it', function () {
    $command = Mockery::mock(Command::class);
    $command->shouldReceive('secret')
        ->once()
        ->with('Choose a manager password of at least 12 characters')
        ->andReturn('A-password-entered-in-terminal');
    $command->shouldReceive('info')
        ->once()
        ->with('Account created: manager@bookshelf.test');

    app(ManagerSeeder::class)
        ->setCommand($command)
        ->run();

    $manager = User::query()->where('email', 'manager@bookshelf.test')->firstOrFail();

    expect($manager->name)->toBe('Bookshelf Manager')
        ->and(Hash::check('A-password-entered-in-terminal', $manager->password))->toBeTrue()
        ->and($manager->password)->not->toBe('A-password-entered-in-terminal');
});

test('the manager seeder rejects a password shorter than twelve characters', function () {
    $command = Mockery::mock(Command::class);
    $command->shouldReceive('secret')
        ->once()
        ->with('Choose a manager password of at least 12 characters')
        ->andReturn('short');

    expect(fn () => app(ManagerSeeder::class)
        ->setCommand($command)
        ->run())
        ->toThrow(RuntimeException::class, 'Password must contain at least 12 characters.');

    $this->assertDatabaseCount('users', 0);
});

test('the manager seeder does not prompt again when the account exists', function () {
    User::factory()->create([
        'name' => 'Existing Manager',
        'email' => 'manager@bookshelf.test',
        'password' => Hash::make('Existing-password-123'),
    ]);

    $command = Mockery::mock(Command::class);
    $command->shouldReceive('info')
        ->once()
        ->with('Manager account already exists.');

    app(ManagerSeeder::class)
        ->setCommand($command)
        ->run();

    $manager = User::query()->where('email', 'manager@bookshelf.test')->firstOrFail();

    expect($manager->name)->toBe('Existing Manager')
        ->and(Hash::check('Existing-password-123', $manager->password))->toBeTrue();
});
