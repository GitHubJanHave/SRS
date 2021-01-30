<?php

declare(strict_types=1);

namespace App\AdminModule\CmsModule\Presenters;

use App\AdminModule\CmsModule\Components\FaqGridControl;
use App\AdminModule\CmsModule\Components\IFaqGridControlFactory;
use App\AdminModule\CmsModule\Forms\FaqFormFactory;
use App\Model\Cms\Repositories\FaqRepository;
use Nette\Application\UI\Form;
use stdClass;

/**
 * Presenter starající se o správu častých otázek.
 *
 * @author Michal Májský
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
class FaqPresenter extends CmsBasePresenter
{
    /** @inject */
    public IFaqGridControlFactory $faqGridControlFactory;

    /** @inject */
    public FaqFormFactory $faqFormFactory;

    /** @inject */
    public FaqRepository $faqRepository;

    public function renderEdit(int $id): void
    {
    }

    protected function createComponentFaqGrid(): FaqGridControl
    {
        return $this->faqGridControlFactory->create();
    }

    protected function createComponentFaqForm(): Form
    {
        $form = $this->faqFormFactory->create((int) $this->getParameter('id'), $this->user->id);

        $form->onSuccess[] = function (Form $form, stdClass $values): void {
            if ($form->isSubmitted() === $form['cancel']) {
                $this->redirect('Faq:default');
            }

            $this->flashMessage('admin.cms.faq_saved', 'success');

            if ($form->isSubmitted() === $form['submitAndContinue']) {
                $id = $values->id ?: $this->faqRepository->findLastId(); // todo: nahradit
                $this->redirect('Faq:edit', ['id' => $id]);
            } else {
                $this->redirect('Faq:default');
            }
        };

        return $form;
    }
}
