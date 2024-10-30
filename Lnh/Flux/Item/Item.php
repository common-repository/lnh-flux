<?php
namespace Lnh\Flux\Item;

/**
 * Instance d'un item du flux XML
 * Va permettre de gérer facilement les données parsées du flux
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Item
{
    protected $xml_element;
    
    public $competitionCode;
    
    public $competitionNom;

    public $matchCode;

    public $journee;

    public $equipeDomicile;

    public $equipeDomicileLogo;

    public $scoreDomicile;

    public $equipeExterieur;

    public $equipeExterieurLogo;

    public $scoreExterieur;

    public function __construct(\SimpleXMLElement $xml_element)
    {
        $this->xml_element = $xml_element;

        $this->extractData();
    }

    /**
     * Va extraire les données du flux XML vers les propriétés de la classe
     */
    protected function extractData()
    {
        foreach ($this->xml_element as $element => $value) {
            if (property_exists($this, $element)) {
                $this->$element = (string) $value;
            }
        }
        
        if(method_exists($this, 'setSaison')){
            $this->setSaison();
        }
    }
}
