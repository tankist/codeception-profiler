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
        [
            [
                'SomeTestCodeceptionCase',
                'testCaseEbay',
                //timeout
                0.1,
            ],
            [
                'SomeTestCodeceptionCase',
                'testCasePhp',
                //timeout
                0.3,
            ],
        ],
        '<bold>TestCases profiling (1)__space_placeholder__</bold>SomeTestCodeceptionCase__space_placeholder____timestamp_placeholder__<bold>Tests profiling (up to 10 slowest)__space_placeholder__</bold>SomeTestCodeceptionCase::testCasePhp__space_placeholder____timestamp_placeholder__SomeTestCodeceptionCase::testCaseEbay__space_placeholder____timestamp_placeholder__',
        [0.4],
        [0.3, 0.1],
    ],
    [
        //config
        [],
        //options
        [
            'silent' => false,
            'verbosity' => OutputInterface::VERBOSITY_NORMAL,
        ],
        [
            [
                'SomeTestCodeceptionCase',
                'testCaseEbay',
                //timeout
                0.1,
            ],
            [
                'SomeTestAnotherCodeceptionCase',
                'testCasePhp',
                //timeout
                0.2,
            ],
        ],
        '<bold>TestCases profiling (2)__space_placeholder__</bold>SomeTestCodeceptionCase__space_placeholder____timestamp_placeholder__SomeTestAnotherCodeceptionCase__space_placeholder____timestamp_placeholder__<bold>Tests profiling (up to 10 slowest)__space_placeholder__</bold>SomeTestAnotherCodeceptionCase::testCasePhp__space_placeholder____timestamp_placeholder__SomeTestCodeceptionCase::testCaseEbay__space_placeholder____timestamp_placeholder__',
        [0.1, 0.2],
        [0.2, 0.1],
    ],
    [
        //config
        [
            'errorTimeLimit' => 1,
            'warningTimeLimit' => 0.5,
        ],
        //options
        [
            'silent' => false,
            'verbosity' => OutputInterface::VERBOSITY_NORMAL,
        ],
        [
            [
                'SomeTestCodeceptionCase',
                'testCaseEbay',
                //timeout
                1.1,
            ],
            [
                'SomeTestAnotherCodeceptionCase',
                'testCasePhp',
                //timeout
                0.3,
            ],
        ],
        '<bold>TestCases profiling (2)__space_placeholder__</bold><error>SomeTestCodeceptionCase__space_placeholder____timestamp_placeholder__</error>SomeTestAnotherCodeceptionCase__space_placeholder____timestamp_placeholder__<bold>Tests profiling (up to 10 slowest)__space_placeholder__</bold><error>SomeTestCodeceptionCase::testCaseEbay__space_placeholder____timestamp_placeholder__</error>SomeTestAnotherCodeceptionCase::testCasePhp__space_placeholder____timestamp_placeholder__',
        [1.1, 0.3],
        [1.1, 0.3],
    ],
    [
        //config
        [
            'errorTimeLimit' => 1,
            'warningTimeLimit' => 0.5,
        ],
        //options
        [
            'silent' => false,
            'verbosity' => OutputInterface::VERBOSITY_NORMAL,
        ],
        [
            [
                'SomeTestCodeceptionCase',
                'testCaseEbay',
                //timeout
                1.1,
            ],
            [
                'SomeTestAnotherCodeceptionCase',
                'testCasePhp',
                //timeout
                0.13,
            ],
            [
                'SomeTestAnotherCodeceptionCase2',
                'testCasePhp',
                //timeout
                0.63,
            ],
            [
                'SomeTestAnotherCodeceptionCase3',
                'testCaseLol',
                //timeout
                0.43,
            ],
            [
                'SomeTestAnotherCodeceptionCase4',
                'testCasePhp',
                //timeout
                0.37,
            ],
            [
                'SomeTestAnotherCodeceptionCase5',
                'testCaseBoom',
                //timeout
                0.32,
            ],
            [
                'SomeTestAnotherCodeceptionCase6',
                'testCasePhp',
                //timeout
                0.81,
            ],
            [
                'SomeTestAnotherCodeceptionCase7',
                'testCasePhp',
                //timeout
                0.33,
            ],
            [
                'SomeTestAnotherCodeceptionCase8',
                'testCasePhp',
                //timeout
                0.6,
            ],
            [
                'SomeTestAnotherCodeceptionCase9',
                'testCasePhp',
                //timeout
                0.4,
            ],
            [
                'SomeTestAnotherCodeceptionCase10',
                'testCaseRuby',
                //timeout
                1.01,
            ],
        ],
        '<bold>TestCases profiling (11)__space_placeholder__</bold><error>SomeTestCodeceptionCase__space_placeholder____timestamp_placeholder__</error>SomeTestAnotherCodeceptionCase__space_placeholder____timestamp_placeholder__<info>SomeTestAnotherCodeceptionCase2__space_placeholder____timestamp_placeholder__</info>SomeTestAnotherCodeceptionCase3__space_placeholder____timestamp_placeholder__SomeTestAnotherCodeceptionCase4__space_placeholder____timestamp_placeholder__SomeTestAnotherCodeceptionCase5__space_placeholder____timestamp_placeholder__<info>SomeTestAnotherCodeceptionCase6__space_placeholder____timestamp_placeholder__</info>SomeTestAnotherCodeceptionCase7__space_placeholder____timestamp_placeholder__<info>SomeTestAnotherCodeceptionCase8__space_placeholder____timestamp_placeholder__</info>SomeTestAnotherCodeceptionCase9__space_placeholder____timestamp_placeholder__<error>SomeTestAnotherCodeceptionCase10__space_placeholder____timestamp_placeholder__</error><bold>Tests profiling (up to 10 slowest)__space_placeholder__</bold><error>SomeTestCodeceptionCase::testCaseEbay__space_placeholder____timestamp_placeholder__</error><error>SomeTestAnotherCodeceptionCase10::testCaseRuby__space_placeholder____timestamp_placeholder__</error><info>SomeTestAnotherCodeceptionCase6::testCasePhp__space_placeholder____timestamp_placeholder__</info><info>SomeTestAnotherCodeceptionCase2::testCasePhp__space_placeholder____timestamp_placeholder__</info><info>SomeTestAnotherCodeceptionCase8::testCasePhp__space_placeholder____timestamp_placeholder__</info>SomeTestAnotherCodeceptionCase3::testCaseLol__space_placeholder____timestamp_placeholder__SomeTestAnotherCodeceptionCase9::testCasePhp__space_placeholder____timestamp_placeholder__SomeTestAnotherCodeceptionCase4::testCasePhp__space_placeholder____timestamp_placeholder__SomeTestAnotherCodeceptionCase7::testCasePhp__space_placeholder____timestamp_placeholder__SomeTestAnotherCodeceptionCase5::testCaseBoom__space_placeholder____timestamp_placeholder__',
        [1.1, 0.13, 0.63, 0.43, 0.37, 0.32, 0.81, 0.33, 0.6, 0.4, 1.01],
        [1.1, 1.01, 0.81, 0.63, 0.6, 0.43, 0.4, 0.37, 0.33, 0.32],
    ],
];