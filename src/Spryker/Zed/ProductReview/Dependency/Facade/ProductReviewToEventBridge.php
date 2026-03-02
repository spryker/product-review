<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductReview\Dependency\Facade;

use Spryker\Shared\Kernel\Transfer\TransferInterface;

class ProductReviewToEventBridge implements ProductReviewToEventInterface
{
    /**
     * @var \Spryker\Zed\Event\Business\EventFacadeInterface
     */
    protected $eventFacade;

    /**
     * @param \Spryker\Zed\Event\Business\EventFacadeInterface $eventFacade
     */
    public function __construct($eventFacade)
    {
        $this->eventFacade = $eventFacade;
    }

    public function trigger(string $eventName, TransferInterface $transfer): void
    {
        $this->eventFacade->trigger($eventName, $transfer);
    }
}
