<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michal
 * Date: 17.2.13
 * Time: 11:30
 * To change this template use File | Settings | File Templates.
 */
namespace FrontModule;

class ProfilePresenter extends BasePresenter
{
    public function startup()
    {
        parent::startup();
        if (!$this->context->user->isLoggedIn()) {
            $this->flashMessage('Pro přístup do profilu musíte být přihlášeni', 'error');
            $this->redirect(':Front:Page');
        }
    }


    public function renderDefault() {

    }

}