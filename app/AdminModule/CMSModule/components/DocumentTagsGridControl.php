<?php

declare(strict_types=1);

namespace App\AdminModule\CMSModule\Components;

use App\Model\ACL\RoleRepository;
use App\Model\CMS\Document\Tag;
use App\Model\CMS\Document\TagRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Kdyby\Translation\Translator;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;
use function array_keys;
use function count;

/**
 * Komponenta pro správu štítků dokumentů.
 *
 * @author Jan Staněk <jan.stanek@skaut.cz>
 * @author Petr Parolek <petr.parolek@webnazakazku.cz>
 */
class DocumentTagsGridControl extends Control
{
    /** @var Translator */
    private $translator;

    /** @var RoleRepository */
    private $roleRepository;

    /** @var TagRepository */
    private $tagRepository;


    public function __construct(Translator $translator, RoleRepository $roleRepository, TagRepository $tagRepository)
    {
        parent::__construct();

        $this->translator     = $translator;
        $this->roleRepository = $roleRepository;
        $this->tagRepository  = $tagRepository;
    }

    /**
     * Vykreslí komponentu.
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/document_tags_grid.latte');
    }

    /**
     * Vytvoří komponentu.
     * @throws DataGridException
     */
    public function createComponentDocumentTagsGrid(string $name) : void
    {
        $grid = new DataGrid($this, $name);
        $grid->setTranslator($this->translator);
        $grid->setDataSource($this->tagRepository->createQueryBuilder('t'));
        $grid->setDefaultSort(['name' => 'ASC']);
        $grid->setPagination(false);

        $grid->addColumnText('name', 'admin.cms.tags_name');

        $grid->addColumnText('roles', 'admin.cms.tags_roles', 'rolesText')
            ->setRendererOnCondition(function () {
                return $this->translator->translate('admin.cms.tags_roles_all');
            }, function (Tag $tag) {
                return count($this->roleRepository->findAll()) === $tag->getRoles()->count();
            });

        $rolesOptions = $this->roleRepository->getRolesWithoutRolesOptions([]);

        $grid->addInlineAdd()->setPositionTop()->onControlAdd[] = function ($container) use ($rolesOptions) : void {
            $container->addText('name', '')
                ->addRule(Form::FILLED, 'admin.cms.tags_name_empty')
                ->addRule(Form::IS_NOT_IN, 'admin.cms.tags_name_exists', $this->tagRepository->findAllNames());
            $container->addMultiSelect('roles', '', $rolesOptions)->setAttribute('class', 'datagrid-multiselect')
                ->setDefaultValue(array_keys($rolesOptions))
                ->addRule(Form::FILLED, 'admin.cms.tags_roles_empty');
        };
        $grid->getInlineAdd()->onSubmit[]     = [$this, 'add'];

        $grid->addInlineEdit()->onControlAdd[]  = function ($container) use ($rolesOptions) : void {
            $container->addText('name', '')
                ->addRule(Form::FILLED, 'admin.cms.tags_name_empty');
            $container->addMultiSelect('roles', '', $rolesOptions)->setAttribute('class', 'datagrid-multiselect')
                ->addRule(Form::FILLED, 'admin.cms.tags_roles_empty');
        };
        $grid->getInlineEdit()->onSetDefaults[] = function ($container, Tag $item) : void {
            $container['name']
                ->addRule(Form::IS_NOT_IN, 'admin.cms.tags_name_exists', $this->tagRepository->findOthersNames($item->getId()));

            $container->setDefaults([
                'name' => $item->getName(),
                'roles' => $this->roleRepository->findRolesIds($item->getRoles()),
            ]);
        };
        $grid->getInlineEdit()->onSubmit[]      = [$this, 'edit'];

        $grid->addAction('delete', '', 'delete!')
            ->setIcon('trash')
            ->setTitle('admin.common.delete')
            ->setClass('btn btn-xs btn-danger')
            ->addAttributes([
                'data-toggle' => 'confirmation',
                'data-content' => $this->translator->translate('admin.cms.tags_delete_confirm'),
            ]);
    }

    /**
     * Zpracuje přidání štítku dokumentu.
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AbortException
     */
    public function add(\stdClass $values) : void
    {
        $tag = new Tag();

        $tag->setName($values['name']);
        $tag->setRoles($this->roleRepository->findRolesByIds($values['roles']));

        $this->tagRepository->save($tag);

        $this->getPresenter()->flashMessage('admin.cms.tags_saved', 'success');

        $this->redirect('this');
    }

    /**
     * Zpracuje úpravu štítku dokumentu.
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AbortException
     */
    public function edit(int $id, \stdClass $values) : void
    {
        $tag = $this->tagRepository->findById($id);

        $tag->setName($values['name']);
        $tag->setRoles($this->roleRepository->findRolesByIds($values['roles']));

        $this->tagRepository->save($tag);

        $this->getPresenter()->flashMessage('admin.cms.tags_saved', 'success');

        $this->redirect('this');
    }

    /**
     * Zpracuje odstranění štítku dokumentu.
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AbortException
     */
    public function handleDelete(int $id) : void
    {
        $tag = $this->tagRepository->findById($id);
        $this->tagRepository->remove($tag);

        $this->getPresenter()->flashMessage('admin.cms.tags_deleted', 'success');

        $this->redirect('this');
    }
}
