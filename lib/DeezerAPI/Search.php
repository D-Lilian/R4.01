<?php

/**
 * PHP library for using Deezzer API
 *
 * (c) Arnaud Costes <arnaud.costes@gmail.com>
 *
 * MIT License
 */

namespace DeezerAPI;

use DeezerAPI\Models\Track;

/**
 * Search class for Deezer API
 *
 * @author Arnaud COSTES <arnaud.costes@gmail.com>
 */
class Search
{

    const DEEZER_API_URL = 'https://api.deezer.com';

    /**
     * Possible connector value
     *
     * @var array
     */
    protected $typesValues = array(
        'album',
        'artist'
    );

    /**
     * Possible order value
     *
     * @var array
     */
    protected $orderValue = array(
        'RANKING',
        'TRACK_ASC',
        'TRACK_DESC',
        'ARTIST_ASC',
        'ARTIST_DESC',
        'ALBUM_ASC',
        'ALBUM_DESC',
        'RATING_ASC',
        'RATING_DESC',
        'DURATION_ASC',
        'DURATION_DESC',
    );

    /**
     * The query string to search
     *
     * @var string
     */
    protected $query;

    /**
     * The type of search
     *
     * @var string
     */
    protected $type;

    /**
     * The order
     *
     * @var string
     */
    protected $order;

    /**
     * The result of the search
     *
     * @var array
     */
    protected $results = array();

    /**
     * The amount of results
     *
     * @var int
     */
    protected $limit;

    /**
     * Proceed to the next page ?
     *
     * @var boolean
     */
    protected $gotoNextPage;

    /**
     * Next page url
     *
     * @var string
     */
    protected $nextPage = null;
	
    /**
     * Options
     *
     * @var string
     */
    protected $opts ;


    /**
     * Constructor
     *
     * @param string $query
     * @param string $type
     * @param string $order
     * @param int    $limit
     * @param bool   $gotoNextPage
     *
     * @throws DeezerException
     */
    public function __construct($query, $type = null, $order = null, $limit = 25, $gotoNextPage = true)
    {
        if ($type && !in_array(strtolower($type), $this->typesValues)) {
            throw new DeezerException($type . ' is not a valid search type');
        }

        if ($order && !in_array(strtoupper($order), $this->orderValue)) {
            throw new DeezerException($order . ' is not a valid order type');
        }

        $this->query           = $query;
        $this->order           = $order;
        $this->type            = $type;
        $this->limit           = $limit;
        $this->gotoNextPage    = $gotoNextPage;
		
		$ctx = stream_context_create(array(
           "http" => array(
                "timeout" => 1
           )
        )); 
        $response = @file_get_contents("http://www.google.fr",0,$ctx);
        if ($response === false)
            $this->opts = array(
				'ssl'  => array(
                    'verify_peer'      => false,
					'verify_peer_name' => false,
                ),
                "http" => array(
                    "proxy" => "tcp://proxy.univ-lemans.fr:3128"
                ),
            );
        else
            $this->opts = array(
				'ssl'  => array(
                    'verify_peer'      => false,
					'verify_peer_name' => false,
                ),
            );
    }

    /**
     * Search an artist
     */
    public function searchArtist()
    {
        $this->type = 'artist';
        return $this->search();
    }

    /**
     * Search an album
     */
    public function searchAlbum()
    {
        $this->type = 'album';
        return $this->search();
    }
	
    /**
     * Search albums by artist
     */
    public function searchAlbumsByArtist($idArtist = null)
    {
		if ($idArtist == null) {
            throw new DeezerException('Id of artist is null');
        }
		
		$this->type = 'album';
		
        $url = self::DEEZER_API_URL . '/artist/' . $idArtist ."/" . $this->type ."s" ;
		
        $this->results = array() ;
		
        $this->parse($url);

        return $this->getAllData();
    }
	
	
    /**
     * Search tracks by album
     */
    public function searchTracksByAlbum($idAlbum = null)
    {
		if ($idAlbum == null) {
            throw new DeezerException('Id of album is null');
        }
		
		$this->type = 'track';
		
        $url = self::DEEZER_API_URL . '/album/' . $idAlbum  ."/" . $this->type ."s" ;

        $this->results = array() ;
		
        $this->parse($url);

        return $this->getAllData();
    }


    /**
     * Basique search function
     * If no type specified it's search for tracks
     *
     * @return array
     */
    public function search()
    {
        $queryString = array(
            'q'     => $this->query,
            'limit' => $this->limit,
        );

        if ($this->order) {
            $queryString['order'] = $this->order;
        }

        $url = self::DEEZER_API_URL . '/search/' . $this->type . '?' . http_build_query($queryString);

        $this->results = array() ;
		
        $this->parse($url);

        return $this->getAllData();
    }

    /**
     * Define the search order
     *
     * @param string
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Parse the search results
     *
     * @param string
     */
    protected function parse($url)
    {
        $context = stream_context_create($this->opts);
        $results = json_decode(file_get_contents($url,false,$context));
        if (isset($results->error)) {
            throw new DeezerException($results->error->message, $results->error->code);
        }
		
        foreach ($results->data as $result) {
			array_push($this->results, $result);
        }

        if (isset($results->next) && $this->gotoNextPage === true) {
            $this->parse($results->next);
        }
    }

    /**
     * Retrieve all datas from the search
     *
     * @return array
     */
    protected function getAllData()
    {
				
        $data = array();
        foreach ($this->results as $result) {
            $class = 'DeezerAPI\Models\\' . ucfirst($result->type);
            $model = new $class($result);
            array_push($data, $model);
        }
        return $data;
    }
}
