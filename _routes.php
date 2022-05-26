<?php

$this->get(
    '/lists',
    [AphecController::class, 'get_lists']
)->setName('aphec_lists_get');

$this->post(
    '/lists',
    [AphecController::class, 'set_lists']
)->setName('aphec_lists_set');
