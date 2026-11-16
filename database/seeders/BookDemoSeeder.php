<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Database\Seeder;

class BookDemoSeeder extends Seeder
{
    /**
     * @var array<int, array{title: string, author: string, genre: string, publisher: string}>
     */
    private const BOOKS = [
        [
            'title' => 'The Mapmaker Notebook',
            'author' => 'Mara Ellis',
            'genre' => 'Adventure',
            'publisher' => 'Juniper House',
        ],
        [
            'title' => 'Small Gardens, Big Harvests',
            'author' => 'Noah Bennett',
            'genre' => 'Gardening',
            'publisher' => 'Fieldstone Press',
        ],
        [
            'title' => 'Beginners Guide to Astronomy',
            'author' => 'Leah Morgan',
            'genre' => 'Science',
            'publisher' => 'Northline Books',
        ],
        [
            'title' => 'The Sunday Table',
            'author' => 'Iris Coleman',
            'genre' => 'Cooking',
            'publisher' => 'Juniper House',
        ],
    ];

    public function run(): void
    {
        foreach (self::BOOKS as $bookDetails) {
            $publisher = Publisher::firstOrCreate([
                'name' => $bookDetails['publisher'],
            ]);

            Book::firstOrCreate(
                [
                    'title' => $bookDetails['title'],
                    'author' => $bookDetails['author'],
                ],
                [
                    'genre' => $bookDetails['genre'],
                    'publisher_id' => $publisher->id,
                ]
            );
        }
    }
}
