<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\ProductReview;

use Spryker\Shared\Kernel\AbstractBundleConfig;

class ProductReviewConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const RESOURCE_TYPE_PRODUCT_REVIEW = 'product_review';

    /**
     * @var string
     */
    public const RESOURCE_TYPE_PRODUCT_ABSTRACT_REVIEW = 'product_abstract_review';

    /**
     * @var string
     */
    public const ELASTICSEARCH_INDEX_TYPE_NAME = 'product-review';
}
