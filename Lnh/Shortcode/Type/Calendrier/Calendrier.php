<?php
namespace Lnh\Shortcode\Type\Calendrier;

use Lnh\Shortcode\Sanitize;
use Lnh\Shortcode\ShortcodeInterface;
/**
 * Shortcode de calendrier LNH
 * Trier par mois.
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Calendrier extends Sanitize implements ShortcodeInterface
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
        
        
        if(isset($atts['competition'])) $competition = $atts['competition'];
        else $competition = null;
        
        if(isset($_GET['competition']) && !empty($_GET['competition'])){
            $competition = $_GET['competition'];
        }
        
        $query_matches = self::getMatches($saison, $competition);
        
        
        
        
        $html = '';
        
        $prev_mounth = '';
        
        $html .= self::getFormSaison($saison, $competition);
        
        if(!$query_matches->have_posts()){
            $html .= '<p>Calendrier non disponible.</p>';
            
            return $html;
        }
        
        $i = 0;
        while($query_matches->have_posts()){
            $query_matches->the_post();
            
            $date_match_uk = get_post_meta(get_the_ID(), 'dateUK', true);
            $time_match = strtotime($date_match_uk);
            
            if(date('m', $time_match) != $prev_mounth){
                $prev_mounth = date('m', $time_match);
                
                setlocale(LC_TIME, 'fr_FR.utf8','fra');
                date_default_timezone_set( 'Europe/Paris' );
                
                if($i){
                   $html .= '</div>'; 
                }
                $html .= '<h2 class="mois"><span>'. strftime('%B', $time_match) . ' ' .date('Y', $time_match).'</span></h2>';
                $html .= '<div class="calendrier">';
                $i++;
            }
            
            //$html .= '<p>'.get_the_title().' ('.$date_match_uk.')</p>';
            
            $time_uk = strtotime($date_match_uk);
            
            $score_domicile = '--';
            
            $score_exterieur = '--';
            
            if(get_post_meta(get_the_ID(), 'equipeDomicileScore', true)){
                $score_domicile = get_post_meta(get_the_ID(), 'equipeDomicileScore', true);
            }
            
            if(get_post_meta(get_the_ID(), 'equipeExterieurScore', true)){
                $score_exterieur = get_post_meta(get_the_ID(), 'equipeExterieurScore', true);
            }
            
            $journee = get_post_meta(get_the_ID(), 'journee', true);
            if(preg_match('#journée#i', $journee)){
                $aJournee = explode(' ', $journee);
                $journee = trim($aJournee[count($aJournee)-1]);
            }

            $journee = str_replace(' ', '-', $journee);
            
            $saison = self::getSaison($date_match_uk);
            
            $url_stat = '#_';
            if($time_uk < time()){
                $url_stat = 'http://www.lnh.fr/calendrier/'
                        .$saison.'/'
                        .strtolower(get_post_meta(get_the_ID(), 'competitionCode', true)).'/'
                        .$journee.'/'
                        .strtolower(remove_accents(get_post_meta(get_the_ID(), 'equipeDomicile', true))).'/'
                        .strtolower(remove_accents(get_post_meta(get_the_ID(), 'equipeExterieur', true)));
            }
            
            
            
            
            $lien_article = get_post_meta(get_the_ID(), 'lnh_match_article', true);
            $lien_photo = get_post_meta(get_the_ID(), 'lnh_match_photo', true);
            $lien_video = get_post_meta(get_the_ID(), 'lnh_match_video', true);
            
            $links = '';

            if($lien_article) $links .= '<a href="'.$lien_article.'" target="_blank" class="article icon-article"></a>';
            else $links .= '<a class="article icon-article disable"></a>';

            if($lien_video) $links .= '<a href="'.$lien_video.'" target="_blank" class="video icon-video"></a>';
            else $links .= '<a class="video icon-video disable"></a>';

            if($lien_photo) $links .= '<a href="'.$lien_photo.'" class="photo icon-photo"></a>';
            else $links .= '<a class="photo icon-photo disable"></a>';
            
            if(preg_match('#\.(jpg|png)#i', get_post_meta(get_the_ID(), 'equipeDomicileLogo', true)))
                $logoDomicile = '<img src="'.get_post_meta(get_the_ID(), 'equipeDomicileLogo', true).'" alt="'.get_post_meta(get_the_ID(), 'equipeDomicile', true).'" />';
            else
                $logoDomicile = '';
            
            if(preg_match('#\.(jpg|png)#i', get_post_meta(get_the_ID(), 'equipeExterieurLogo', true)))
                $logoExterieur = '<img src="'.get_post_meta(get_the_ID(), 'equipeExterieurLogo', true).'" alt="'.get_post_meta(get_the_ID(), 'equipeExterieur', true).'" />';
            else
                $logoExterieur = '';
            
            
            $html .= '<div class="ligne">
                            <div>
                                    '.date('d/m/Y', $time_uk).'<br />
                                    '.get_post_meta(get_the_ID(), 'heure', true).'<br />
                                    '.get_post_meta(get_the_ID(), 'journee', true).'
                            </div>
                            <div class="match">
                                    <div>
                                            '.get_post_meta(get_the_ID(), 'equipeDomicile', true).' '.$logoDomicile.'
                                    </div>
                                    <div class="score">
                                            '.$score_domicile.' - '.$score_exterieur.'
                                    </div>
                                    <div>
                                            '.$logoExterieur.' '.get_post_meta(get_the_ID(), 'equipeExterieur', true).'
                                    </div> 
                            </div>';
            if($url_stat != '#_'){
                $html .= '<div class="stats">
                                        <a href="'.$url_stat.'" target="_blank" class="stats"></a>
                                </div>';
            }
            
            $html .= '<div>
                                    '.$links.'
                            </div>
                    </div>';
            
            
        }
        
        wp_reset_postdata();
        
        $html .= '</div>';
        
        return $html;
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
     * Prépare la requête
     * 
     * @param string $saison saison au format 20142015
     * @return \WP_Query
     */
    private static function getMatches($saison, $competition = null)
    {
        
        $year1 = substr($saison, 0, 4);
        $year2 = substr($saison, 4);
        
        $args = array(
                        'posts_per_page' => -1,
                        'post_type' => 'lnh_matches',
                        'meta_query' => array(
                            array(
                                'key' => 'dateUK',
                                'value' => $year1.'-08-01',
                                'compare' => '>='
                            ),
                            array(
                                'key' => 'dateUK',
                                'value' => $year2.'-07-31',
                                'compare' => '<='
                            )
                        ),
                        'orderby' => 'dateUK',
                        'order' => 'ASC'
                    );
        
        if(!is_null($competition)){
            $args['meta_query'][] = array(
                                'key' => 'competitionCode',
                                'value' => $competition,
                            );
        }
        
        $query  = new \WP_Query(
                    $args
                );
        
        return $query;
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
                        'post_type' => 'lnh_matches',
                        'posts_per_page' => -1
                    )
                );
        
        $saison = array();
        
        $current_saison = self::getCurrentSaison();
        
        $saison[$current_saison] = substr($current_saison, 0, 4).' / '.substr($current_saison, 4);
        
        foreach($post as $match){
            
            $dateUK = get_post_meta($match->ID, 'dateUK', true);
            
            $timeUK = strtotime($dateUK);
            
            $year = date('Y', $timeUK);
            
            if(date('m', $timeUK) >= 8){
                $year1 = $year+1;
                $saison_calendrier = $year . $year1;
            } else {
                $year1 = $year-1;
                $saison_calendrier = $year1 . $year;
            }
            
            if(!empty($saison_calendrier)) $saison[$saison_calendrier] = substr($saison_calendrier, 0, 4).' / '.substr($saison_calendrier, 4);
            
        }
        
        return $saison;
    }
    
    
    /**
     * Retourne les championnats disponibles
     * 
     * @return array
     */
    private static function getAvalaibleChampionship()
    {
        $post = get_posts(
                    array(
                        'post_type' => 'lnh_matches',
                        'posts_per_page' => -1
                    )
                );
        
        $championnat = array();
        
        foreach($post as $match){
            
            $nom_championnat = get_post_meta($match->ID, 'competitionNom', true);
            $code_championnat = get_post_meta($match->ID, 'competitionCode', true);
            
            if(!empty($code_championnat) && !empty($nom_championnat)) $championnat[$code_championnat] = $nom_championnat;
            
        }
        
        return $championnat;
    }
    
    private static function getFormSaison($saison_selected = null, $competition_selected = null)
    {
        $form = '<form method="get" action="#_" class="filtres">';
        
        $form .= '<label>Filtres</label>';
        
        $form .= '<select name="saison" id="saison">';
        
        $saison = self::getAvalaibleSaison();
        $form .= '<option value="'.self::getCurrentSaison().'">Saison</option>';
        
        foreach($saison as $value => $label){
            $selected = null;

            if(isset($_GET['saison']) && isset($saison_selected) && $saison_selected == $value){
                $selected = ' selected';
            }
            
            $form .= '<option value="'.$value.'"'.$selected.'>'.$label.'</option>';
        }
        
        $form .= '</select>';
        
        
        $championnats = self::getAvalaibleChampionship();
        
        $form .= '<select name="competition" id="competition">';
        
        $form .= '<option value="">Compétition</option>';
        
        foreach($championnats as $value => $label){
            $selected = null;

            if(isset($_GET['competition']) && isset($competition_selected) && $competition_selected == $value){
                $selected = ' selected';
            }
            
            $form .= '<option value="'.$value.'"'.$selected.'>'.$label.'</option>';
        }
        
        $form .= '</select>';
        
        
        $form .= '<input type="submit" id="submit_saison" value="→ Ok" />';
        
        $form .= '</form>';
        
        return $form;
    }
    
    private static function getSaison($date_uk_format)
    {
        $time = strtotime($date_uk_format);
        
        $year1 = date('Y', $time);
        
        if(date('m', $time) >= 8){
            $year2 = $year1+1;
        } else {
            $year2 = date('Y', $time);
            $year1 = $year2-1;
        }
        
        return $year1.$year2;
    }
    
}
