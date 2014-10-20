<?php

/**
 * Helper class for doing API requests to the Nosto REST API.
 */
class NostoTaggingApiRequest extends NostoTaggingHttpRequest
{
	const BASE_URL = 'https://api.nosto.com';

	const PATH_ORDER_TAGGING = '/visits/order/confirm/{m}/{cid}';
	const PATH_UNMATCHED_ORDER_TAGGING = '/visits/order/unmatched/{m}';
	const PATH_SIGN_UP = '/accounts/create';
	const PATH_SSO_AUTH = '/users/{email}';

	const TOKEN_SIGN_UP = 'JRtgvoZLMl4NPqO9XWhRdvxkTMtN82ITTJij8U7necieJPCvjtZjm5C4fpNrYJ81';

	/**
	 * Setter for the end point path, e.g. one of the PATH_ constants.
	 * The API base url is always prepended.
	 *
	 * @param string $path the endpoint path (use PATH_ constants).
	 */
	public function setPath($path)
	{
		$this->setUrl(self::BASE_URL.$path);
	}
}