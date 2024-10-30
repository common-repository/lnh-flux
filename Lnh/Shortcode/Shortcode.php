<?php
namespace Lnh\Shortcode;

/**
 * Register new shortcodes
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Shortcode
{
    static function register($aCodes)
    {
        foreach($aCodes as $tag => $class){            
            add_shortcode( $tag, array( "Lnh\Shortcode\Type\\$class\\$class", 'filter' ) );
        }
    }
}