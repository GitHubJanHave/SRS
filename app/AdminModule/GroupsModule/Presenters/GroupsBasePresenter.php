<?php

declare(strict_types=1);

namespace App\AdminModule\GroupsModule\Presenters;

use App\AdminModule\Presenters\AdminBasePresenter;
use App\Model\Acl\Permission;
use App\Model\Acl\SrsResource;
use Nette\Application\AbortException;

/**
 * Basepresenter pro ProgramModule.
 */
abstract class GroupsBasePresenter extends AdminBasePresenter
{
    protected string $resource = SrsResource::GROUPS;

    /** @throws AbortException */
    public function startup(): void
    {
        parent::startup();

        $this->checkPermission(Permission::MANAGE);
    }
}
