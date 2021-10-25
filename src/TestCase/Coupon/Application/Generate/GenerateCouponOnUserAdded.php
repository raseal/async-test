<?php

declare(strict_types=1);

namespace TestCase\Coupon\Application\Generate;

use Shared\Domain\Bus\Event\DomainEventSubscriber;
use TestCase\User\Domain\UserAdded;

final class GenerateCouponOnUserAdded implements DomainEventSubscriber
{
    public function __construct(
        private CouponGenerator $coupon_generator
    ) {}

    public static function subscribedTo(): array
    {
        return [
            UserAdded::class
        ];
    }

    public function __invoke(UserAdded $event): void
    {
        // dummy class to simulate the coupon generation
        $this->coupon_generator->__invoke();
    }
}
