<?php
namespace Lnh\Widget\NextMatch;

/**
 * Widget du prochain match de l'équipe.
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class NextMatch extends \WP_Widget
{
    private $content_type = 'lnh_matches';
    
    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
                    'lnh_last_next_widget', // Base ID
                    __('LNH Next Match', 'lnh_next_match'), // Name
                    array( 'description' => __('Affichage du prochain match depuis le fichier XML de la LNH', 'lnh_next_match')) // Args
            );
    }
    
    /**
     * Récupération du prochain match de l'équipe
     * 
     * @return mixed
     */
    public function getNextMatchData()
    {
        
        $date = new \DateTime();
        $date_format = $date->format('Y-m-d');
        $heure_format = $date->format('H:i');
        
        $post = get_posts(
                            array(
                                'posts_per_page' => 1,
                                'post_type' => $this->content_type,
                                'orderby' => 'dateUK',
                                'order' => 'ASC',
                                'meta_query' => array(
                                        array(
                                           'key'     => 'dateUK',
                                           'value'   => (string)$date_format,
                                           'compare' => '>='
                                        ),
                                        array(
                                           'key'     => 'heure',
                                           'value'   => (string)$heure_format,
                                           'compare' => '>'
                                        )
                                     )
                                )
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
        
        $post = $this->getNextMatchData();
        
        if($post){

            $equipe_domicile = get_post_meta($post->ID, 'equipeDomicile', true);
            $score_domicile = '--';
            $logo_domicile = get_post_meta($post->ID, 'equipeDomicileLogo', true);

            $equipe_exterieur = get_post_meta($post->ID, 'equipeExterieur', true);
            $score_exterieur = '--';
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


            $lien_article = get_post_meta($post->ID, 'lnh_match_article', true);
            $lien_photo = get_post_meta($post->ID, 'lnh_match_photo', true);
            $lien_video = get_post_meta($post->ID, 'lnh_match_video', true);

            if($lien_article || $lien_video || $lien_photo){
                $class=" lien_footer";
            } else {
                $class="";
            }


            $html = '<article class="next_match">

                    <h2>Prochain match</h2>
                    <p class="date">'.date('d-m-Y', $time_match).'</p>
                    <p>'.$equipe_domicile.' <span>vs</span> '.$equipe_exterieur.'</p>
                    <div class="scores'.$class.'">
                            <div class="content">
                                    <a href="#_" class="btn">
                                            <!-- <span></span> -->
                                            <img src="'.get_template_directory_uri().'/custom/img/croix.png" alt="Deployer bloc" />
                                    </a>
                                    <div>
                                            <figure>
                                                    <img src="'.$logo_domicile.'" alt="'.$equipe_domicile.'" />
                                            </figure>
                                            <div>
                                                    <p>vs</p>';

            if(!empty($logo_championnat)) $html .= '<img src="'.$logo_championnat.'" alt="'.$nom_championnat.'" />';

            $html .= '</div>
                                            <figure>
                                                    <img src="'.$logo_exterieur.'" alt="'.$equipe_exterieur.'" />
                                            </figure>
                                    </div>
                                    <div class="clearfix">';

            
            
            if($lien_article || $lien_video || $lien_photo){
                
                $html .= '<a class="stats disable"></a>';

                if($lien_article) $html .= '<a href="'.$lien_article.'" target="_blank" class="article icon-article"></a>';
                else $html .= '<a class="article icon-article disable"></a>';

                if($lien_video) $html .= '<a href="'.$lien_video.'" target="_blank" class="video icon-video"></a>';
                else $html .= '<a class="video icon-video disable"></a>';

                if($lien_photo) $html .= '<a href="'.$lien_photo.'" class="photo icon-photo"></a>';
                else $html .= '<a class="photo icon-photo disable"></a>';
            
            }


            $html .= '</div>
                            </div>
                    </div>
            </article>';

            echo $html;

            return $html;
        }
        
        
    }
    
    /**
     * Retourne la saison en fonction d'une date format Y-m-d
     * 
     * @param string $date_uk_format
     * @return string
     */
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
