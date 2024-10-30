<?php
namespace Lnh\Flux\Equipe\Joueur;

use Lnh\Flux\Item\Item;
/**
 * caractéristique joueur
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Joueur extends Item
{
    public $nom;
    
    public $prenom;
    
    public $dateNaissanceSmall;
    
    public $dateNaissanceFull;
    
    public $paysNom;
    
    public $sexe;
    
    public $taille;
    
    public $poids;
    
    public $droiteGauche;
    
    public $posteNom;
    
    public $groupeCode;
    
    public $photo;
    
    public $url;
    
//    public $nbPlayed;
//    
//    public $playerGoals;
//    
//    public $playerAttempts;
//    
//    public $goalkeeperStops;
//    
//    public $goalkeeperConceded;
//    
//    public $nbTurnovers;
//    
//    public $nbTwoMin;
//    
//    public $nbEjected;
    
    
    public $lnh_classement_saison;
    
    public function setSaison()
    {
        $time = time();
        
        $year1 = date('Y', $time);
        
        if(date('m', $time) >= 8){
            $year2 = $year1+1;
        } else {
            $year2 = date('Y', $time);
            $year1 = $year2-1;
        }
        
        $this->lnh_classement_saison = $year1.$year2;
        
        return $year1.$year2;
    }
    
    
    /**
     * Va extraire les données du flux XML vers les propriétés de la classe
     */
    protected function extractData()
    {
        
        foreach ($this->xml_element as $element => $value) {
            
            if (property_exists($this, $element)) {
                $this->$element = (string) $value;
                
            } else if($element === 'stats') {
                
                foreach($value as $elementStats => $valueStats ){
                    if (property_exists($this, $elementStats)) {
                        $this->$elementStats = (string) $valueStats;
                    }
                }
            }
        }
        
        
        $this->setSaison();
    }
    
}
