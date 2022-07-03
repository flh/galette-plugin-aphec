<?php
$this->register(
    'Aphec',                            //Name
    'Gestion des services APHEC',       //Short description
    'Florian Hatat',                    //Author
    '1',                                //Version
    '0.9',                              //Galette version compatibility
    'aphec',                            //routing name and translation domain
    '2022-05-26',                       //Date
    [   //Permissions needed - not yet implemented
        'aphec_lists_get' => 'bureau',
        'anotherroute'   => 'anotheracl'
    ]
);
