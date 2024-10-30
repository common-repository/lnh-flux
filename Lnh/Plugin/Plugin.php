<?php
namespace Lnh\Plugin;

use Lnh\Widget\Widget;
use Lnh\Plugin\Import\Import;
use Lnh\Flux\Parser;
use Lnh\Shortcode\Shortcode;
/**
 * Initialisation du plugin d'import de données
 *
 * @author Raphael GONCALVES <raphael@couleur-citron.com>
 */
class Plugin
{
    /**
     * Initialise les actions
     */
    public function init()
    {
        add_action('init', array($this, 'createContentType'));

        add_action('admin_init', array($this, 'pluginAdminInit'));

        add_action('admin_menu', array($this, 'createMenuBO'));

        add_action('admin_action_importPageAction', array($this, 'importPageAction'));

        $this->initWidgets();
    }

    /**
     * Va créer une entré BO pour la configuration du Module
     */
    public function createMenuBO()
    {
        //création de la page de configuration
        \add_options_page('LNH Settings', 'LNH Settings', 'manage_options', 'LNH_Settings', array($this, 'pagePluginSettings'));

        //création de la page d'import manuel
        \add_management_page('Import flux LNH', 'Import flux LNH', 'administrator', 'Import_flux_LNH', array($this, 'importPage'));
    }

    /**
     * Créé le content type Match
     */
    public function createContentType()
    {
        
        $options = \get_option('lnh_equipe_handball');
        
        if($options['calendrier'] == '1'){
            register_post_type('lnh_matches',
                array(
                    'labels' => array(
                      'name' => 'LNH Matchs',
                      'singular_name' => 'LNH Match',
                    ),
                    'public' => true,
                    'supports' => array('title'),
                  )
                );
        }
        
        if($options['classement'] == '1'){
            register_post_type('lnh_classement',
                array(
                    'labels' => array(
                      'name' => 'LNH Classement',
                      'singular_name' => 'LNH Classement',
                    ),
                    'public' => true,
                    'supports' => array('title'),
                  )
                );
        }
        
        if($options['joueurs'] == '1'){
            register_post_type('lnh_joueurs_team',
                array(
                    'labels' => array(
                      'name' => 'LNH Joueurs',
                      'singular_name' => 'LNH Joueurs',
                    ),
                    'public' => true,
                    'supports' => array('title'),
                    'rewrite' => array( 'slug' => 'joueur_team' ),
                    'has_archive' => true
                  )
                );
        }
        $this->createCustomFields();
    }
    
    /**
     * Need to use the wp-framework-backend-form plugin
     * 
     * @see https://github.com/Weysan/wp-framework-backend-form
     */
    public function createCustomFields()
    {
        if(class_exists('\Form\WpForm')){
            $options = \get_option('lnh_equipe_handball');
            
            if($options['classement'] == '1') $this->customFieldsClassement();
            if($options['calendrier'] == '1') $this->customFieldsMatchs();
            if($options['joueurs'] == '1') $this->customFieldsPlayer();
            
        } else {
            \add_action( 'admin_notices', array($this, 'needPlugin') );
        }
    }
    
    private function customFieldsMatchs()
    {
        $custom_fields_match = array(
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Code Compétition',
                    'id' => 'competitionCode'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Nom Compétition',
                    'id' => 'competitionNom'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Logo Compétition',
                    'id' => 'competitionLogo'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Code Match',
                    'id' => 'matchCode'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Journée',
                    'id' => 'journee'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Equipe domicile',
                    'id' => 'equipeDomicile'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Logo Equipe domicile',
                    'id' => 'equipeDomicileLogo'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Score Equipe domicile',
                    'id' => 'equipeDomicileScore'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Equipe extérieur',
                    'id' => 'equipeExterieur'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Logo Equipe extérieur',
                    'id' => 'equipeExterieurLogo'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Score Equipe extérieur',
                    'id' => 'equipeExterieurScore'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Date format UK',
                    'id' => 'dateUK'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Date format FR',
                    'id' => 'dateFR'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Date format raccourci',
                    'id' => 'dateSmall'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Date complète',
                    'id' => 'dateFull'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Heure',
                    'id' => 'heure'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Nom de salle',
                    'id' => 'salleNom'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Ville du match',
                    'id' => 'salleVille'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Chaine télé',
                    'id' => 'teleNom'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Logo Chaine télé',
                    'id' => 'teleLogo'
                ),
                array(
                    'type' => 'Form\Input\Url\Url',
                    'label' => 'Lien article',
                    'id' => 'lnh_match_article'
                ),
                array(
                    'type' => 'Form\Input\Url\Url',
                    'label' => 'Lien photo',
                    'id' => 'lnh_match_photo'
                ),
                array(
                    'type' => 'Form\Input\Url\Url',
                    'label' => 'Lien vidéo',
                    'id' => 'lnh_match_video'
                )
            );
            
