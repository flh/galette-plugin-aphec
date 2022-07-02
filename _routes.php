<?php

// Routes pour les adhérents
$this->get(
    '/lists',
    [ListsController::class, 'get_lists']
)->setName('aphec_lists_get')->add($authenticate);

$this->post(
    '/lists',
    [ListsController::class, 'set_lists']
)->setName('aphec_lists_set')->add($authenticate);


// Routes d'administration
$this->get(
    '/lists_admin',
    [AdminListsController::class, 'get_lists']
)->setName('aphec_lists_admin')->add($authenticate);

$this->post(
    '/lists_admin',
    [AdminListsController::class, 'set_lists']
)->setName('aphec_lists_admin_set')->add($authenticate);
