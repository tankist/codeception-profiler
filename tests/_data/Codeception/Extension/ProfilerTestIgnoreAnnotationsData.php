<?php

use Symfony\Component\Console\Output\OutputInterface;

return [
    [
        //config
        [],
        //options
        [
            'silent' => false,
            'verbosity' => OutputInterface::VERBOSITY_NORMAL,
        ],
        'SomeTestCodeceptionCaseIgnore',
        'testCasePhp',
        //timeout
        0.01,
        [
            'class' => [
                'someAnnotation' => 1,
            ]
        ],
        '<bold>TestCases profiling (1)__space_placeholder__</bold>SomeTestCodeceptionCaseIgnore__space_placeholder____timestamp_placeholder__<bold>Tests profiling (up to 10 slowest)__space_placeholder__</bold>SomeTestCodeceptionCaseIgnore::testCasePhp__space_placeholder____timestamp_placeholder__'
    ],
    [
        //config
        [],
        //options
        [
            'silent' => false,
            'verbosity' => OutputInterface::VERBOSITY_NORMAL,
        ],
        'SomeTestCodeceptionCaseIgnore',
        'testCasePhp',
        //timeout
        0.01,
        [
            'class' => [
                'ignoreProfiler' => '',
            ]
        ],
        ''
    ],
];