<?php
namespace tests\Lnh\Widget;

use Lnh\Widget\Widget;
/**
 * Test de création des widgets lié à l'import
 * des données LNH
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class WidgetTest extends \PHPUnit_Framework_TestCase
{
    
    public function setUp() 
    {
        \WP_Mock::setUp();
    }

    public function tearDown() 
    {
        \WP_Mock::tearDown();
    }
    
    public function test_init_widgets()
    {
        $widget = new Widget();
        
        \WP_Mock::expectActionAdded( 'widgets_init', array( $widget, 'registerWidgets' ) );
        
        $widget->init();
        
        \WP_Mock::wpPassthruFunction('register_widget', array('times' => 1, 'args' => array('Lnh\Widget\LastMatch\LastMatch')));
        
        \WP_Mock::wpPassthruFunction('register_widget', array('times' => 1, 'args' => array('Lnh\Widget\NextMatch\NextMatch')));
        
        \WP_Mock::wpPassthruFunction('register_widget', array('times' => 1, 'args' => array('Lnh\Widget\Calendar\Calendar')));
        
        \WP_Mock::wpPassthruFunction('register_widget', array('times' => 1, 'args' => array('Lnh\Widget\Calendar\Short\CalendarShort')));
        
        \WP_Mock::wpPassthruFunction('register_widget', array('times' => 1, 'args' => array('Lnh\Widget\Classement\Classement')));
        
        $widget->registerWidgets();
    }
    
}
