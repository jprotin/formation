<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Formation extends Module
{
    protected $config_form = false;
    protected $class_names = array(
        'AdminFormationConfiguration',
    );

    // Available from 1.7.5 ############
    public $tabs = array(
        array(
            'name' => 'Formation Sf', // One name for all langs
            'class_name' => 'Formation',
            'visible' => true,
            'parent_class_name' => 'AdminCatalog',
    ));
    //##################################
    public function __construct()
    {
        $this->name = 'formation';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Johan PROTIN';
        /**
         * S'il est défini sur 0, le module ne sera pas chargé et 
         * dépensera donc moins de ressources pour générer la page «Modules». 
         * Si votre module doit afficher un message d'avertissement 
         * dans la page «Modules», vous devez définir cet attribut sur 1.
         */
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Formation');
        $this->description = $this->l('Formation PrestaShop');

        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir supprimer le module ?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('FORMATION_LIVE_MODE', false);

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayBackOfficeFooter') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayHome') &&
            $this->registerHook('displayLeftColumn') &&
            $this->registerHook('ActionCronJob') &&
            $this->createAdminTab();
    }

    public function uninstall()
    {
        Configuration::deleteByName('FORMATION_LIVE_MODE');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Generate a link in the menu side (Exercice 4)
     */
    protected function createAdminTab()
    {
        foreach ($this->class_names as $class_name) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->module = $this->name;
            $tab->class_name = $class_name;
            $tab->id_parent = 2;
            $tab->position = 6;

            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = $this->name;
            }
            if (!$tab->save()) {
                return false;
            }
        }
        return true;
    }
    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitFormationModule')) == true) {
            $mode = 'insert';
            $id_for = (int)Tools::getValue('id_formation');
            if(!empty($id_for) && $id_for > 0) {
                $mode = 'update';
            }
            if ( $this->postProcess($mode) ) {
                $this->displayConfirmation('Thank you, successful registration !');
            }
        }
        if ((bool)Tools::isSubmit('deleteformation') == true ) {
            $mode = 'delete';
            if ( $this->postProcess($mode) ) {
                $this->displayConfirmation('Thank you, successful registration !');
            }
            
        }

        $this->context->smarty->assign('formation_description', $this->l('Welcome to the Formation extension'));

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->createList().$this->renderForm();//exercice 2 et 3
    }


    // EXERCICE 2 et 3
    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFormationModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array($this->getConfigForm()));
    }
    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Create a new formation'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_formation',
                        'label' => $this->l('ID'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'for_libelle',
                        'label' => $this->l('Libellé'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'for_description',
                        'label' => $this->l('Description'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }
     /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $id_formation = Tools::getValue('id_formation');
        $for_libelle = '';
        $for_description = '';
        if((bool)Tools::isSubmit('updateformation') && !empty($id_formation)){
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('formation', 'f');
            $sql->where('f.id_formation = '.$id_formation);
            $result =  Db::getInstance()->executeS($sql);
            $for_libelle = $result[0]['for_libelle'];
            $for_description = $result[0]['for_description'];
        }        

        return array(
            'id_formation' => (bool)Tools::isSubmit('updateformation') ? $id_formation:'',
            'for_libelle' => $for_libelle,
            'for_description' => $for_description,
        );
    }

    private function createList()
    {
        $fields_list = array(
            'id_formation' => array(
                'title' => $this->l('ID'),
                'width' => 140,
                'type' => 'text',
            ),
            'for_libelle' => array(
                'title' => $this->l('Libellé'),
                'width' => 140,
                'type' => 'text',
            ),
        );
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        // Actions to be displayed in the "Actions" column
        $helper->actions = array(
            'edit', 'delete'
        );
        $helper->identifier = 'id_formation';
        $helper->show_toolbar = true;
        $helper->title = 'List of formations';
        $helper->table = 'formation';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $formation_data='';
        // generate SQL request
        $sql = 'SELECT * FROM '._DB_PREFIX_.'formation';
        $results = Db::getInstance()->ExecuteS($sql);
        if (empty($results)) {
            $formation_data = 'Nothing';
        } else {
            $formation_data = $results;
        }
        return $helper->generateList($formation_data,$fields_list);
    }
    // ------------ FIN EXERCICE 2 & 3
    /**
     * Save form data.
     */
    protected function postProcess($mode = 'insert')
    {
        $for_libelle        = Tools::getValue('for_libelle');
        $for_description    = Tools::getValue('for_description');

        if ($mode == 'insert') {
            return Db::getInstance()->insert('formation', array(
                'for_libelle' => pSQL($for_libelle),
                'for_description' => pSQL($for_description),
            ));
        } elseif ($mode == 'update') {
            return Db::getInstance()->update(
                'formation', 
                array(
                    'for_libelle'       => pSQL($for_libelle), 
                    'for_description'   => pSQL($for_description)
                ),
                'id_formation = '.(int)Tools::getValue('id_formation')
            );
        } elseif ($mode == 'delete') {
            return Db::getInstance()->delete(
                'formation', 
                'id_formation = '. (int)Tools::getValue('id_formation')
            );
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayBackOfficeFooter()
    {
        /* Place your code here. */
    }

    public function hookDisplayBackOfficeHeader()
    {
        /* Place your code here. */
    }

    public function hookDisplayFooter()
    {
        /* Place your code here. */
    }

    public function hookDisplayHeader()
    {
        /* Place your code here. */
    }

    public function hookDisplayHome()
    {
        return $this->hookDisplayLeftColumn();
    }
    public function hookDisplayLeftColumn()
    {
        $this->context->smarty->assign([
            'formation_title' => $this->l('Formation - Exercice 1'),
            'formation_text' => $this->l('Hello World!')
        ]);

        return $this->display(__FILE__, 'exercice-1.tpl');
    }

    /**
     * Exercice 6 gestion de la tâhe cron
     */
     /**
     * Hook d'exécution de la crontab
     */
    public function hookActionCronJob() {
 
        //Exemple basique on va créer un fichier de log et insérer un contenu dès que la tache cron est appellée
        $fp = fopen(dirname(__FILE__) . '/cron.log', 'a+');
        fputs($fp, 'CALLED at ' . date('Y-m-d H:i:s'));
        fclose($fp);
 
        //Exemple plus avancé, on souhaite effectuer des taches différentes en fonction de l'heure
        $hour = date('H');
 
        switch ($hour) {
            case 07:
                //Lancement des actions du matin
                break;
 
            case 12:
                //Lancement des actions du midi
                break;
            case 18:
                //Lancement des actions du soir
                break;
            default:
                //Action par défaut
                $message = "Hello world Cron  !!!";
                $severity = 2; // 1 = info, 2 = warning, 3 = error, 4 = critical
                $errorCode = "ERROR_HELLO_WORLD";
                PrestaShopLogger::addLog(print_r($message,TRUE), $severity, $errorCode,null, null,true);
                break;
        }
    }
 
    /**
     * Information sur la fréquence des taches cron du module
     * Granularité maximume à l'heure
     */
    public function getCronFrequency() {
        return array(
            'hour' => -1, // -1 equivalent à * en cron normal
            'day' => -1, 
            'month' => -1,
            'day_of_week' => -1
        );
    }
}
