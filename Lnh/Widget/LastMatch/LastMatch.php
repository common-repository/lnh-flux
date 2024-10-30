<?php
namespace Lnh\Widget\LastMatch;

/**
 * Widget pour afficher le dernier match
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class LastMatch extends \WP_Widget
{
    private $content_type = 'lnh_matches';
    
    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
                    'lnh_last_match_widget', // Base ID
                    __('LNH Last Match', 'lnh_last_match'), // Name
                    array( 'description' => __('Affichage du dernier match depuis le fichier XML de la LNH', 'lnh_last_match')) // Args
            );
    }
    
    
    private function getLastMatchData()
    {
        
        $args = array(
                    'posts_per_page' => 1,
                    'post_type' => $this->content_type,
                    'orderby' => 'dateUK',
                    'meta_query' => array(
                            array(
                               'key'     => 'dateUK',
                               'value'   => date('Y-m-d'),
                               'compare' => '<',
                               'type' => 'DATE'
                            )
                         )
                    );
        
        $post = get_posts(
                           $args 
                            );
        
        if(isset($post[0])) 
            return $post[0];
        else false;
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        
        $post = $this->getLastMatchData();
        
        if($post){

            $equipe_domicile = get_post_meta($post->ID, 'equipeDomicile', true);
            $score_domicile = get_post_meta($post->ID, 'equipeDomicileScore', true);
            $logo_domicile = get_post_meta($post->ID, 'equipeDomicileLogo', true);

            $equipe_exterieur = get_post_meta($post->ID, 'equipeExterieur', true);
            $score_exterieur = get_post_meta($post->ID, 'equipeExterieurScore', true);
            $logo_exterieur = get_post_meta($post->ID, 'equipeExterieurLogo', true);

            $logo_championnat = get_post_meta($post->ID, 'competitionLogo', true);
            $nom_championnat = get_post_meta($post->ID, 'competitionNom', true);
            $code_championnat = get_post_meta($post->ID, 'competitionCode', true);

            $date_match_uk = get_post_meta($post->ID, 'dateUK', true);
            $time_match = strtotime($date_match_uk);

            $journee = get_post_meta($post->ID, 'journee', true);
            if(preg_match('#journée#i', $journee)){
                $aJournee = explode(' ', $journee);
                $journee = trim($aJournee[count($aJournee)-1]);
            }

            $journee = str_replace(' ', '-', $journee);

            $saison = $this->getSaison($date_match_uk);



            $url_stat = 'http://www.lnh.fr/calendrier/'
                    .$saison.'/'
                    .strtolower($code_championnat).'/'
                    .$journee.'/'
                    .strtolower(remove_accents($equipe_domicile)).'/'
                    .strtolower(remove_accents($equipe_exterieur));

            $html = '<article>

                    <h2>Dernier match</h2>
                    <p class="date">'.date('d-m-Y', $time_match).'</p>
                    <p>'.$equipe_domicile.' <span>vs</span> '.$equipe_exterieur.'</p>
                    <div class="scores open">
                            <div class="content">
                                    <a href="#_" class="btn">
                                            <!-- <span></span> -->
                                            <img src="'.get_template_directory_uri().'/custom/img/croix.png" alt="Deployer bloc" />
                                    </a>
                                    <div>
                                            <figure>
                                                    <img src="'.$logo_domicile.'" alt="'.$equipe_domicile.'" />
                                                    <figcaption>
                                                            '.$score_domicile.'
                                                    </figcaption>
                                            </figure>
                                            <div>
                                                    <p>vs</p>';

            if(!empty($logo_championnat)) $html .= '<img src="'.$logo_championnat.'" alt="'.$nom_championnat.'" />';

            $html .= '</div>
                                            <figure>
                                                    <img src="'.$logo_exterieur.'" alt="'.$equipe_exterieur.'" />
                                                    <figcaption>
                                                            '.$score_exterieur.'
                                                    </figcaption>
                                            </figure>
                                    </div>
                                    <div class="clearfix">
                                            <a href="'.$url_stat.'" target="_blank" class="stats"></a>';

            $lien_article = get_post_meta($post->ID, 'lnh_match_article', true);
            $lien_photo = get_post_meta($post->ID, 'lnh_match_photo', true);
            $lien_video = get_post_meta($post->ID, 'lnh_match_video', true);

            if($lien_article) $html .= '<a href="'.$lien_article.'" target="_blank" class="article icon-article"></a>';
            else $html .= '<a class="article icon-article disable"></a>';

            if($lien_video) $html .= '<a href="'.$lien_video.'" target="_blank" class="video icon-video"></a>';
            else $html .= '<a class="video icon-video disable"></a>';

            if($lien_photo) $html .= '<a href="'.$lien_photo.'" class="photo icon-photo"></a>';
            else $html .= '<a class="photo icon-photo disable"></a>';



            $html .= '</div>
                            </div>
                    </div>
            </article>';

            echo $html;

            return $html;
        }
    }
    
    private function getSaison($date_uk_format)
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
