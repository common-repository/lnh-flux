<?php
namespace Lnh\Shortcode;

/**
 * Va permettre de cleaner les contenus
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Sanitize
{
    protected function clean($content, $authorized_tags = null){
        
        $content = strip_tags ($content, $authorized_tags);
        
        if ( '</p>' == substr( $content, 0, 4 )
and '<p>' == substr( $content, strlen( $content ) - 3 ) )
	$content = substr( $content, 4, strlen( $content ) - 7 );
        
        $content = str_replace('<br />[', '[', $content);
        
        return $content;
        
    }
}
