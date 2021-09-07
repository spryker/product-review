<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductReview;

use Codeception\Actor;

/**
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class ProductReviewBusinessTester extends Actor
{
    use _generated\ProductReviewBusinessTesterActions;

    /**
     * @see SpyProductReviewTableMap::COL_UPDATED_AT
     * @see SpyProductReviewTableMap::COL_CREATED_AT
     * @var array
     */
    public const DATE_FIELDS = [
        'created_at',
        'updated_at',
    ];

    /**
     * Note: for MySQL compatibility
     *
     * @param array $productReview
     *
     * @return array
     */
    public function removeProductReviewDateFields(array $productReview): array
    {
        return array_diff_key($productReview, array_flip(static::DATE_FIELDS));
    }
}
