<?php

declare(strict_types=1);

namespace App\AdminModule\MailingModule\Components;

use App\Model\ACL\Role;
use App\Model\ACL\RoleRepository;
use App\Model\Mailing\MailRepository;
use App\Model\Structure\SubeventRepository;
use App\Utils\Helpers;
use Doctrine\ORM\QueryBuilder;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Ublaboo\DataGrid\DataGrid;

/**
 * Komponenta pro výpis historie e-mailů.
 *
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
class MailHistoryGridControl extends Control
{
    /** @var Translator */
    private $translator;

    /** @var MailRepository */
    private $mailRepository;

    /** @var RoleRepository */
    private $roleRepository;

    /** @var SubeventRepository */
    private $subeventRepository;


    public function __construct(
        Translator $translator,
        MailRepository $mailRepository,
        RoleRepository $roleRepository,
        SubeventRepository $subeventRepository
    ) {
        parent::__construct();

        $this->translator         = $translator;
        $this->mailRepository     = $mailRepository;
        $this->roleRepository     = $roleRepository;
        $this->subeventRepository = $subeventRepository;
    }

    /**
     * Vykreslí komponentu.
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/mail_history_grid.latte');
    }

    /**
     * Vytvoří komponentu.
     */
    public function createComponentMailHistoryGrid(string $name) : void
    {
        $grid = new DataGrid($this, $name);
        $grid->setTranslator($this->translator);
        $grid->setDataSource($this->mailRepository->createQueryBuilder('m'));
        $grid->setDefaultSort(['datetime' => 'DESC']);
        $grid->setItemsPerPageList([25, 50, 100, 250, 500]);
        $grid->setStrictSessionFilterValues(false);

        $grid->addColumnText('recipientRoles', 'admin.mailing.history.recipient_roles', 'recipientRolesText')
            ->setFilterMultiSelect($this->roleRepository->getRolesWithoutRolesOptions([Role::GUEST, Role::UNAPPROVED, Role::NONREGISTERED]))
            ->setCondition(function (QueryBuilder $qb, $values) : void {
                $qb->join('m.recipientRoles', 'r')
                    ->andWhere('r.id IN (:rids)')
                    ->setParameter('rids', $values);
            });

        $grid->addColumnText('recipientSubevents', 'admin.mailing.history.recipient_subevents', 'recipientSubeventsText')
            ->setFilterMultiSelect($this->subeventRepository->getSubeventsOptions())
            ->setCondition(function (QueryBuilder $qb, $values) : void {
                $qb->join('m.recipientSubevents', 's')
                    ->andWhere('s.id IN (:sids)')
                    ->setParameter('sids', $values);
            });

        $grid->addColumnText('recipientUsers', 'admin.mailing.history.recipient_users', 'recipientUsersText')
            ->setFilterText()
            ->setCondition(function (QueryBuilder $qb, $value) : void {
                $qb->join('m.recipientUsers', 'u')
                    ->andWhere('u.displayName LIKE :displayName')
                    ->setParameter('displayName', '%' . $value . '%');
            });

        $grid->addColumnText('subject', 'admin.mailing.history.subject')
            ->setFilterText();

        $grid->addColumnDateTime('datetime', 'admin.mailing.history.datetime')
            ->setFormat(Helpers::DATETIME_FORMAT);

        $grid->addColumnText('automatic', 'admin.mailing.history.automatic')
            ->setReplacement([
                '0' => $this->translator->translate('admin.common.no'),
                '1' => $this->translator->translate('admin.common.yes'),
            ])
            ->setFilterSelect([
                '' => 'admin.common.all',
                '0' => 'admin.common.no',
                '1' => 'admin.common.yes',
            ])
            ->setTranslateOptions();
    }
}
