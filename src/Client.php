<?php

declare(strict_types=1);

namespace Exonet\Api;

use Exonet\Api\Auth\AbstractAuth;
use Exonet\Api\Exceptions\AuthenticationException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * The Exonet API Client 'starting point'.
 */
class Client implements LoggerAwareInterface
{
    /**
     * The version of this package. This is being used for the user-agent header.
     */
    public const CLIENT_VERSION = 'v0.1.0';

    /**
     * @var Client The client instance.
     */
    private static $_instance;

    /**
     * @var AbstractAuth The auth instance.
     */
    private $auth;

    /**
     * @var LoggerInterface The logger instance.
     */
    private $logger;

    /**
     * Client constructor.
     *
     * @param AbstractAuth|null $auth If provided, set the auth instance.
     */
    public function __construct(?AbstractAuth $auth = null)
    {
        if ($auth) {
            $this->setAuth($auth);
        }

        if (!isset(self::$_instance)) {
            self::$_instance = $this;
        }
    }

    /**
     * Implement the singleton pattern so the client is shared.
     *
     * @return Client The cache instance.
     */
    public static function getInstance() : self
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Implement the singleton pattern to share the $auth instance between all calls in a request.
     *
     * @throws AuthenticationException If there is no authentication method set.
     *
     * @return AbstractAuth The auth instance.
     */
    public function getAuth() : AbstractAuth
    {
        if ($this->auth === null) {
            $this->log()->error('No authentication method set.');

            throw new AuthenticationException('No authentication method set.');
        }

        return $this->auth;
    }

    /**
     * Set the auth instance to use.
     *
     * @param AbstractAuth $auth The auth instance to use.
     *
     * @return self The current Client instance.
     */
    public function setAuth(AbstractAuth $auth) : self
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Get the logger instance.
     *
     * @return LoggerInterface The log instance.
     */
    public function log() : LoggerInterface
    {
        if ($this->logger === null) {
            // If there's no logger set, use the NullLogger.
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * Set the logger instance to use.
     *
     * @param LoggerInterface $log The log instance to use.
     *
     * @return self The current Client instance.
     */
    public function setLogger(LoggerInterface $log) : self
    {
        $this->logger = $log;

        return $this;
    }

    /**
     * Easy way to start building a new API request for the specified resource.
     *
     * @param string $resource The resource name.
     *
     * @return Request The request instance to use.
     */
    public function resource(string $resource) : Request
    {
        $this->log()->debug('Starting new request', ['resource' => $resource]);

        return new Request($resource);
    }
}