            $classement_form = new \Form\WpForm('lnh_matches', 'Import de flux LNH', $custom_fields_match);
    }
    
    private function customFieldsPlayer()
    {
        
        $custom_fields_player = array(
            array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Nom de famille',
                    'id' => 'nom'
                ),
            array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Prénom',
                    'id' => 'prenom'
                ),
            array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Date de naissance (petit format)',
                    'id' => 'dateNaissanceSmall'
                ),
            array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Date de naissance (format complet)',
                    'id' => 'dateNaissanceFull'
                ),
            array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Pays',
                    'id' => 'paysNom'
                ),
            array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Sexe',
                    'id' => 'sexe'
                ),
            array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Taille (cm)',
                    'id' => 'taille'
                ),
            array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Poids (kg)',
                    'id' => 'poids'
                ),
            array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Droitier/Gaucher',
                    'id' => 'droiteGauche'
                ),
            array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Position',
                    'id' => 'posteNom'
                ),
            array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Code groupe',
                    'id' => 'groupeCode'
                ),
            array(
                    'type' => 'Form\Input\Url\Url',
                    'label' => 'Photo',
                    'id' => 'photo'
                ),
            array(
                    'type' => 'Form\Input\Url\Url',
                    'label' => 'Lien fiche LNH',
                    'id' => 'url'
                ),
