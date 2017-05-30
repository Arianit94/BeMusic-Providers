<?php namespace App\Services\Providers\Spotify;

use App\Services\Providers\Spotify\SpotifyHttpClient;
use App\Traits\AuthorizesWithSpotify;

class SpotifyRadio {

    /**
     * HttpClient instance.
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Spotify Search Instance.
     *
     * @var SpotifySearch
     */
    private $spotifySearch;

    /**
     * Create new SpotifyArtist instance.
     */
    public function __construct(SpotifySearch $spotifySearch) {
        $this->httpClient = \App::make('SpotifyHttpClient');
        $this->spotifySearch = $spotifySearch;
    }

    public function getSuggestions($name)
    {
        $response = $this->spotifySearch->search($name, 1, 'artist');

        if ( ! isset($response['artists']) || empty($response['artists'])) return [];

        $spotifyId = $response['artists'][0]['spotify_id'];

        $response = $this->httpClient->get('recommendations',
            [
                'query' => [
                    'seed_artists'  => $spotifyId,
                    'min_popularity' => 30,
                    'limit' => 100,
                ],
            ]
        );

        if ( ! isset($response['tracks'][0])) return [];

        $tracks = [];

        foreach($response['tracks'] as $track) {
            $tracks[] = [
                'name' => $track['name'],
                'artist' => ['name' => $track['artists'][0]['name']],
            ];
        }

        return $tracks;
    }
}