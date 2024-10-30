<?php
namespace Lnh\Widget\Calendar;

/**
 * Widget calendrier de la saison entière
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Calendar extends \WP_Widget
{
    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
                    'lnh_calendar_widget', // Base ID
                    __('LNH Calendar Saison', 'lnh_calendar'), // Name
                    array( 'description' => __('Affichage du calendrier de la saison depuis le fichier XML de la LNH', 'lnh_calendar')) // Args
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
        $html = "<div class='widget widget-calendar'>"
                   ."Hello Calendrier saison"
                   ."</div>";

        echo $html;

        return $html;
    }
}
