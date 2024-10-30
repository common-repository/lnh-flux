<?php
namespace tests\Lnh\Plugin;

use Lnh\Plugin\Plugin;
/**
 * Test de l'initialisation du plugin
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{
    
    public function setUp() {
        \WP_Mock::setUp();
    }

    public function tearDown() {
        \WP_Mock::tearDown();
    }
    
    public function test_action_init()
    {
        
        $plugin = new Plugin();
        
        \WP_Mock::expectActionAdded( 'init', array( $plugin, 'createContentType' ) );
        
        $plugin->init();
                
    }
    
    public function test_content_type()
    {
        
        $plugin = new Plugin();
        
        \WP_Mock::wpFunction( 'register_post_type', array(
            'times' => 3,
            'arg' => array('lnh_matches')
         ) );
        
        \WP_Mock::wpFunction( 'get_option', array(
            'times' => 1,
            'arg' => array('lnh_equipe_handball'),
            'return' => array(
                'equipe' => 'toulouse',
                'classement' => '1',
                'joueurs' => '1',
                'calendrier' => '1'
                )
         ) );
        
        $plugin->createContentType();
                
    }
    
    public function test_init_widgets()
    {
        $plugin = new Plugin();
        $widgets = $plugin->initWidgets();
        
        $this->assertInstanceOf('Lnh\Widget\Widget', $widgets);
    }
}
