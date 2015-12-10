<?php

namespace Helper;

use Codeception\Module;
use Codeception\Util\Stub as StubUtil;

/**
 * Class Unit
 * @package Helper
 */
class Unit extends Module
{

    /**
     * @var array
     */
    protected $stubsToVerify = [];

    /**
     * @param object $object
     * @param string $propertyName
     * @return mixed
     */
    public function getProtectedProperty($object, $propertyName)
    {
        $class = new \ReflectionObject($object);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    /**
     * @param object $object
     * @param string $propertyName
     * @param mixed $propertyValue
     */
    public function setProtectedProperty($object, $propertyName, $propertyValue)
    {
        $class = new \ReflectionObject($object);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $propertyValue);
    }

    /**
     * @param object $stub
     * @return $this
     */
    public function addStubToVerify($stub)
    {
        $this->stubsToVerify[] = $stub;
        return $this;
    }

    /**
     * @return $this
     */
    public function verifyStubs()
    {
        /** @var StubUtil $stub */
        foreach ($this->stubsToVerify as $stub) {
            $stub->__phpunit_getInvocationMocker()->verify();
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function clearStubsToVerify()
    {
        $this->stubsToVerify = [];
        return $this;
    }

    /**
     * @param string $file
     * @param boolean $include file as raw php data
     * @return string|mixed
     */
    public function getData($file, $include = false)
    {
        $file = CODECEPTION_DATA_PATH . $file;

        if ($include === true) {
            return include $file;
        }

        return file_get_contents($file);
    }
}
