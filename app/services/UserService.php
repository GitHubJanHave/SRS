<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\Enums\PaymentType;
use App\Model\User\ApplicationRepository;
use App\Model\User\User;
use App\Model\User\UserRepository;
use Kdyby\Translation\Translator;
use Nette;

/**
 * Služba pro správu uživatelů.
 *
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
class UserService
{
    use Nette\SmartObject;

    /** @var Translator */
    private $translator;

    /** @var UserRepository */
    private $userRepository;

    /** @var ApplicationRepository */
    private $applicationRepository;


    public function __construct(
        Translator $translator,
        UserRepository $userRepository,
        ApplicationRepository $applicationRepository
    ) {
        $this->translator            = $translator;
        $this->userRepository        = $userRepository;
        $this->applicationRepository = $applicationRepository;
    }

    public function getMembershipText(User $user) : string
    {
        if ($user->getUnit() !== null) {
            return $user->getUnit();
        }

        if ($user->isMember()) {
            return $this->translator->translate('admin.users.users_membership_no');
        }

        if ($user->isExternalLector()) {
            return $this->translator->translate('admin.users.users_membership_external');
        }

        return $this->translator->translate('admin.users.users_membership_not_connected');
    }

    public function getPaymentMethod(User $user) : ?string
    {
        $paymentMethod = null;

        foreach ($user->getNotCanceledApplications() as $application) {
            $currentPaymentMethod = $application->getPaymentMethod();
            if (! $currentPaymentMethod) {
                continue;
            }
            if (! $paymentMethod) {
                $paymentMethod = $currentPaymentMethod;
                continue;
            }
            if ($paymentMethod !== $currentPaymentMethod) {
                return PaymentType::MIXED;
            }
        }

        if ($paymentMethod) {
            return $paymentMethod;
        }

        return null;
    }
}
