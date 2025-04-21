<?php

declare(strict_types=1);

namespace Exonet\Api;

use Exonet\Api\Auth\AbstractAuth;
use Exonet\Api\Exceptions\AuthenticationException;
use Exonet\Api\Structures\ApiResourceIdentifier;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * The Exonet API Client 'starting point'.
 */
class Client implements LoggerAwareInterface
{
    /**
     * The version of this package. Used in the user-agent header.
     */
    public const CLIENT_VERSION = 'v3.2.2';

    /**
     * The API base URL.
     */
    public const API_URL = 'https://api.exonet.nl/';

    /**
     * The API test base URL.
     */
    public const API_TEST_URL = 'https://test-api.exonet.nl/';

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
     * @var string The API URL to connect to.
     */
    private $apiUrl;

    /**
     * Client constructor.
     *
     * @param AbstractAuth|null $auth   If provided, set the auth instance.
     * @param string|null       $apiUrl If provided, set the host URL
     */
    public function __construct(?AbstractAuth $auth = null, ?string $apiUrl = null)
    {
        if ($auth) {
            $this->setAuth($auth);
        }

        $this->setApiUrl($apiUrl ?? self::API_URL);

        if (!isset(self::$_instance)) {
            self::$_instance = $this;
        }
    }

    /**
     * Implement the singleton pattern so the client is shared.
     *
     * @return Client The cache instance.
     */
    public static function getInstance(): self
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
    public function getAuth(): AbstractAuth
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
    public function setAuth(AbstractAuth $auth): self
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Get the API URL to connect to.
     *
     * @return string The API URL to connect to.
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * Set the API URL to use.
     *
     * @param string $apiUrl The API Url to use.
     *
     * @return self The current Client instance.
     */
    public function setApiUrl(string $apiUrl): self
    {
        if (substr($apiUrl, -1) !== '/') {
            $apiUrl .= '/';
        }

        $this->apiUrl = $apiUrl;

        return $this;
    }

    /**
     * Get the logger instance.
     *
     * @return LoggerInterface The log instance.
     */
    public function log(): LoggerInterface
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
     * @param LoggerInterface $logger The log instance to use.
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Easy way to start building a new API request for a resource.
     *
     * Gives a basic request to start making API calls or a resource identifier when an ID is provided.
     *
     * @param string      $resourceType The resource name.
     * @param string|null $id           The optional resource ID.
     *
     * @return ApiResourceIdentifier|Request A request or resource identifier for the specified resource.
     */
    public function resource(string $resourceType, ?string $id = null)
    {
        $this->log()->debug('Starting new request', ['resource' => $resourceType]);

        if ($id !== null) {
            return new ApiResourceIdentifier($resourceType, $id);
        }

        return new Request($resourceType);
    }
}
