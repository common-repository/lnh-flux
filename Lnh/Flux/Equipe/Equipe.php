<?php
namespace Lnh\Flux\Equipe;

use Lnh\Flux\Item\Item;
/**
 * Item d'équipe dans le classement
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Equipe extends Item
{
    public $position;

    public $equipeNom;

    public $equipeLogo;

    public $totalMatch;

    public $totalMatchDomicile;

    public $totalMatchExterieur;

    public $totalVictoire;

    public $totalNul;

    public $totalDefaite;

    public $totalPoint;

    public $totalPointDomicile;

    public $totalPointExterieur;

    public $totalButPour;

    public $totalButPourDomicile;

    public $totalButPourExterieur;

    public $totalButContre;

    public $totalButContreDomicile;

    public $totalButContreExterieur;
    
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
        
        //$this->lnh_classement_saison = 20142015;
        
        return $year1.$year2;
    }
}
