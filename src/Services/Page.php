<?php

namespace WoganMay\DomoPHP\Services;

/**
 * DomoPHP Page.
 *
 * Utility methods for working with pages
 *
 * @author     Wogan May <wogan.may@gmail.com>
 * @link       https://github.com/woganmay/domo-php
 */
class Page
{
    private $Client = null;

    /**
     * oAuth Client ID.
     *
     * The Client ID obtained from developer.domo.com
     *
     * @param \WoganMay\DomoPHP\Client $APIClient An instance of the API Client
     */
    public function __construct(\WoganMay\DomoPHP\Client $APIClient)
    {
        $this->Client = $APIClient;
    }

    /**
     * @param integer $id The Page ID
     * @return mixed
     * @throws \Exception
     */
    public function getPage($id = null)
    {
        if ($id == null)
            throw new \Exception("Need a valid Page ID!");

        return $this->Client->getJSON("v1/pages/$id");
    }

    /**
     * @param string $name Page Name
     * @param array $properties Optional properties to include
     * @return string
     */
    public function createPage($name, $properties = [])
    {
        return $this->Client->postJSON('/v1/pages', array_merge([
            'name' => $name
        ], $properties));
    }

    /**
     * @param integer $id The Page ID
     * @param integer $card_id The ID of the Card to add
     * @return mixed
     */
    public function addCard($id, $card_id)
    {
        $page = $this->getPage($id);

        // Merge new card into existing array
        $cards = $page->cardIds;

        if (!isset($cards[$card_id])) $cards[] = $card_id;

        return $this->updatePage($id, [ 'cardIds' => $cards ]);
    }

    /**
     * @param integer $id The Page ID to update
     * @param array $properties Properties to update on the page
     * @return mixed
     */
    public function updatePage($id, $properties = [])
    {
        return $this->Client->putJSON("/v1/pages/$id", $properties);
    }

    /**
     * @param integer $id The Page ID to delete
     * @return bool
     */
    public function deletePage($id)
    {
        $response = $this->Client->WebClient->delete("/v1/pages/$id", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->Client->getToken(),
            ],
        ]);

        return $response->getStatusCode() == 204;
    }

    /**
     * Get a List of Pages.
     *
     * @param int $limit (Default 10) The number of groups to return
     * @param int $offset (Default 0) Used for pagination
     * @return mixed
     * @throws \Exception
     */
    public function getList($limit = 10, $offset = 0)
    {
        $url = sprintf('/v1/pages?offset=%s&limit=%s', $offset, $limit);

        return $this->Client->getJSON($url);
    }

    /**
     * @param integer $id The page ID to query
     * @param int $limit (Default 10) The number of groups to return
     * @param int $offset (Default 0) Used for pagination
     * @return mixed
     * @throws \Exception
     */
    public function getPageCollections($id, $limit = 10, $offset = 0)
    {
        $url = sprintf('/v1/pages/%s/collections?offset=%s&limit=%s', $id, $offset, $limit);
        return $this->Client->getJSON($url);
    }

    /**
     * @param integer $id The Page ID to create the Collection on
     * @param string $title The title for the new Collection
     * @param array $properties Additional properties (description, cardIds)
     * @return string
     */
    public function createPageCollection($id, $title, $properties = [])
    {
        $url = sprintf('/v1/pages/%s/collections', $id);

        return $this->Client->postJSON($url, array_merge([
            'title' => $title
        ], $properties));
    }

    /**
     * @param integer $id The Page ID
     * @param integer $collection_id The Collection ID to update
     * @param array $properties The updates to apply
     * @return mixed
     */
    public function updatePageCollection($id, $collection_id, $properties = [])
    {
        return $this->Client->putJSON("/v1/pages/$id/collections/$collection_id", $properties);
    }

    /**
     * @param integer $id The Page ID
     * @param integer $collection_id The Collection ID to remove
     * @return bool
     */
    public function deletePageCollection($id, $collection_id)
    {
        $response = $this->Client->WebClient->delete("/v1/pages/$id/collections/$collection_id", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->Client->getToken(),
            ],
        ]);

        return $response->getStatusCode() == 204;
    }


}