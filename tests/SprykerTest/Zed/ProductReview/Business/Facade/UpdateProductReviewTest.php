<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductReview\Business\Facade;

use Codeception\TestCase\Test;
use Generated\Shared\DataBuilder\ProductReviewBuilder;
use Generated\Shared\Transfer\ProductReviewTransfer;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group ProductReview
 * @group Business
 * @group Facade
 * @group UpdateProductReviewTest
 * Add your own group annotations below this line
 */
class UpdateProductReviewTest extends Test
{

    /**
     * @var \SprykerTest\Zed\ProductReview\ProductReviewBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testUpdateProductReviewPersistsChangesToDatabase()
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $productReviewTransfer = (new ProductReviewBuilder([
            ProductReviewTransfer::CUSTOMER_REFERENCE => $customerTransfer->getCustomerReference(),
        ]))->build();
        $productReviewTransfer = $this->tester->getFacade()->createProductReview($productReviewTransfer);

        $productReviewTransferToUpdate = (new ProductReviewBuilder([
            ProductReviewTransfer::ID_PRODUCT_REVIEW => $productReviewTransfer->getIdProductReview(),
        ]))->build();

        // Act
        $updatedProductReviewTransfer = $this->tester->getFacade()->updateProductReview($productReviewTransferToUpdate);

        // Assert
        $actualProductReviewTransfer = $this->tester->getFacade()->findProductReview($updatedProductReviewTransfer);
        $this->assertArraySubset($productReviewTransferToUpdate->modifiedToArray(), $actualProductReviewTransfer->toArray(), 'Updated product review should have expected data.');
    }

    /**
     * @return void
     */
    public function testUpdateProductReviewTouchesProductReviewSearchResource()
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $productReviewTransfer = (new ProductReviewBuilder([
            ProductReviewTransfer::CUSTOMER_REFERENCE => $customerTransfer->getCustomerReference(),
        ]))->build();
        $productReviewTransfer = $this->tester->getFacade()->createProductReview($productReviewTransfer);

        $productReviewTransferToUpdate = (new ProductReviewBuilder([
            ProductReviewTransfer::ID_PRODUCT_REVIEW => $productReviewTransfer->getIdProductReview(),
        ]))->build();

        // Act
        $this->tester->getFacade()->updateProductReview($productReviewTransferToUpdate);

        // Assert
        $this->tester->assertTouchActive(ProductReviewConfig::RESOURCE_TYPE_PRODUCT_Review, $productReviewTransferToUpdate->getIdProductReview(), 'Product review should have been touched as active.');
    }

}
