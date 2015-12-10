<?php

namespace Codeception\Extension;

use Codeception\Event\PrintResultEvent;
use Codeception\Event\TestEvent;
use Codeception\Lib\Console\Message;
use Codeception\Platform\Extension;
use Codeception\Events;

/**
 * Class Profiler
 * @package Codeception\Extension
 */
class Profiler extends Extension
{

    /**
     * @var array
     */
    public static $events = array(
        Events::TEST_BEFORE => 'beforeTest',
        Events::TEST_END => 'endTest',
        Events::RESULT_PRINT_AFTER => 'afterPrint',
    );

    /**
     * @var array
     */
    protected $profile = [];

    /**
     * @var float
     */
    protected $suiteTime;

    /**
     * @var float
     */
    protected $warningTimeLimit = 1;

    /**
     * @var float
     */
    protected $errorTimeLimit = 10;

    /**
     * @param array $config
     * @param array $options
     */
    public function __construct($config, $options)
    {
        parent::__construct($config, $options);

        if (isset($config['warningTimeLimit']) && $config['warningTimeLimit'] > 0) {
            $this->warningTimeLimit = (float)$config['warningTimeLimit'];
        }
        if (isset($config['errorTimeLimit']) && $config['errorTimeLimit'] > 0) {
            $this->errorTimeLimit = (float)$config['errorTimeLimit'];
        }
    }

    /**
     * @param TestEvent $e
     */
    public function beforeTest(TestEvent $e)
    {
        $annotations = $e->getTest()->getAnnotations();
        $this->suiteTime = microtime(true);
        list($class,) = explode('::', $e->getTest()->getTestSignature($e->getTest()));
        if (!isset($annotations['class']['ignoreProfiler']) && !isset($this->profile[$class])) {
            $this->profile[$class] = [];
        }
    }

    /**
     * @param TestEvent $e
     */
    public function endTest(TestEvent $e)
    {
        $annotations = $e->getTest()->getAnnotations();
        $time = microtime(true) - $this->suiteTime;
        $signature = $e->getTest()->getTestSignature($e->getTest());
        list($class, $method) = explode('::', $signature);
        if (!isset($annotations['class']['ignoreProfiler']) && !isset($annotations['method'][$method]['ignoreProfiler'])) {
            $this->profile[$class][$method] = $time;
        }
    }

    /**
     * @param PrintResultEvent $e
     */
    public function afterPrint(PrintResultEvent $e)
    {
        if (!empty($this->profile)) {

            $maxLength = max(array_map('strlen', array_keys($this->profile)));

            $this->writeln('');
            $this->message('TestCases profiling (%d)')
                ->with(count($this->profile))
                ->width($maxLength + 25, '-')
                ->style('bold')
                ->writeln();

            $individualProfiles = [];
            foreach ($this->profile as $class => $profile) {
                $testCaseTime = array_sum($profile);
                $this->writeProfileMessage($class, $testCaseTime, $maxLength + 15);

                foreach ($profile as $method => $time) {
                    $individualProfiles[$class . '::' . $method] = $time;
                }

            }

            arsort($individualProfiles);

            $individualProfiles = array_slice($individualProfiles, 0, 10);
            $maxLength = max(array_map('strlen', array_keys($individualProfiles)));
            $this->writeln('');
            $this->message('Tests profiling (up to 10 slowest)')
                ->width($maxLength + 25, '-')
                ->style('bold')
                ->writeln();

            foreach ($individualProfiles as $signature => $time) {
                $this->writeProfileMessage($signature, $time, $maxLength + 15);
            }

        }
    }

    /**
     * @param string $text
     * @return Message
     */
    protected function message($text = '')
    {
        return new Message($text, $this->output);
    }

    /**
     * @param string|Message$text
     * @param float $time
     * @param int $length
     */
    protected function writeProfileMessage($text, $time, $length = 0)
    {
        $message = $this->message($text);
        if ($length > 0) {
            $message->width($length);
        }
        $message->append($this->message('%.3f sec.')->with(round($time, 4)));
        if ($time > $this->errorTimeLimit) {
            $message->style('error');
        } elseif ($time> $this->warningTimeLimit) {
            $message->style('info');
        }
        $message->writeln();
    }

}