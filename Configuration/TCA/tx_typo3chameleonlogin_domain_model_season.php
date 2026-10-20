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
        'background_type' => [
            'label' => 'typo3_chameleon_login.db:season.backgroundType',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'typo3_chameleon_login.db:season.backgroundType.image', 'value' => 'image'],
                    ['label' => 'typo3_chameleon_login.db:season.backgroundType.gradient', 'value' => 'gradient'],
                ],
                'default' => 'image',
            ],
        ],
        'image' => [
            'label' => 'typo3_chameleon_login.db:season.image',
            'displayCond' => 'FIELD:background_type:=:image',
            'config' => [
                'type' => 'file',
                'allowed' => 'common-image-types',
                'maxitems' => 1,
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
        'gradient_color_1' => [
            'label' => 'typo3_chameleon_login.db:season.gradientColor1',
            'displayCond' => 'FIELD:background_type:=:gradient',
            'config' => [
                'type' => 'color',
                'default' => '',
                'searchable' => false,
            ],
        ],
        'gradient_color_2' => [
            'label' => 'typo3_chameleon_login.db:season.gradientColor2',
            'displayCond' => 'FIELD:background_type:=:gradient',
            'config' => [
                'type' => 'color',
                'default' => '',
                'searchable' => false,
            ],
        ],
        'gradient_color_3' => [
            'label' => 'typo3_chameleon_login.db:season.gradientColor3',
            'displayCond' => 'FIELD:background_type:=:gradient',
            'config' => [
                'type' => 'color',
                'default' => '',
                'searchable' => false,
            ],
        ],
        'gradient_color_4' => [
            'label' => 'typo3_chameleon_login.db:season.gradientColor4',
            'displayCond' => 'FIELD:background_type:=:gradient',
            'config' => [
                'type' => 'color',
                'default' => '',
                'searchable' => false,
            ],
        ],
        'gradient_speed' => [
            'label' => 'typo3_chameleon_login.db:season.gradientSpeed',
            'displayCond' => 'FIELD:background_type:=:gradient',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'typo3_chameleon_login.db:season.gradientSpeed.slow', 'value' => 'slow'],
                    ['label' => 'typo3_chameleon_login.db:season.gradientSpeed.medium', 'value' => 'medium'],
                    ['label' => 'typo3_chameleon_login.db:season.gradientSpeed.fast', 'value' => 'fast'],
                ],
                'default' => 'medium',
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
                    ['label' => 'typo3_chameleon_login.db:season.effect.leaves', 'value' => 'leaves'],
                    ['label' => 'typo3_chameleon_login.db:season.effect.blossoms', 'value' => 'blossoms'],
                    ['label' => 'typo3_chameleon_login.db:season.effect.confetti', 'value' => 'confetti'],
                    ['label' => 'typo3_chameleon_login.db:season.effect.hearts', 'value' => 'hearts'],
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
                --div--;typo3_chameleon_login.db:tab.appearance, background_type, image, gradient_color_1, gradient_color_2, gradient_color_3, gradient_color_4, gradient_speed, background_color, effect,
                --div--;typo3_chameleon_login.db:tab.period, valid_from, valid_until, recurring, priority,
                --div--;typo3_chameleon_login.db:tab.access, hidden
            ',
        ],
    ],
];
