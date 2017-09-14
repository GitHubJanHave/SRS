<?php

namespace App\WebModule\Presenters;

use App\Model\ACL\Role;
use App\Model\Enums\PaymentType;
use App\Model\Mailing\Template;
use App\Model\Mailing\TemplateVariable;
use App\Model\Settings\Settings;
use App\Model\Structure\SubeventRepository;
use App\Services\ApplicationService;
use App\Services\Authenticator;
use App\Services\ExcelExportService;
use App\Services\MailService;
use App\Services\PdfExportService;
use App\WebModule\Components\ApplicationsGridControl;
use App\WebModule\Components\IApplicationsGridControlFactory;
use App\WebModule\Forms\AdditionalInformationForm;
use App\WebModule\Forms\PersonalDetailsForm;
use App\WebModule\Forms\RolesForm;
use App\WebModule\Forms\SubeventsForm;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Application\UI\Form;


/**
 * Presenter obsluhující profil uživatele.
 *
 * @author Michal Májský
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
class ProfilePresenter extends WebBasePresenter
{
    /**
     * @var PersonalDetailsForm
     * @inject
     */
    public $personalDetailsFormFactory;

    /**
     * @var IApplicationsGridControlFactory
     * @inject
     */
    public $applicationsGridControlFactory;

    /**
     * @var AdditionalInformationForm
     * @inject
     */
    public $additionalInformationFormFactory;

    /**
     * @var PdfExportService
     * @inject
     */
    public $pdfExportService;

    /**
     * @var ExcelExportService
     * @inject
     */
    public $excelExportService;

    /**
     * @var Authenticator
     * @inject
     */
    public $authenticator;

    /**
     * @var SubeventRepository
     * @inject
     */
    public $subeventRepository;

    /**
     * @var MailService
     * @inject
     */
    public $mailService;

    /**
     * @var ApplicationService
     * @inject
     */
    public $applicationService;


    public function startup()
    {
        parent::startup();

        if (!$this->user->isLoggedIn()) {
            $this->flashMessage('web.common.login_required', 'danger', 'lock');
            $this->redirect(':Web:Page:default');
        }
    }

    public function renderDefault()
    {
        $this->template->pageName = $this->translator->translate('web.profile.title');
        $this->template->paymentMethodBank = PaymentType::BANK;
        $this->template->editRegistrationAllowed = $this->applicationService->isAllowedEditRegistration($this->dbuser);
    }

    /**
     * Odhlásí uživatele ze semináře.
     */
    public function actionCancelRegistration()
    {
        $this->mailService->sendMailFromTemplate(new ArrayCollection(), new ArrayCollection([$this->dbuser]), '', Template::REGISTRATION_CANCELED, [
            TemplateVariable::SEMINAR_NAME => $this->settingsRepository->getValue(Settings::SEMINAR_NAME)
        ]);

        $this->userRepository->remove($this->dbuser);

        $this->redirect(':Auth:logout');
    }

    /**
     * Vyexportuje rozvrh uživatele.
     */
    public function actionExportSchedule()
    {
        $user = $this->userRepository->findById($this->user->id);
        $response = $this->excelExportService->exportUsersSchedule($user, "harmonogram-seminare.xlsx");
        $this->sendResponse($response);
    }

    protected function createComponentPersonalDetailsForm()
    {
        $form = $this->personalDetailsFormFactory->create($this->user->id);

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $this->flashMessage('web.profile.personal_details_update_successful', 'success');

            $this->redirect('this#collapsePersonalDetails');
        };

        $this->personalDetailsFormFactory->onSkautIsError[] = function () {
            $this->flashMessage('web.profile.personal_details_synchronization_failed', 'danger');
        };

        return $form;
    }

    protected function createComponentAdditionalInformationForm()
    {
        $form = $this->additionalInformationFormFactory->create($this->user->id);

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $this->flashMessage('web.profile.additional_information_update_successfull', 'success');

            $this->redirect('this#collapseAdditionalInformation');
        };

        return $form;
    }

    protected function createComponentApplicationsGrid()
    {
        return $this->applicationsGridControlFactory->create();
    }
}
