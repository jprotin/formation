<?php
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class FormationController extends FrameworkBundleAdminController
{
    public function formationAction(Request $request)
    {
        return $this->render('@Modules/formation/templates/admin/formation.html.twig');
    }
}