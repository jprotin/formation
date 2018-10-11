<?php
class AdminFormationConfigurationController extends AdminController
{

    public function initContent()
    {
        parent::initContent();

        // Le template smarty

        $content = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'formation/views/templates/admin/exercice-4.tpl');

        //assign le contenu
        $this->context->smarty->assign(
            array(
                'content' => $this->content . $content,
            )
        );

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
}   