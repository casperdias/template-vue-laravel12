<?php

it('returns to login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
