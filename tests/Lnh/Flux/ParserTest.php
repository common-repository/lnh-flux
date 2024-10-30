<?php
namespace tests\Lnh\Flux;

use Lnh\Flux\Parser;
use Lnh\Flux\Item\Item;
use Lnh\Flux\Item\Season\Season;
/**
 * Description of ParserTest
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    private $url_parse = 'http://www.lnh.fr/remote/equipes/toulouse/xml_lastJourneeEquipe.xml';
    
    private $url_parse_multiple_objects = 'http://www.lnh.fr/remote/equipes/toulouse/xml_saisonCalendrierEquipe.xml';
    
    private $url_parse_joueur = 'http://www.lnh.fr/remote/equipes/toulouse/xml_saisonCompositionEquipe.xml';
    
    public function test_parser()
    {
        $parser = new Parser($this->url_parse);
        $parsed = $parser->parse();
        
        $this->assertInstanceOf('SimpleXMLElement', $parsed);
        
        if(date('m') != 8){ //le flux est resetté en aout
            $this->assertEquals($parsed->count(), 1);

            $this->assertEquals($parsed->children()->count(), 1);

            $this->assertGreaterThan(1, $parsed->children()->children()->count());


            $collection = $parser->getItemsCollection();

            $this->assertInternalType('array', $collection);

            $this->assertInstanceOf('Lnh\Flux\Item\Item', $collection[0]);

            $this->assertNotInstanceOf('Lnh\Flux\Item\Season\Season', $collection[0]);

            foreach($collection as $k => $item){
                $this->notNullPropertyNormal($item);
            }
        } else {
            $this->assertEquals($parsed->count(), 0);
        }
    }
    
    public function test_multiple_parser()
    {
        $parser = new Parser($this->url_parse_multiple_objects);
        $parsed = $parser->parse();
        
        $this->assertInstanceOf('SimpleXMLElement', $parsed);
        
        $this->assertGreaterThan(1, $parsed->count());
        
        $this->assertGreaterThan(1, $parsed->children()->count());
        
        $this->assertGreaterThan(1, $parsed->children()->children()->count());
        
        
        $collection = $parser->getItemsCollection();
        
        $this->assertInternalType('array', $collection);
        
        $this->assertInstanceOf('Lnh\Flux\Item\Season\Season', $collection[0]);
        
        foreach($collection as $k => $item){
            $this->notNullPropertySaison($item);
        }
    }
    
    private function notNullPropertyNormal(\Lnh\Flux\Item\Item $item)
    {
        $this->assertNotNull($item->competitionCode);
        
        $this->assertNotNull($item->competitionNom);
        
        $this->assertNotNull($item->matchCode);
        $this->assertNotNull($item->journee); //si match amical == null
        $this->assertNotNull($item->equipeDomicile);
        $this->assertNotNull($item->equipeDomicileLogo);
        $this->assertNotNull($item->scoreDomicile);
        $this->assertNotNull($item->equipeExterieur);
        $this->assertNotNull($item->equipeExterieurLogo);
        $this->assertNotNull($item->scoreExterieur);
    }
    
    private function notNullPropertySaison(\Lnh\Flux\Item\Item $item)
    {
        $this->assertNotNull($item->competitionCode);
        
        $this->assertNotNull($item->competitionNom);
        
        $this->assertNotNull($item->matchCode);
        //$this->assertNotNull($item->journee); //si match amical == null
        $this->assertNotNull($item->equipeDomicile);
        $this->assertNotNull($item->equipeDomicileLogo);
        $this->assertNotNull($item->equipeDomicileScore);
        $this->assertNotNull($item->equipeExterieur);
        $this->assertNotNull($item->equipeExterieurLogo);
        $this->assertNotNull($item->equipeExterieurScore);
        $this->assertNotNull($item->dateFR);
        $this->assertNotNull($item->heure);
        $this->assertNotNull($item->salleNom);
        $this->assertNotNull($item->salleVille);
    }
    
    
    private function notNullPropertyPlayer(\Lnh\Flux\Equipe\Joueur\Joueur $item)
    {
        $this->assertNotNull($item->nom);
        
        $this->assertNotNull($item->prenom);
        
        $this->assertNotNull($item->dateNaissanceSmall);
        //$this->assertNotNull($item->journee); //si match amical == null
        $this->assertNotNull($item->paysNom);
        $this->assertNotNull($item->taille);
        $this->assertNotNull($item->poids);
        $this->assertNotNull($item->droiteGauche);
        $this->assertNotNull($item->posteNom);
        $this->assertNotNull($item->photo);
        $this->assertNotNull($item->url);
    }
    
    
    
    public function test_multiple_parser_joueur()
    {
        $parser = new Parser($this->url_parse_joueur);
        $parsed = $parser->parse();
        
        $this->assertInstanceOf('SimpleXMLElement', $parsed);
        
        
        $collection = $parser->getItemsCollection();
        
        $this->assertInternalType('array', $collection);
        
        $this->assertInstanceOf('Lnh\Flux\Equipe\Joueur\Joueur', $collection[0]);
        
        foreach($collection as $k => $item){
            $this->notNullPropertyPlayer($item);
        }
    }
}
