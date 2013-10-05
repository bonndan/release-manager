<?php

namespace Liip\RMT\Information;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Collect user info
 */
class InformationCollector
{

    static $standardRequests = array(
        'comment' => array(
            'description' => 'Comment associated with the release',
            'type' => 'text'
        ),
        'type' => array(
            'description' => 'Release type, can be major, minor, patch or current tag.',
            'type' => 'choice',
            'choices' => array('major', 'minor', 'patch', 'current-vcs'),
            'choices_shortcuts' => array('m' => 'major', 'i' => 'minor', 'p' => 'patch', 'c' => 'current-vcs'),
            'default' => 'patch'
        )
    );
    protected $requests = array();
    protected $values = array();

    /**
     * Register an information request.
     * 
     * @param \Liip\RMT\Information\InformationRequest $request
     * @throws \Exception
     */
    public function registerRequest(InformationRequest $request)
    {
        $name = $request->getName();
        if (in_array($name, static::$standardRequests)) {
            throw new \Exception("Request [$name] is reserved as a standard request name, choose an other name please");
        }

        if ($this->hasRequest($name)) {
            throw new \Exception("Request [$name] already registered");
        }

        $this->requests[$name] = $request;
    }

    /**
     * Register a bunch of requests.
     * 
     * @param mixed $list
     * @throws \Exception
     */
    public function registerRequests($list)
    {
        foreach ($list as $request) {
            if (is_string($request)) {
                $this->registerStandardRequest($request);
            } else if ($request instanceof InformationRequest) {
                $this->registerRequest($request);
            } else {
                throw new \Exception("Invalid request, must a Request class or a string for standard requests");
            }
        }
    }

    /**
     * Register a request by name.
     * 
     * @param string $name comment|type
     * @throws \Exception
     */
    public function registerStandardRequest($name)
    {
        if (!in_array($name, array_keys(static::$standardRequests))) {
            throw new \Exception("There is no standard request named [$name]");
        }
        if (!isset($this->requests[$name])) {
            $this->requests[$name] = new InformationRequest($name, static::$standardRequests[$name]);
        }
    }

    /**
     * Returns a registered request.
     * 
     * @return InformationRequest
     */
    public function getRequest($name)
    {
        if (!$this->hasRequest($name)) {
            throw new \InvalidArgumentException("There is no information request named [$name]");
        }
        return $this->requests[$name];
    }

    /**
     * Checks if a request has been registered under the given name.
     * 
     * @param string $name
     * @return boolean
     */
    public function hasRequest($name)
    {
        return isset($this->requests[$name]);
    }

    /**
     * Return a set of command request, converted from the Base Request
     *
     * @return InputOption[]
     */
    public function getCommandOptions()
    {
        $consoleOptions = array();
        foreach ($this->requests as $name => $request) {
            if ($request->isAvailableAsCommandOption()) {
                $consoleOptions[$name] = $request->convertToCommandOption();
            }
        }
        return $consoleOptions;
    }

    public function hasMissingInformation()
    {
        foreach ($this->requests as $request) {
            if (!$request->hasValue()) {
                return true;
            }
        }
        return false;
    }

    public function getInteractiveQuestions()
    {
        $questions = array();
        foreach ($this->requests as $name => $request) {
            if ($request->isAvailableForInteractive() && !$request->hasValue()) {
                $questions[$name] = $request->convertToInteractiveQuestion();
            }
        }
        return $questions;
    }

    public function handleCommandInput(InputInterface $input)
    {
        foreach ($input->getOptions() as $name => $value) {
            if ($this->hasRequest($name) && $this->getRequest($name)->getOption('default') !== $value) {
                $this->getRequest($name)->setValue($value);
            }
        }
    }

    public function setValueFor($requestName, $value)
    {
        return $this->getRequest($requestName)->setValue($value);
    }

    public function getValueFor($requestName, $default = null)
    {
        if ($this->hasRequest($requestName)) {
            return $this->getRequest($requestName)->getValue();
        } else {
            if (func_num_args() == 2) {
                return $default;
            }
            throw new \Exception("No request named $requestName");
        }
    }

}
