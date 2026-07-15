<?php

it('renders the login gate at the root path', function () {
    $this->get('/')->assertSuccessful();
});
