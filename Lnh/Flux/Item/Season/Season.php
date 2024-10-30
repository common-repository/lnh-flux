<?php
namespace Lnh\Flux\Item\Season;

use Lnh\Flux\Item\Item;
/**
 * Item du calendrier d'une saison
 * différent d'un item de match...
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Season extends Item
{
    public $equipeExterieurScore;

    public $equipeDomicileScore;

    public $dateUK;

    public $dateFR;

    public $dateSmall;

    public $dateFull;

    public $heure;

    public $salleNom;

    public $salleVille;

    public $teleNom;

    public $teleLogo;
    
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
