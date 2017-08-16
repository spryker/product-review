<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductReview\Zed;

use Generated\Shared\Transfer\ProductReviewRequestTransfer;
use Spryker\Client\ZedRequest\Stub\ZedRequestStub;

class ProductReviewStub extends ZedRequestStub implements ProductReviewStubInterface
{

    /**
     * @param \Generated\Shared\Transfer\ProductReviewRequestTransfer $productReviewRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ProductReviewTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function submitCustomerReview(ProductReviewRequestTransfer $productReviewRequestTransfer)
    {
        return $this->zedStub->call('/product-review/gateway/submit-customer-review', $productReviewRequestTransfer);
    }

}
