<?php
namespace Lnh\Widget\Calendar\Short;

/**
 * Widget calendrier short destiné à la sidebar.
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class CalendarShort extends \WP_Widget
{
    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
                    'lnh_calendar_widget_short', // Base ID
                    __('LNH Calendar Saison court', 'lnh_calendar_short'), // Name
                    array( 'description' => __('Permet d\'afficher un lien vers la page calendrier de la saison en court(sidebar)', 'lnh_calendar_short')) // Args
            );
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
        $link = $instance['calendar_link_widget_short_link'];
        
        $html = '<article>
                        <h2>Calendrier & résultats</h2>
                        <p class="date">Saison '.$this->getCurrentSaison().'</p>
                        <a href="'.$link.'" class="btn_link"></a>
                </article>';

        echo $html;

        return $html;
    }
    
    /**
     * Retourne la saison courante au format aaaa - aaaa
     * 
     * @return string
     */
    private function getCurrentSaison()
    {
        if(date('m')>= 8){
            $year1 = date('Y');
            $year2 = $year1+1;
        } else {
            $year2 = date('Y');
            $year1 = $year1-1;
        }
        
        return $year1.' - '.$year2;
    }
    
    /**
    * Outputs the options form on admin
    *
    * @param array $instance The widget options
    */
    public function form( $instance ) {
        $link = ! empty( $instance['calendar_link_widget_short_link'] ) ? $instance['calendar_link_widget_short_link'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'calendar_link_widget_short_link' ); ?>">Lien bouton "En savoir plus"</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'calendar_link_widget_short_link' ); ?>" name="<?php echo $this->get_field_name( 'calendar_link_widget_short_link' ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>">
        </p>
        <?php 
    }
}
