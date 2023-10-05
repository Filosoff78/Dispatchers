<?php
return [
    'intranet.customSection' => [
        'value' => [
            'provider' => '\\NB\\TestTask\\Integration\\Intranet\\CustomSectionProvider',
        ],
    ],
    'ui.entity-selector' => [
        'value' => [
            'entities' => [
                [
                    'entityId' => 'objects',
                    'provider' => [
                        'moduleId' => 'nb.testtask',
                        'className' =>'\\NB\\TestTask\\Integration\\UI\\EntitySelector\\ObjectsProvider'
                    ],
                ],
            ]
        ],
        'readonly' => true,
    ],
];
