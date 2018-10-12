<?php
class AdminFormationConfigurationController extends ModuleAdminController
{
    public function __construct()
	{
		$this->display = 'view';
        parent::__construct();

        if (!$this->module->active) {
            $this->sendErrorRequest('Invalid request.');
        }		
	}
    public function initContent()
    {
        $this->context->smarty->assign(
            array(
                'text_hello'=> $this->l('Hello world !!'),
                'text_hello2'=> $this->l('Hello world 2 !!'),
            ));

        parent::initContent();

        /**
         * Exercice 5 - générer des logs
         */
        $message = "Hello world logs !!!";
        /*$message = array(
            "toto" => "tototototot",
            "titi" => "tititititit",
            "tutu" => "tututututut",
        );*/
        $severity = 1; // 1 = info, 2 = warning, 3 = error, 4 = critical
        $errorCode = "ERROR_HELLO_WORLD";
        PrestaShopLogger::addLog(print_r($message,TRUE), $severity, $errorCode,null, null,true);

    }
    public function renderView() {
        $tpl = $this->context->smarty->createTemplate($this->getTemplatePath().'exercice-4.tpl');
        return $tpl->fetch();

    }

    public function ajaxProcessFoo() {
        die('toto');
    }
}   