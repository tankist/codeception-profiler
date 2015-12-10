<?php

namespace Unit\Codeception\Extension;

use Codeception\Event\PrintResultEvent;
use Codeception\Event\TestEvent;
use Codeception\Events;
use Codeception\Specify;
use Codeception\TestCase\Test;
use Codeception\Util\Stub;
use Codeception\Extension\Profiler;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class ProfilerTest
 * @package Unit\Codeception\Extension
 *
 * @property \UnitTester $tester
 * @ignoreProfiler
 */
class ProfilerTest extends Test
{

    use Specify;

    const DATA_PATH = 'Codeception/Extension';

    const DELTA = 0.05;

    /**
     * @var EventDispatcher
     */
    protected $eventsManager;

    protected function _before()
    {
        //Can't be instantiated directly, because of specify ignored class
        $this->eventsManager = Stub::make('\Symfony\Component\EventDispatcher\EventDispatcher');
        $this->specifyConfig()->cloneOnly('eventsManager')->shallowClone();

        //Verify/Clean stubs should be isolated to each Specify block
        $this->afterSpecify(function () {
            $this->tester->verifyStubs();
            $this->tester->clearStubsToVerify();
        });
    }

    protected function _after()
    {
        $this->cleanSpecify();
    }

    public function testBeforeTestProfiler()
    {

        $this->specify('profiler should invoke before test event', function ($config, $options, $testCaseClass, $testCaseName) {
            /** @var Profiler $profiler */
            $profiler = Stub::construct('\Codeception\Extension\Profiler', [$config, $options], [
                'beforeTest' => Stub::once(),
            ]);

            $this->eventsManager->addSubscriber($profiler);

            /** @var Test $testCase */
            $testCase = Stub::construct($testCaseClass, [
                'name' => $testCaseName,
            ]);

            $this->eventsManager->dispatch(Events::TEST_BEFORE, new TestEvent($testCase));

            $this->tester->addStubToVerify($profiler);
        }, [
            'examples' => $this->getBeforeTestProfilerData(),
        ]);

        $this->specify('profiler should be initialized', function ($config, $options, $testCaseClass, $testCaseName) {
            $profiler = new Profiler($config, $options);

            $this->eventsManager->addSubscriber($profiler);

            /** @var Test $testCase */
            $testCase = Stub::construct($testCaseClass, [
                'name' => $testCaseName,
            ]);

            $this->eventsManager->dispatch(Events::TEST_BEFORE, new TestEvent($testCase));

            $profileData = $this->tester->getProtectedProperty($profiler, 'profile');

            $this->assertArrayHasKey(get_class($testCase), $profileData);
        }, [
            'examples' => $this->getBeforeTestProfilerData(),
        ]);
    }

    public function testAfterTestCanStoreProfileData()
    {
        $this->specify('should store profile data', function ($config, $options, $testCaseClass, $testCaseName, $timeout) {
            $profiler = new Profiler($config, $options);

            $this->eventsManager->addSubscriber($profiler);

            /** @var Test $testCase */
            $testCase = Stub::construct($testCaseClass, [
                'name' => $testCaseName,
            ]);

            $this->eventsManager->dispatch(Events::TEST_BEFORE, new TestEvent($testCase));
            usleep($timeout * 1e6);
            $this->eventsManager->dispatch(Events::TEST_END, new TestEvent($testCase));

            $profileData = $this->tester->getProtectedProperty($profiler, 'profile');

            $this->assertArrayHasKey(get_class($testCase), $profileData);
            $this->assertArrayHasKey($testCaseName, $profileData[get_class($testCase)]);

            $this->assertEquals($timeout, $profileData[get_class($testCase)][$testCaseName], sprintf('timeout should be +/- %.1f', self::DELTA * 100), self::DELTA);
        }, [
            'examples' => $this->getAfterTestProfilerData(),
        ]);
    }

