<?php
namespace framework\core\Exception;


class NotFoundException extends LimpidException
{
    /**
     * Exception message
     * @var string
     */
    protected $message;

    const STATUS = 404;

    function __construct($message)
    {
        parent::__construct($message, self::STATUS);
    }

    protected function defaultErrorPage()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " ".self::STATUS." Not Found", true, $this->status);

        echo '<h1>404 Page not found</h1>
                    <p>' . $this->message . '</p>';
        exit();
    }
}