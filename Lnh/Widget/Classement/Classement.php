<?php
namespace Lnh\Widget\Classement;

/**
 * Widget permettant d'afficher le classement courant d'une équipe.
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Classement extends \WP_Widget
{
    
    private $content_type = 'lnh_classement';
    
    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
                    'lnh_classement_short_widget', // Base ID
                    __('LNH classement court', 'lnh_classement_short'), // Name
                    array( 'description' => __('Affichage du nombre de point et le classement en cours d\'une équipe.', 'lnh_classement_short')) // Args
            );
    }
    
    /**
     * Récupération du prochain match de l'équipe
     * 
     * @return mixed
     */
    private function getClassement($instance)
    {
        
        $equipe_module = $instance['equipe_widget_short'];
        
        $post = get_posts(
                            array(
                                'posts_per_page' => 1,
                                'post_type' => $this->content_type,
                                'meta_query' => array(
                                        array(
                                           'key'     => 'equipeNom',
                                           'value'   => $equipe_module,
                                           'compare' => '='
                                        ),
                                        array(
                                            'key' => 'lnh_classement_saison',
                                            'value' => self::getCurrentSaison()
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
        
        if(!isset($instance['equipe_widget_short']))
            return false;
        
        $link = $instance['equipe_widget_short_link'];
        
        $post = $this->getClassement($instance);
        
        $html = '<article>
		<h2>Classement</h2>';
        
        if($post){

            $position = get_post_meta($post->ID, 'position', true);
            $point = get_post_meta($post->ID, 'totalPoint', true);
            
            if((int)$point > 1){
                $point .= ' points';
            } else {
                $point .= ' point';
            }
            
            if((int)$position == 1){
                $position .= ' er';
            } else {
                $position .= ' ème';
            }
            
            $html .= '<p class="date">'.$position.' - '.$point.'</p>';

            
        } else  {
            $next_match = new \Lnh\Widget\NextMatch\NextMatch();
            $next_match_data = $next_match->getNextMatchData();
            
            $date_match_uk = get_post_meta($next_match_data->ID, 'dateUK', true);
            $time_match = strtotime($date_match_uk);
            
            $html .= '<p class="date">Prochain match le '.date('d-m-Y', $time_match).'</p>'; // vérifier quand on reset le classement?
        }
        
        if($link) $html .= '<a href="'.$link.'" class="btn_link"></a>';
        
        $html .= '</article>';
        
        echo $html;

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
    * Outputs the options form on admin
    *
    * @param array $instance The widget options
    */
    public function form( $instance ) {
        $title = ! empty( $instance['equipe_widget_short'] ) ? $instance['equipe_widget_short'] : '';
        $link = ! empty( $instance['equipe_widget_short_link'] ) ? $instance['equipe_widget_short_link'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'equipe_widget_short' ); ?>">Equipe à afficher pour le classement</label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'equipe_widget_short' ); ?>" name="<?php echo $this->get_field_name( 'equipe_widget_short' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'equipe_widget_short_link' ); ?>">Lien bouton "En savoir plus"</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'equipe_widget_short_link' ); ?>" name="<?php echo $this->get_field_name( 'equipe_widget_short_link' ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>">
        </p>
        <?php 
    }
}
