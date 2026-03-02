<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductReview\Business\Trigger;

use Generated\Shared\Transfer\ProductReviewTransfer;

interface ProductEventTriggerInterface
{
    public function triggerProductUpdateEvent(ProductReviewTransfer $productReviewTransfer): void;
}
