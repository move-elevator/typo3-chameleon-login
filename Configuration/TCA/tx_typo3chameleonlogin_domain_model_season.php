<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Resource\FileType;

return [
    'ctrl' => [
        'title' => 'typo3_chameleon_login.db:season',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'default_sortby' => 'priority DESC, uid ASC',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'iconfile' => 'EXT:typo3_chameleon_login/Resources/Public/Icons/season.svg',
    ],
    'columns' => [
        'title' => [
            'label' => 'typo3_chameleon_login.db:season.title',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'required' => true,
                'eval' => 'trim',
            ],
        ],
        'image' => [
            'label' => 'typo3_chameleon_login.db:season.image',
            'config' => [
                'type' => 'file',
                'allowed' => 'common-image-types',
                'maxitems' => 1,
                'minitems' => 1,
                'required' => true,
                'overrideChildTca' => [
                    'types' => [
                        '0' => [
                            'showitem' => '--palette--;;filePalette',
                        ],
                        FileType::IMAGE->value => [
                            'showitem' => 'crop, --palette--;;filePalette',
                        ],
                    ],
                ],
            ],
        ],
        'valid_from' => [
            'label' => 'typo3_chameleon_login.db:season.validFrom',
            'config' => [
                'type' => 'datetime',
                'format' => 'date',
                'required' => true,
                'searchable' => false,
            ],
        ],
        'valid_until' => [
            'label' => 'typo3_chameleon_login.db:season.validUntil',
            'config' => [
                'type' => 'datetime',
                'format' => 'date',
                'required' => true,
                'searchable' => false,
            ],
        ],
        'recurring' => [
            'label' => 'typo3_chameleon_login.db:season.recurring',
            'description' => 'typo3_chameleon_login.db:season.recurring.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'priority' => [
            'label' => 'typo3_chameleon_login.db:season.priority',
            'config' => [
                'type' => 'number',
                'default' => 0,
                'size' => 5,
            ],
        ],
        'background_color' => [
            'label' => 'typo3_chameleon_login.db:season.backgroundColor',
            'description' => 'typo3_chameleon_login.db:season.backgroundColor.description',
            'config' => [
                'type' => 'color',
                'default' => '',
                'searchable' => false,
            ],
        ],
        'effect' => [
            'label' => 'typo3_chameleon_login.db:season.effect',
            'description' => 'typo3_chameleon_login.db:season.effect.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'typo3_chameleon_login.db:season.effect.none', 'value' => ''],
                    ['label' => 'typo3_chameleon_login.db:season.effect.snow', 'value' => 'snow'],
                ],
                'default' => '',
            ],
        ],
        'hidden' => [
            'label' => 'typo3_chameleon_login.db:season.hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;typo3_chameleon_login.db:tab.general, title,
                --div--;typo3_chameleon_login.db:tab.appearance, image, background_color, effect,
                --div--;typo3_chameleon_login.db:tab.period, valid_from, valid_until, recurring, priority,
                --div--;typo3_chameleon_login.db:tab.access, hidden
            ',
        ],
    ],
];