    public function testAfterPrintShouldDisplayProfileMessage()
    {
        $this->specify('', function ($config, $options, $testCaseData, $expectedOutput, $expectedTimeoutsTestCases, $expectedTimeoutsTests) {
            $profiler = new Profiler($config, $options);

            $outputData = '';
            $output = Stub::construct('\Codeception\Lib\Console\Output', [$options], [
                'write' => Stub::atLeastOnce(function ($messages) use (&$outputData) {
                    $outputData .= $messages;
                }),
            ]);

            $this->tester->setProtectedProperty($profiler, 'output', $output);
            $this->eventsManager->addSubscriber($profiler);

            /** @var \PHPUnit_Framework_TestResult $testResult */
            $testResult = Stub::makeEmpty('\PHPUnit_Framework_TestResult');
            /** @var \PHPUnit_Util_Printer $printer */
            $printer = Stub::makeEmpty('\PHPUnit_Util_Printer');

            foreach ($testCaseData as $testCaseRow) {
                list($testCaseClass, $testCaseName, $timeout) = $testCaseRow;

                /** @var Test $testCase */
                $testCase = $this->getMock('\Codeception\TestCase\Test', null, [
                    'name' => $testCaseName,
                ], $testCaseClass);

                $this->eventsManager->dispatch(Events::TEST_BEFORE, new TestEvent($testCase));
                usleep($timeout * 1e6);
                $this->eventsManager->dispatch(Events::TEST_END, new TestEvent($testCase));
            }

            $this->eventsManager->dispatch(Events::RESULT_PRINT_AFTER, new PrintResultEvent($testResult, $printer));

            $this->assertProfileOutput($expectedOutput, $outputData);

            $actualOutputData = array_values(array_filter(preg_split('/<bold>[^<]+<\/bold>/', trim($outputData))));
            $this->assertCount(2, $actualOutputData);
            list($actualTestCaseOutput, $actualTestsOutput) = $actualOutputData;
            $this->assertProfilerData($expectedTimeoutsTestCases, $actualTestCaseOutput, '', self::DELTA);
            $this->assertProfilerData($expectedTimeoutsTests, $actualTestsOutput, '', self::DELTA);

        }, [
            'examples' => $this->getProfilerTestOutputData(),
        ]);
    }

    public function testCanIgnoreTest()
    {
        $this->specify('testcase should be ignored if @ignoreProfiler class annotation met', function ($config, $options, $testCaseClass, $testCaseName, $timeout, $annotations, $expectedOutput) {
            $profiler = new Profiler($config, $options);

            $outputData = '';
            $output = Stub::construct('\Codeception\Lib\Console\Output', [$options], [
                'write' => Stub::atLeastOnce(function ($messages) use (&$outputData) {
                    $outputData .= $messages;
                }),
            ]);

            $this->tester->setProtectedProperty($profiler, 'output', $output);
            $this->eventsManager->addSubscriber($profiler);

            /** @var \PHPUnit_Framework_TestResult $testResult */
            $testResult = Stub::makeEmpty('\PHPUnit_Framework_TestResult');
            /** @var \PHPUnit_Util_Printer $printer */
            $printer = Stub::makeEmpty('\PHPUnit_Util_Printer');

            $testCase = $this->getMockBuilder('\Codeception\TestCase\Test')
                ->setMethods(['getAnnotations'])
                ->setMockClassName($testCaseClass)
                ->getMock();

            $testCase
                ->expects($this->exactly(2))
                ->method('getAnnotations')
                ->will($this->returnValue($annotations));

            /** @var Test $testCase */

            $testCase->setName($testCaseName);

            $this->eventsManager->dispatch(Events::TEST_BEFORE, new TestEvent($testCase));
            usleep($timeout * 1e6);
            $this->eventsManager->dispatch(Events::TEST_END, new TestEvent($testCase));

            $this->eventsManager->dispatch(Events::RESULT_PRINT_AFTER, new PrintResultEvent($testResult, $printer));

            $this->assertProfileOutput($expectedOutput, $outputData);
        }, [
            'examples' => $this->getTestIgnoreAnnotationsData(),
        ]);
    }

    protected function getBeforeTestProfilerData()
    {
        return [
            [
                //config
                [],
                //options
                [],
                '\Codeception\TestCase\Test',
                'testCaseBeforeTest'
            ],
        ];
    }

    protected function getAfterTestProfilerData()
    {
        return [
            [
                //config
                [],
                //options
                [],
                '\Codeception\TestCase\Test',
                'testCaseAfterTest',
                //timeout
                1,
            ],
        ];
    }

    protected function getProfilerTestOutputData()
    {
        return $this->tester->getData(self::DATA_PATH . '/ProfilerTestOutputData.php', true);
    }

    protected function getTestIgnoreAnnotationsData()
    {
        return $this->tester->getData(self::DATA_PATH . '/ProfilerTestIgnoreAnnotationsData.php', true);
    }

    /**
     * @param string $expected
     * @param string $actual
     * @param string $message
     */
    protected function assertProfileOutput($expected, $actual, $message = '')
    {
        $testFormatOutputData = preg_replace('/[0-9\.]+ sec./', '__timestamp_placeholder__', $actual);
        $testFormatOutputData = preg_replace('/[\-\s]{5,}/', '__space_placeholder__', $testFormatOutputData);
        $this->assertEquals($expected, $testFormatOutputData, $message);
    }

    /**
     * @param array $expected
     * @param string $actual
     * @param string $message
     * @param float $delta
     * @param int $maxDepth
     */
    protected function assertProfilerData($expected, $actual, $message = '', $delta = 0.0, $maxDepth = 10)
    {
        $found = preg_match_all('/([\d\.]+) sec\./', $actual, $actualTimings);
        $this->assertCount($found, $expected, sprintf('Timings count is not equal to expected: %d vs %d', $found, count($expected)));
        $actualTimings = array_map('floatval', $actualTimings[1]);
        $this->assertEquals($expected, $actualTimings, $message, $delta, $maxDepth);
    }

}