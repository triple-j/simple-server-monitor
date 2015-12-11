<?php
namespace trejeraos\SparkTest\Auth;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Spark\Auth\AuthHandler;
use Spark\Auth\AdapterInterface;
use Spark\Auth\Credentials\ExtractorInterface as CredentialsExtractor;
use Spark\Auth\Token\ExtractorInterface as TokenExtractor;
use Spark\Auth\Exception\UnauthorizedException;

class FooHandler extends AuthHandler
{
    protected $config;
    protected $auth_type = 0;

    protected $offered_token;
    protected $offered_credentials;

    // Authentication Types
    const NONE = 0;
    const TOKEN = 1;
    const CREDENTIALS = 2;

    public function __construct(
        TokenExtractor $token,
        CredentialsExtractor $credentials,
        AdapterInterface $adapter
    ) {
        parent::__construct($token, $credentials, $adapter);
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $this->offered_token       = null;
        $this->offered_credentials = null;

        if ($token = $this->token->getToken($request)) {
            $this->auth_type = self::TOKEN;
            $this->offered_token = $token;
        } elseif ($credentials = $this->credentials->getCredentials($request)) {
            $this->auth_type = self::CREDENTIALS;
            $this->offered_credentials = $credentials;
        } else {
            $this->auth_type = self::NONE;
        }

        $response = $next($request, $response);
        return $response;
    }

    /**
     * @return \Spark\Auth\Token
     * @throws \Spark\Auth\Exception\InvalidException if an invalid auth token
     *         is specified
     * @throws \Spark\Auth\Exception\AuthException if another error occurs
     *         during authentication
     * @throws \Spark\Auth\Exception\UnauthorizedException if no token or
     *         credentials are specified
     */
    public function authenticate() {
        if ($this->auth_type === self::TOKEN) {
            // validate token
            $authToken = $this->adapter->validateToken($this->offered_token);
        } elseif ($this->auth_type === self::CREDENTIALS) {
            // validate credentials
            $authToken = $this->adapter->validateCredentials($this->offered_credentials);
        } else {
            throw new UnauthorizedException;
        }

        return $authToken;
    }
}
