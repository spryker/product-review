<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductReview\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\ProductReviewBuilder;
use Generated\Shared\Transfer\ProductReviewTransfer;
use Orm\Zed\ProductReview\Persistence\Map\SpyProductReviewTableMap;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use Spryker\Shared\ProductReview\ProductReviewConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductReview
 * @group Business
 * @group Facade
 * @group UpdateProductReviewStatusTest
 * Add your own group annotations below this line
 */
class UpdateProductReviewStatusTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\ProductReview\ProductReviewBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testUpdateProductReviewStatusPersistsChangesToDatabase(): void
    {
        // Arrange
        $productReviewTransfer = $this->tester->haveProductReview();
        $productReviewTransfer = $this->tester->getFacade()->createProductReview($productReviewTransfer);

        $productReviewTransferToUpdate = (new ProductReviewBuilder([
            ProductReviewTransfer::ID_PRODUCT_REVIEW => $productReviewTransfer->getIdProductReview(),
            ProductReviewTransfer::STATUS => SpyProductReviewTableMap::COL_STATUS_APPROVED,
        ]))->build();

        // Act
        $updatedProductReviewTransfer = $this->tester->getFacade()->updateProductReviewStatus($productReviewTransferToUpdate);

        // Assert
        $actualProductReviewTransfer = $this->tester->getFacade()->findProductReview($updatedProductReviewTransfer);
        $this->assertSame($productReviewTransferToUpdate->getStatus(), $actualProductReviewTransfer->getStatus(), 'Updated product review should have expected data.');
    }

    /**
     * @skip This test was temporarily skipped due to flikerness. See {@link https://spryker.atlassian.net/browse/CC-25718} for details
     *
     * @dataProvider statusDataProvider
     *
     * @param string $inputStatus
     * @param bool $isTouchActive
     *
     * @return void
     */
    public function testUpdateProductReviewStatusTouchesProductReviewSearchResource(string $inputStatus, bool $isTouchActive): void
    {
        // Arrange
        $productReviewTransfer = $this->tester->haveProductReview();
        $productReviewTransfer = $this->tester->getFacade()->createProductReview($productReviewTransfer);

        $productReviewTransferToUpdate = (new ProductReviewBuilder([
            ProductReviewTransfer::ID_PRODUCT_REVIEW => $productReviewTransfer->getIdProductReview(),
            ProductReviewTransfer::STATUS => $inputStatus,
        ]))->build();

        // Act
        $this->tester->getFacade()->updateProductReviewStatus($productReviewTransferToUpdate);

        // Assert
        if ($isTouchActive) {
            $this->tester->assertTouchActive(ProductReviewConfig::RESOURCE_TYPE_PRODUCT_REVIEW, $productReviewTransferToUpdate->getIdProductReview(), 'Product review should have been touched as active.');

            return;
        }

        $this->tester->assertTouchDeleted(ProductReviewConfig::RESOURCE_TYPE_PRODUCT_REVIEW, $productReviewTransferToUpdate->getIdProductReview(), 'Product review should have been touched as deleted.');
    }

    /**
     * @return array
     */
    public function statusDataProvider(): array
    {
        return [
            'pending status' => [SpyProductReviewTableMap::COL_STATUS_PENDING, false],
            'approved status' => [SpyProductReviewTableMap::COL_STATUS_APPROVED, true],
            'rejected status' => [SpyProductReviewTableMap::COL_STATUS_REJECTED, false],
        ];
    }

    /**
     * @dataProvider statusDataProvider
     *
     * @param string $inputStatus
     *
     * @return void
     */
    public function testUpdateProductReviewStatusTouchesProductReviewAbstractSearchResource(string $inputStatus): void
    {
        // Arrange
        $productReviewTransfer = $this->tester->haveProductReview();
        $productReviewTransfer = $this->tester->getFacade()->createProductReview($productReviewTransfer);

        $productReviewTransferToUpdate = (new ProductReviewBuilder([
            ProductReviewTransfer::ID_PRODUCT_REVIEW => $productReviewTransfer->getIdProductReview(),
            ProductReviewTransfer::STATUS => $inputStatus,
        ]))->build();

        // Act
        $this->tester->getFacade()->updateProductReviewStatus($productReviewTransferToUpdate);

        // Assert
        $this->tester->assertTouchActive(ProductReviewConfig::RESOURCE_TYPE_PRODUCT_ABSTRACT_REVIEW, $productReviewTransferToUpdate->getFkProductAbstract(), 'Product review abstract should have been touched as active.');
    }

    /**
     * @return void
     */
    public function testUpdateProductReviewReturnsUpdatedTransfer(): void
    {
        // Arrange
        $productReviewTransfer = $this->tester->haveProductReview();
        $productReviewTransfer = $this->tester->getFacade()->createProductReview($productReviewTransfer);

        $productReviewTransferToUpdate = (new ProductReviewBuilder([
            ProductReviewTransfer::ID_PRODUCT_REVIEW => $productReviewTransfer->getIdProductReview(),
            ProductReviewTransfer::STATUS => SpyProductReviewTableMap::COL_STATUS_APPROVED,
        ]))->build();

        // Act
        $actualProductReviewTransfer = $this->tester->getFacade()->updateProductReviewStatus($productReviewTransferToUpdate);

        // Assert
        $expectedProductReviewTransfer = $this->tester->getFacade()->findProductReview($productReviewTransfer);
        $this->assertSame(
            $this->tester->removeProductReviewDateFields($actualProductReviewTransfer->toArray()),
            $this->tester->removeProductReviewDateFields($expectedProductReviewTransfer->toArray()),
            'Updated product review should have been returned.',
        );
    }

    /**
     * @return void
     */
    public function testUpdateProductReviewStatusThrowsExceptionWhenProductReviewIdIsNotProvidedInTransfer(): void
    {
        // Arrange
        $productReviewTransferToUpdate = (new ProductReviewBuilder([
            ProductReviewTransfer::ID_PRODUCT_REVIEW => rand(),
        ]))->build();

        // Assert
        $this->expectException(RequiredTransferPropertyException::class);

        // Act
        $this->tester->getFacade()->updateProductReviewStatus($productReviewTransferToUpdate);
    }

    /**
     * @return void
     */
    public function testUpdateProductReviewStatusThrowsExceptionWhenProductReviewStatusIsNotProvidedInTransfer(): void
    {
        // Arrange
        $productReviewTransferToUpdate = (new ProductReviewBuilder([
            ProductReviewTransfer::STATUS => SpyProductReviewTableMap::COL_STATUS_APPROVED,
        ]))->build();

        // Assert
        $this->expectException(RequiredTransferPropertyException::class);

        // Act
        $this->tester->getFacade()->updateProductReviewStatus($productReviewTransferToUpdate);
    }
}
