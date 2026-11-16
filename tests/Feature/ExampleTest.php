<?php

test('the application redirects the home page to the book collection', function () {
    $this->get('/')
        ->assertRedirect(route('books.index'));
});
