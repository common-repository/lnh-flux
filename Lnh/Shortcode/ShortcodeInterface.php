<?php
namespace Lnh\Shortcode;

/**
 * Description of SCInterface
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
interface ShortcodeInterface
{
    public function filter($atts, $content = null);
}