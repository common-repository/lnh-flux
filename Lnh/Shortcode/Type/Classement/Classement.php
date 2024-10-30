<?php
namespace Lnh\Shortcode\Type\Classement;

use Lnh\Shortcode\Sanitize;
use Lnh\Shortcode\ShortcodeInterface;
/**
 * Shortcode permettant l'affichage du classement en cours.
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Classement extends Sanitize implements ShortcodeInterface
{
    
    /**
     * Traitement du shortcode
     * 
     * @param array $atts
     * @param string $content
     * @throws \Exception
     */
    public function filter($atts, $content = null)
    {
        if(isset($atts['saison'])) $saison = $atts['saison'];
        else $saison = self::getCurrentSaison();
        
        if(isset($_GET['saison'])){
            $saison = $_GET['saison'];
        }
        
        if($saison != (int)$saison || strlen($saison) != 8){
            throw new \Exception('Le format des saisons n\'est pas correct.');
        }
        
        $classement = self::getClassement($saison);
        
        
        
        
        $html = self::getFormSaison($saison);
        
        if(!$classement){
            $html .=  '<p>Classement non disponible.</p>';
            
            return $html;
        }
        
        
        $equipe_plugin = get_option('lnh_equipe_handball');
        
        $html .= '<div class="classement">';
        
        $html .= '<div class="ligne">
                        <div>&nbsp;</div>
                        <div>&nbsp;</div>
                        <div>&nbsp;</div>
                        <div>PTS</div>
                        <div class="hidden_mobile">
                                <div>PTS Pén *</div>
                                <div>MJ</div>
                                <div>VICT.</div>
                                <div>NUL</div>
                                <div>Déf.</div>
                                <div>BUTS POUR</div>
                                <div>BUTS CONTRE</div>
                                <div>GOAL AVG *</div>
                                <div>PART. PTS *</div>
                                <div>PART GOALS *</div>
                        </div>
                </div>';
        
        foreach($classement as $team){
            
            $goal_avg = (int)get_post_meta($team->ID, 'totalButPour', true) - (int)get_post_meta($team->ID, 'totalButContre', true);
            
            //var_dump(get_option('lnh_equipe_handball')); die();
            if(strtolower($equipe_plugin['text_string']) == strtolower(get_post_meta($team->ID, 'equipeNom', true))){
                $actif = " actif";
            } else $actif = '';
            
            $html .= '<div class="ligne'.$actif.'">
                            <div>'. get_post_meta($team->ID, 'position', true) .'</div>
                            <div><img src="'. get_post_meta($team->ID, 'equipeLogo', true) .'" alt="'. get_post_meta($team->ID, 'equipeNom', true) .'"></div>
                            <div>'. get_post_meta($team->ID, 'equipeNom', true) .'</div>
                            <div>'. get_post_meta($team->ID, 'totalPoint', true) .'</div>
                            <div class="hidden_mobile">
                                    <div>0</div>
                                    <div>26</div>
                                    <div>'. get_post_meta($team->ID, 'totalVictoire', true) .'</div>
                                    <div>'. get_post_meta($team->ID, 'totalNul', true) .'</div>
                                    <div>'. get_post_meta($team->ID, 'totalDefaite', true) .'</div>
                                    <div>'. get_post_meta($team->ID, 'totalButPour', true) .'</div>
                                    <div>'. get_post_meta($team->ID, 'totalButContre', true) .'</div>
                                    <div>'.$goal_avg.'</div>
                                    <div>0</div>
                                    <div>0</div>
                            </div>
                    </div>';
        }
        
        $html .= '';
        
        return $html;
    }
    
    /**
     * Retourne le classement de la saison spécifiée
     * 
     * @param string $saison saison au format 20142015
     * @param string $order ASC ou DESC
     * @return array|false
     */
    private static function getClassement($saison, $order = 'ASC')
    {
        $post = get_posts(
                    array(
                        'post_type' => 'lnh_classement',
                        'posts_per_page' => -1,
                        'meta_key' => 'lnh_classement_saison',
                        'meta_value' => $saison,
                        'orderby' => 'position',
                        'order' => $order
                    )
                );
        
        if($post)
            return $post;
        else
            return false;
    }
    
    /**
     * Retourne la saison courante
     * 
     * @return string
     */
    public static function getCurrentSaison()
    {
        $time = time();
        
        $year1 = date('Y', $time);
        
        if(date('m', $time) >= 8){
            $year2 = $year1+1;
        } else {
            $year2 = date('Y', $time);
            $year1 = $year2-1;
        }
        
        return $year1.$year2;
    }
    
    /**
     * Retourne les saisons disponibles
     * 
     * @return string
     */
    private static function getAvalaibleSaison()
    {
        $post = get_posts(
                    array(
                        'post_type' => 'lnh_classement',
                        'posts_per_page' => -1
                    )
                );
        
        $saison = array();
        
        $current_saison = self::getCurrentSaison();
        
        $saison[$current_saison] = substr($current_saison, 0, 4).' / '.substr($current_saison, 4);
        
        foreach($post as $team){
            
            $saison_classement = get_post_meta($team->ID, 'lnh_classement_saison', true);
            
            if(!empty($saison_classement)) $saison[$saison_classement] = substr($saison_classement, 0, 4).' / '.substr($saison_classement, 4);
            
        }
        
        return $saison;
    }
    
    
    private static function getFormSaison($saison_selected = null)
    {
        $form = '<p><form method="get" action="#_" class="filtres">';
        
        $form .= '<label>Filtres</label>';
        
        $form .= '<select name="saison" id="saison">';
        
        $saison = self::getAvalaibleSaison();
        //var_dump($saison);
        $form .= '<option value="'.self::getCurrentSaison().'">Saison</option>';
        
        foreach($saison as $value => $label){
            $selected = null;

            if(isset($_GET['saison']) && isset($saison_selected) && $saison_selected == $value){
                $selected = ' selected';
            }
            
            $form .= '<option value="'.$value.'"'.$selected.'>'.$label.'</option>';
        }
        
        $form .= '</select>';
        
        $form .= '<input type="submit" id="submit_saison" value="→ Ok" />';
        
        $form .= '</form></p>';
        
        return $form;
    }
    
}
