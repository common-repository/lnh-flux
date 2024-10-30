<?php
namespace Lnh\Widget;

/**
 * Création de différents Widget
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Widget
{
    public function init()
    {
        add_action('widgets_init', array($this, 'registerWidgets'));
    }

    public function registerWidgets()
    {
        register_widget('Lnh\Widget\LastMatch\LastMatch'); //widget du dernier match joué
        register_widget('Lnh\Widget\NextMatch\NextMatch'); //widget du prochain match joué
        register_widget('Lnh\Widget\Calendar\Calendar'); //widget du calendrier de la saison
        register_widget('Lnh\Widget\Classement\Classement'); //widget du classement short.
        register_widget('Lnh\Widget\Calendar\Short\CalendarShort'); //widget du calendrier short.
    }
}
