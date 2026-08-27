<?php

test('the application redirects the homepage to the student registration system', function () {
    $response = $this->get('/');

    $response->assertRedirect('/students');
});
