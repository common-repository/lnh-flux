<?php
namespace Lnh\Plugin\Import;

use Lnh\Flux\Item\Item;
use Lnh\Flux\Equipe\Equipe;
use Lnh\Flux\Equipe\Joueur\Joueur;
/**
 * Import un item dans la BDD wordpress
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Import
{
    private $item;

    private $content_type = 'lnh_matches';

    public function __construct(Item $item)
    {
        $this->item = $item;

        if ($item instanceof Equipe) {
            $this->content_type = 'lnh_classement';
            $item->setSaison();
        } else if ($item instanceof Joueur) {
            $this->content_type = 'lnh_joueurs_team';
            $item->setSaison();
        }
    }

    /**
     * Changement d'item de travail
     *
     * @param \Lnh\Flux\Item\Item $item
     */
    public function setItem(Item $item)
    {
        $this->item = $item;
    }

    /**
     * On vérifie si l'item est déjà présent en BDD ou non
     * grâce au code match
     */
    private function getExistingItem()
    {
        if ($this->content_type == 'lnh_matches') {
            $args = array(
                'meta_key'         => 'matchCode',
                'meta_value'       => $this->item->matchCode,
                'post_type'        => 'lnh_matches',
                'post_status'      =>  array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
            );
        } elseif ($this->content_type == 'lnh_classement') {
            $args = array(
                'meta_query' => array(
                    array(
                        'key'         => 'position',
                        'value'       => $this->item->position,
                    ),
                    array(
                        'key' => 'lnh_classement_saison',
                        'value' => $this->item->lnh_classement_saison
                    )
                ),
                'post_type'        => 'lnh_classement',
                'post_status'      =>  array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
            );
        } elseif ($this->content_type == 'lnh_joueurs_team') {
            
            $args = array(
                'meta_query' => array(
                    array(
                        'key'         => 'nom',
                        'value'       => $this->item->nom,
                    ),
                    array(
                        'key'         => 'prenom',
                        'value'       => $this->item->prenom,
                    ),
                    array(
                        'key' => 'lnh_classement_saison',
                        'value' => $this->item->lnh_classement_saison
                    )
                ),
                'post_type'        => 'lnh_joueurs_team',
                'post_status'      =>  array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
            );
            
        }

        return \get_posts($args);
    }

    /**
     * Enregistre les méta données
     *
     * @param  integer    $id_item
     * @throws \Exception
     */
    private function saveMeta($id_item)
    {
        foreach ($this->item as $meta_key => $meta_value) {
            $return = null;

            if (null !== $meta_value) {
                $return = \update_post_meta($id_item, $meta_key, $meta_value);
            }
            //if(false === $return) throw new \Exception ('Error when saving the meta data'.$meta_key.':'.$meta_value);
        }
    }

    /**
     * On sauvegarde l'item en BDD
     *
     * @return integer $id_post
     */
    public function save()
    {
        $existing_post = $this->getExistingItem();

        $args = array();

        if (count($existing_post) && isset($existing_post[0])) {
            $args['ID'] = $existing_post[0]->ID;
        }

        if ($this->content_type == 'lnh_matches') {
            $args['post_title'] = $this->item->competitionNom.' '.$this->item->journee.' '.$this->item->equipeDomicile.' - '.$this->item->equipeExterieur;
        } elseif ($this->content_type == 'lnh_classement') {
            $args['post_title'] = $this->item->position.' - '.$this->item->equipeNom;
        } elseif ($this->content_type == 'lnh_joueurs_team') {
            $args['post_title'] = $this->item->nom.' - '.$this->item->prenom;
        }
        $args['post_type'] = $this->content_type;
        $args['post_status'] = 'publish';

        $id_post = \wp_insert_post($args);

        if (0 === $id_post) {
            throw new \Exception('Error when saving '.$args['post_title']);
        }

        $this->saveMeta($id_post);

        return $id_post;
    }
}
