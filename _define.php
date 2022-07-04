<?php
$this->register(
    'Aphec',                            //Name
    'Gestion des services APHEC',       //Short description
    'Florian Hatat',                    //Author
    '1',                                //Version
    '0.9.6',                              //Galette version compatibility
    'aphec',                            //routing name and translation domain
    '2022-05-26',                       //Date
    [
        'aphec_lists_get' => 'member',
        'aphec_lists_set' => 'member',
        'aphec_lists_admin' => 'staff',
        'aphec_lists_admin_set' => 'staff',
    ]
);
