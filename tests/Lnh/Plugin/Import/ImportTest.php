<?php
namespace tests\Lnh\Plugin\Import;

use Lnh\Plugin\Import\Import;
/**
 * Test de la sauvegarde de donnée dans la BDD wordpress
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class ImportTest extends \PHPUnit_Framework_TestCase
{
    public function setUp() {
        \WP_Mock::setUp();
    }

    public function tearDown() {
        \WP_Mock::tearDown();
    }
    
    public function test_save_item()
    {
        $importer = new Import($this->createItem(1));
        
        
        
        \WP_Mock::wpPassthruFunction('get_posts', array('times' => 1));
        
        \WP_Mock::wpPassthruFunction('wp_insert_post', array('times' => 1));
        
        \WP_Mock::wpPassthruFunction('update_post_meta', array('times' => 1));
        
        $importer->save();
    }
    
    
    public function test_save_item_multiple()
    {
        $importer = new Import($this->createItem(10));
        
        
        
        \WP_Mock::wpPassthruFunction('get_posts', array('times' => 1));
        
        \WP_Mock::wpPassthruFunction('wp_insert_post', array('times' => 1));
        
        \WP_Mock::wpPassthruFunction('update_post_meta', array('times' => 10));
        
        $importer->save();
        
        
        
        $importer->setItem($this->createItem(5));
        
        \WP_Mock::wpPassthruFunction('get_posts', array('times' => 1));
        
        \WP_Mock::wpPassthruFunction('wp_insert_post', array('times' => 1));
        
        \WP_Mock::wpPassthruFunction('update_post_meta', array('times' => 5));
        
        $importer->save();
    }
    
    private function createItem($nbProperty = 1)
    {
        $stub = $this->getMockBuilder('Lnh\Flux\Item\Item')
                     ->disableOriginalConstructor()
                     ->getMock();
        
        $stub->matchCode = 'test';
        
        for($i = 1; $i < $nbProperty; $i++){
            $property_name = 'property_'.$i;
            
            $stub->$property_name = $i;
        }
        
        return $stub;
    }
}
