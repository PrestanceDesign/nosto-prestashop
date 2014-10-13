<?php

/**
 * Helper class for doing OAuth2 authentication with Nosto.
 * The client implements the 'Authorization Code' grant type.
 */
class NostoTaggingOAuth2Client
{
	const NOSTOTAGGING_OAUTH2_CLIENT_BASE_URL = 'https://my.nosto.com/oauth';
	const NOSTOTAGGING_OAUTH2_CLIENT_ID = 'prestashop';
	const NOSTOTAGGING_OAUTH2_CLIENT_AUTH_PATH = '/authorize?client_id={cid}&redirect_uri={uri}&response_type=code';
	const NOSTOTAGGING_OAUTH2_CLIENT_TOKEN_PATH = '/token?code={cod}&client_id={cid}&redirect_uri={uri}&grant_type=authorization_code';

	/**
	 * @var string the client id the identify this application to the oauth2 server.
	 */
	protected $client_id = self::NOSTOTAGGING_OAUTH2_CLIENT_ID;

	/**
	 * @var string the client secret the identify this application to the oauth2 server.
	 */
	protected $client_secret;

	/**
	 * @var string the redirect url that will be used by the oauth2 server when authenticating the client.
	 */
	protected $redirect_url;

	/**
	 * Setter for the client id to identify this application to the oauth2 server.
	 *
	 * @param string $client_id the client id.
	 */
	public function setClientId($client_id)
	{
		$this->client_id = $client_id;
	}

	/**
	 * Setter for the client secret to identify this application to the oauth2 server.
	 *
	 * @param string $client_secret the client secret.
	 */
	public function setClientSecret($client_secret)
	{
		$this->client_secret = $client_secret;
	}

	/**
	 * Setter for the redirect url that will be used by the oauth2 server when authenticating the client.
	 *
	 * @param string $redirect_url the redirect url.
	 */
	public function setRedirectUrl($redirect_url)
	{
		$this->redirect_url = $redirect_url;
	}

	/**
	 * Returns the authentication url to the oauth2 server.
	 *
	 * @return string the url.
	 */
	public function getAuthorizationUrl()
	{
		return NostoTaggingHttpRequest::build_uri(
			self::NOSTOTAGGING_OAUTH2_CLIENT_BASE_URL.self::NOSTOTAGGING_OAUTH2_CLIENT_AUTH_PATH,
			array(
				'{cid}' => $this->client_id,
				'{uri}' => $this->redirect_url
			)
		);
	}

	/**
	 * Authenticates the application with the given code to receive an access token.
	 *
	 * @param string $code code sent by the authorization server to exchange for an access token.
	 * @return NostoTaggingOAuth2Token|bool a token or false.
	 */
	public function authenticate($code)
	{
		if (empty($code))
		{
			NostoTaggingLogger::log(
				__CLASS__.'::'.__FUNCTION__.' - Invalid authentication token.',
				NostoTaggingLogger::LOG_SEVERITY_ERROR,
				500
			);
			return false;
		}

		$request = new NostoTaggingHttpRequest();
		$url = NostoTaggingHttpRequest::build_uri(
			self::NOSTOTAGGING_OAUTH2_CLIENT_BASE_URL.self::NOSTOTAGGING_OAUTH2_CLIENT_TOKEN_PATH,
			array(
				'{cid}' => self::NOSTOTAGGING_OAUTH2_CLIENT_ID,
				'{uri}' => $this->redirect_url,
				'{cod}' => $code
			)
		);
		$response = $request->get($url);
		$result = $response->getJsonResult(true);

		if ($response->getCode() !== 200)
		{
			NostoTaggingLogger::log(
				__CLASS__.'::'.__FUNCTION__.' - Failed to authenticate with code.',
				NostoTaggingLogger::LOG_SEVERITY_ERROR,
				$response->getCode()
			);
			return false;
		}

		if (empty($result['access_token']))
		{
			NostoTaggingLogger::log(
				__CLASS__.'::'.__FUNCTION__.' - No "access_token" returned after authenticating with code.',
				NostoTaggingLogger::LOG_SEVERITY_ERROR,
				$response->getCode()
			);
			return false;
		}

		if (empty($result['merchant_name']))
		{
			NostoTaggingLogger::log(
				__CLASS__.'::'.__FUNCTION__.' - No "merchant_name" returned after authenticating with code.',
				NostoTaggingLogger::LOG_SEVERITY_ERROR,
				$response->getCode()
			);
			return false;
		}

		return NostoTaggingOAuth2Token::create($result);
	}
} 