<?php namespace App\Services\Providers\Local;

use App\Genre;
use App\Services\Providers\Lastfm\LastfmGenres;
use App\Services\Settings;
use Illuminate\Database\Eloquent\Collection;

class LocalGenres {

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var Genre
     */
    private $genre;

    /**
     * @var LastfmGenres
     */
    private $lastfmGenres;

    /**
     * Create new LocalGenres instance.
     *
     * @param Settings $settings
     * @param Genre $genre
     * @param LastfmGenres $lastfmGenres
     */
    public function __construct(Settings $settings, Genre $genre, LastfmGenres $lastfmGenres)
    {
        $this->genre = $genre;
        $this->settings = $settings;
        $this->lastfmGenres = $lastfmGenres;
    }

    /**
     * Get genres using local provider.
     *
     * @return Collection
     */
    public function getGenres() {
        $names = json_decode($this->settings->get('homepage.genres'), true);

        if ( ! $names) return collect();

        $genres = $this->genre->whereIn('name', $names)->get()->map(function(Genre $genre) {
            $genre['image'] = $this->lastfmGenres->getLocalImagePath($genre->name);
            return $genre;
        });

        return $genres->sort(function(Genre $current, Genre $previous) use($names) {
            return array_search($current->name, $names) > array_search($previous->name, $names);
        })->values();
    }

    public function getGenreArtists(Genre $genre)
    {
        return null;
    }
}