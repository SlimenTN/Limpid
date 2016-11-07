<?php
namespace framework\core\Router;

/**
 * Class Route
 * Object that contains routes's properties such as name, pattern and command's path
 * @package framework\core\Router
 * 
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class Route{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var string
     */
    private $commandPath;
    
    public function __construct($name, $pattern, $commandPath){
        $this->name = $name;
        $this->pattern = $pattern;
        $this->commandPath = $commandPath;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getCommandPath()
    {
        return $this->commandPath;
    }

    /**
     * @param string $commandPath
     */
    public function setCommandPath($commandPath)
    {
        $this->commandPath = $commandPath;
    }
    
    
}