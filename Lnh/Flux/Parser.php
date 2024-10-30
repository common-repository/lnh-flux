<?php
namespace Lnh\Flux;

use Lnh\Flux\Item\Item;
use Lnh\Flux\Item\Season\Season;
use Lnh\Flux\Equipe\Equipe;
use Lnh\Flux\Equipe\Joueur\Joueur;
/**
 * Va parser le XML venant de la LNH
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Parser
{
    private $url_flux;

    private $parsed_data;

    private $collection_items = array();

    public function __construct($url_flux)
    {
        $this->url_flux = $url_flux;
    }

    /**
     * Parse les données provenant d'une URL
     *
     * @return SimpleXMLElement
     */
    public function parse()
    {
        $this->parsed_data = new \SimpleXMLElement($this->url_flux, 0, true);

        return $this->parsed_data;
    }

    /**
     * Va parser les données dans une entité connue et plus simple d'utilisation
     *
     * @return array
     */
    public function getItemsCollection()
    {
        foreach ($this->parsed_data as $element => $item) {
            if (property_exists($item->children(), 'equipeDomicileScore')) {
                //calendrier saison
                $data = new Season($item);
            } elseif (property_exists($item->children(), 'totalMatch')) {
                $data = new Equipe($item);
            } elseif ($element == 'joueur') {
                $data = new Joueur($item);
            } else {
                $data = new Item($item);
            }

            if(isset($data)) $this->collection_items[] = $data;
        }

        return $this->collection_items;
    }
}