//            array(
//                    'type' => 'Form\Input\Text\Text',
//                    'label' => 'Nombre de match',
//                    'id' => 'nbPlayed'
//                ),
//            array(
//                    'type' => 'Form\Input\Text\Text',
//                    'label' => 'But marqués',
//                    'id' => 'playerGoals'
//                ),
//            array(
//                    'type' => 'Form\Input\Text\Text',
//                    'label' => 'player Attempts',
//                    'id' => 'playerAttempts'
//                ),
//            array(
//                    'type' => 'Form\Input\Text\Text',
//                    'label' => 'Arrêts gardien',
//                    'id' => 'goalkeeperStops'
//                ),
//            array(
//                    'type' => 'Form\Input\Text\Text',
//                    'label' => 'Buts encaissés',
//                    'id' => 'goalkeeperConceded'
//                ),
//            array(
//                    'type' => 'Form\Input\Text\Text',
//                    'label' => 'Buts encaissés',
//                    'id' => 'goalkeeperConceded'
//                ),
//            array(
//                    'type' => 'Form\Input\Text\Text',
//                    'label' => 'nbTurnovers',
//                    'id' => 'nbTurnovers'
//                ),
//            array(
//                    'type' => 'Form\Input\Text\Text',
//                    'label' => 'nbTwoMin',
//                    'id' => 'nbTwoMin'
//                ),
//            array(
//                    'type' => 'Form\Input\Text\Text',
//                    'label' => 'Nombre de cartons rouge',
//                    'id' => 'nbEjected'
//                )
        );
        
        $player_form = new \Form\WpForm('lnh_joueurs_team', 'Import de flux LNH', $custom_fields_player);
    }
    
    private function customFieldsClassement()
    {
        $custom_fields_classement = array(
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Position',
                    'id' => 'position'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Equipe',
                    'id' => 'equipeNom'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Logo',
                    'id' => 'equipeLogo'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Match total',
                    'id' => 'totalMatch'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Match total domicile',
                    'id' => 'totalMatchDomicile'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Match total extérieur',
                    'id' => 'totalMatchExterieur'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Match total victoire',
                    'id' => 'totalVictoire'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Match total nul',
                    'id' => 'totalNul'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Match total défaite',
                    'id' => 'totalDefaite'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Points',
                    'id' => 'totalPoint'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Points domicile',
                    'id' => 'totalPointDomicile'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Points extérieur',
                    'id' => 'totalPointExterieur'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Nombre de but',
                    'id' => 'totalButPour'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Nombre de but domicile',
                    'id' => 'totalButPourDomicile'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Nombre de but extérieur',
                    'id' => 'totalButPourExterieur'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Nombre de but encaissé',
                    'id' => 'totalButContre'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Nombre de but encaissé domicile',
                    'id' => 'totalButContreDomicile'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Nombre de but encaissé extérieur',
                    'id' => 'totalButContreExterieur'
                ),
                array(
                    'type' => 'Form\Input\Text\Text',
                    'label' => 'Saison (format : 20152016)',
                    'id' => 'lnh_classement_saison'
                )
            );
            
            $classement_form = new \Form\WpForm('lnh_classement', 'Import de flux LNH', $custom_fields_classement);
    }
    
    public function needPlugin()
    {
        $class = "error";
	$message = "LNH-Flux nécessite d'activer le plugin <strong>wp-framework-backend-form plugin</strong> : <a href='https://github.com/Weysan/wp-framework-backend-form'>https://github.com/Weysan/wp-framework-backend-form</a>";
        
        echo "<div class=\"$class\"> <p>$message</p></div>";
    }

    /**
     * initialisation des widgets
     *
     * @return \Lnh\Widget\Widget
     */
    public function initWidgets()
    {
        $widget = new Widget();
        $widget->init();

        return $widget;
    }

    /**
     * HTML de la page settings
     */
    public function pagePluginSettings()
    {
        ?>
        <div>
        <h2>LNH Plugin Settings</h2>
        Options relatifs à l'import des données venu du site de la LNH.<br />L'import des données se fera pour une seule équipe uniquement.
        
        <?php if(isset($_POST['lnh_equipe_handball'])){
            //var_dump($_POST['lnh_equipe_handball']);
            //die('là');
            
            if(!isset($_POST['lnh_equipe_handball']['classement'])){
                $_POST['lnh_equipe_handball']['classement'] = '0';
            }
            if(!isset($_POST['lnh_equipe_handball']['calendrier'])){
                $_POST['lnh_equipe_handball']['calendrier'] = '0';
            }
            if(!isset($_POST['lnh_equipe_handball']['joueurs'])){
                $_POST['lnh_equipe_handball']['joueurs'] = '0';
            }
            
            
            $update = update_option('lnh_equipe_handball', $_POST['lnh_equipe_handball']);
            
            $options_updated = \get_option('lnh_equipe_handball');
        } ?>
        
        <form action="#_" method="post">
        <?php 
        settings_fields('lnh_equipe_handball');
        ?>
        <?php 
        do_settings_sections('plugin');
        ?>

        <input name="Submit" type="submit" value="<?php esc_attr_e('Sauver');
        ?>" />
        </form></div>

        <?php

    }

    /**
     * Initialisation des settings (section, fields, etc.
     */
    public function pluginAdminInit()
    {
        if($_GET['page'] == 'LNH_Settings'){
            register_setting('lnh_equipe_handball', 'lnh_equipe_handball', array($this, 'optionValidate'));
            
            add_settings_section('plugin_main', 'Option principale', array($this, 'pluginSectionString'), 'plugin');
            add_settings_field('lnh_equipe_handball', 'Equipe de handball', array($this, 'inputSettingString'), 'plugin', 'plugin_main');
            add_settings_field('lnh_flux_handball', 'Flux de handball', array($this, 'inputFluxCheckbox'), 'plugin', 'plugin_main');
        }
    }

    /**
     * HTML et description de la section de la page Config
     */
    public function pluginSectionString()
    {
        echo '<p>Le plugin ne prend en charge qu\'une seule équipe de handball à la fois.</p>';
    }

    /**
     * Champ input de configuration
     */
    public function inputSettingString()
    {
        $options = \get_option('lnh_equipe_handball');
        
        //var_dump($options);
        echo "<input id='lnh_equipe_handball' name='lnh_equipe_handball[text_string]' size='40' type='text' value='{$options['text_string']}' /><br /><br />";
    }
    
    public function inputFluxCheckbox()
    {
        
        $options = \get_option('lnh_equipe_handball');
        
        $html = "<input type='checkbox' name='lnh_equipe_handball[classement]' id='lnh_equipe_handball' value='1'";
        if($options['classement'] == 1){
            $html .= ' checked="checked"';
        }
        $html .= " /><label for='lnh_flux_handball_classement'>Flux classement de l'équipe</label><br />";
        
        
        
        $html .= "<input type='checkbox' name='lnh_equipe_handball[calendrier]' id='lnh_equipe_handball' value='1'";
        if($options['calendrier'] == 1){
            $html .= ' checked="checked"';
        }
        $html .= " /><label for='lnh_flux_handball_calendrier'>Flux Calendrier de l'équipe</label><br />";
        
        
        $html .= "<input type='checkbox' name='lnh_equipe_handball[joueurs]' id='lnh_equipe_handball' value='1'";
        if($options['joueurs'] == 1){
            $html .= ' checked="checked"';
        }
        $html .= " /><label for='lnh_flux_handball_joueur'>Flux joueurs de l'équipe</label>";
        
        echo $html;
    }

    /**
     * Validation de la valeur envoyé dans le formulaire
     *
     * @param  array $input
     * @return array
     */
    public function optionValidate($input)
    {
        $options = \get_option('lnh_equipe_handball');
        $options['text_string'] = trim($input['text_string']);
        $options['classement'] = trim($input['classement']);
        $options['calendrier'] = trim($input['calendrier']);
        $options['joueurs'] = trim($input['joueurs']);

        return $options;
    }

    public function importPage()
    {
        ?>
        <h1>Import flux LNH</h1>
        <p>Import manuel des matchs de la saison</p>
        <form method="POST" action="<?php echo admin_url('admin.php');
        ?>">
            <input type="hidden" name="action" value="importPageAction" />
            <input type="submit" value="Import manuel" />
        </form>
        <?php

    }

    public function importPageAction($cron = false)
    {
        $equipe = \get_option('lnh_equipe_handball');
        $equipe_flux = strtolower($equipe['text_string']);
        
        /**
         * Import des données
         */
        if($equipe['calendrier'] == '1') $flux['calendrier'] = 'http://www.lnh.fr/remote/equipes/'.$equipe_flux.'/xml_saisonCalendrierEquipe.xml';
        if($equipe['classement'] == '1') $flux['classement'] = 'http://www.lnh.fr/remote/equipes/'.$equipe_flux.'/xml_saisonClassement.xml';
        if($equipe['joueurs'] == '1') $flux['joueurs'] = 'http://www.lnh.fr/remote/equipes/'.$equipe_flux.'/xml_saisonCompositionEquipe.xml';

        foreach ($flux as $type => $xml) {
            $objects = new Parser($xml);
            $objects->parse();
            
            foreach ($objects->getItemsCollection() as $item) {
                $importer = new Import($item);
                $importer->save();
            }
        }
        
        if(!$cron){
            /**
             * Redirection après import (pour éviter de relancer l'import)
             */
            wp_redirect($_SERVER['HTTP_REFERER']);
            exit();
        }
    }
    
    /**
     * Mise en place des shortcodes lié au plugin
     */
    public function initShortcode()
    {
        $shortcode = Shortcode::register(array('classement' => 'Classement', 'lnh-calendrier' => 'Calendrier'));
    }
}
