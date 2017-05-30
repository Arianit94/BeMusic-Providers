<?php namespace App\Services\Providers\Spotify;

use App;
use App\Services\HttpClient;
use App\Traits\AuthorizesWithSpotify;

class SpotifyHttpClient extends HttpClient {

	use AuthorizesWithSpotify;

	public function __construct($params = [], $showFeedback = false) {
		parent::__construct($params, $showFeedback);
		$this->authorize();
	}

	public function get($url, $options = []) {
		return parent::get($url, ['headers' => ['Authorization' => 'Bearer '.$this->token]]);
	}

}