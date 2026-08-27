<?php

test('the application redirects the homepage to the student registration form', function () {
    $response = $this->get('/');

    $response->assertRedirect('/students/register');
});
