<?php
namespace trejeraos\SparkTest\Auth;

use Spark\Auth\AdapterInterface;
use Spark\Auth\Credentials;
use Spark\Auth\Token;
use Spark\Auth\Exception\InvalidException;
use Spark\Auth\Exception\AuthException;
use trejeraos\SparkTest\Configuration;

## Based on -- https://www.webstreaming.com.ar/articles/php-slim-token-authentication/

class Authenticator implements AdapterInterface
{
    /**
     * @var \Spark\Auth\Credentials
     */
    protected $credentials;

    public function __construct(Configuration $config)
    {
        $this->credentials = $config->getCredentials();
    }

    /**
     * Validates a specified authentication token.
     *
     * - If the specified token is invalid, an InvalidException instance is
     *   thrown.
     * - If a valid token is present, a corresponding Token instance is
     *   returned.
     * - If for some reason the token cannot be validated, an AuthException
     *   instance is thrown.
     *
     * @param string $token
     * @return \Spark\Auth\Token
     * @throws \Spark\Auth\Exception\InvalidException if an invalid auth token
     *         is specified
     * @throws \Spark\Auth\Exception\AuthException if another error occurs
     *         during authentication
     */
    public function validateToken($token)
    {
        var_dump($token);

        return new Token("bob", array());
    }

    /**
     * Validates a set of user credentials.
     *
     * - If the user credentials are valid, a new authentication token is
     *   created and a corresponding Token instance is returned.
     * - If the user credentials are invalid, an InvalidException instance is
     *   thrown.
     * - If for some reason the user credentials cannot be validated, an
     *   AuthException instance is thrown.
     *
     * @param \Spark\Auth\Credentials $credentials
     * @return \Spark\Auth\Token
     * @throws \Spark\Auth\Exception\InvalidException if an invalid auth token
     *         is specified
     * @throws \Spark\Auth\Exception\AuthException if another error occurs
     *         during authentication
     */
    public function validateCredentials(Credentials $credentials)
    {
        var_dump($credentials);

        $offered_identifier = $credentials->getIdentifier();
        $offered_password   = $credentials->getPassword();

        $expected_identifier = $this->credentials->getIdentifier();
        $expected_password   = $this->credentials->getPassword();

        if (
            $offered_identifier == $expected_identifier
            && $offered_password == $expected_password
        ) {
            // generate a random token string
            $token_string = bin2hex(openssl_random_pseudo_bytes(16));

            $metadata = array(
                'username'   => $offered_identifier, // just for reference
                'expiration' => date('Y-m-d H:i:s', strtotime('+1 hour')) // the expiration date will be in one hour from the current moment
            );

            $token = new Token($token_string, $metadata);

            // update the token in the database and set the expiration date-time
            #$this->updateToken($token);
        } else {
            throw new InvalidException;
        }



        return $token;
    }
}
