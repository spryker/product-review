<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductReview\Search;

use Generated\Shared\Transfer\ProductReviewSearchRequestTransfer;

interface ProductReviewSearchReaderInterface
{
    public function findProductReviews(ProductReviewSearchRequestTransfer $productReviewSearchRequestTransfer): array;

    /**
     * @return \Elastica\ResultSet|mixed|array
     */
    public function searchProductReviews();
}
