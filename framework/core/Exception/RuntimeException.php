<?php
namespace framework\core\Exception;


class RuntimeException extends LimpidException
{
    /**
     * Exception message
     * @var string
     */
    protected $message;

    const STATUS = 500;
    
    function __construct($message)
    {
        $this->message = $message;
        parent::__construct($this->message, self::STATUS);
    }

    protected function defaultErrorPage()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " ".self::STATUS." Server error", true, $this->status);

        echo '<h1>500 Server error</h1>
                    <p>' . $this->message . '</p>';
        exit();
    }
}