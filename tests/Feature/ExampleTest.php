<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertRedirect('/admin'); // Redirects to admin panel base URL
});
