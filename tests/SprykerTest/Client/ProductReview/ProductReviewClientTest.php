<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Client\ProductReview;

use Codeception\Test\Unit;
use Spryker\Client\ProductReview\Dependency\Client\ProductReviewToSearchInterface;
use Spryker\Client\ProductReview\ProductReviewDependencyProvider;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Client
 * @group ProductReview
 * @group ProductReviewClientTest
 * Add your own group annotations below this line
 */
class ProductReviewClientTest extends Unit
{
    /**
     * @uses \Spryker\Client\ProductReview\ProductViewExpander\ProductViewExpander::KEY_RATING_AGGREGATION
     *
     * @var string
     */
    protected const KEY_RATING_AGGREGATION = 'ratingAggregation';

    /**
     * @uses \Spryker\Client\ProductReview\ProductViewExpander\ProductViewExpander::KEY_PRODUCT_BULK_AGGREGATION
     *
     * @var string
     */
    protected const KEY_PRODUCT_BULK_AGGREGATION = 'productAggregation';

    /**
     * @var int
     */
    protected const TEST_ID_PRODUCT_ABSTRACT = 1;

    /**
     * @var int
     */
    protected const TEST_ID_PRODUCT_ABSTRACT_2 = 2;

    /**
     * @var int
     */
    protected const TEST_ID_PRODUCT_ABSTRACT_3 = 3;

    /**
     * @var int
     */
    protected const TEST_ID_PRODUCT_ABSTRACT_4 = 888;

    /**
     * @var \SprykerTest\Client\ProductReview\ProductReviewClientTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testExpandProductViewBulkWithProductReviewData(): void
    {
        // Arrange
        $clientSearchMockResponse = $this->getClientSearchMockResponse();
        $this->mockSearchResult($clientSearchMockResponse);

        $productAbstractIds = array_keys($clientSearchMockResponse['productAggregation']);
        $productViewTransfers = $this->tester->createProductViewTransfers($productAbstractIds);
        $bulkProductReviewSearchRequestTransfer = $this->tester->createBulkProductReviewSearchRequestTransfer($productAbstractIds);

        // Act
        $expandedProductViewsTransfers = $this->tester->getClient()
            ->expandProductViewBulkWithProductReviewData($productViewTransfers, $bulkProductReviewSearchRequestTransfer);

        // Assert
        $expectedAverageRating = $this->getExpectedAverageRating();
        foreach ($expandedProductViewsTransfers as $productViewTransfer) {
            $this->assertSame(
                $expectedAverageRating[$productViewTransfer->getIdProductAbstract()],
                $productViewTransfer->getRatingOrFail()->getAverageRating(),
            );
        }
    }

    /**
     * @param array $returnedContent
     *
     * @return void
     */
    protected function mockSearchResult(array $returnedContent): void
    {
        $contentToStorageBridge = $this->getMockBuilder(ProductReviewToSearchInterface::class)->getMock();
        $contentToStorageBridge->method('search')->willReturn($returnedContent);
        $contentToStorageBridge->method('expandQuery')->willReturn($this->createQueryMock());

        $this->tester->setDependency(ProductReviewDependencyProvider::CLIENT_SEARCH, $contentToStorageBridge);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Client\Search\Dependency\Plugin\QueryInterface
     */
    protected function createQueryMock(): QueryInterface
    {
        return $this->createMock(QueryInterface::class);
    }

    /**
     * @return array<array<array<array<int>>>>
     */
    protected function getClientSearchMockResponse(): array
    {
        return [
            static::KEY_PRODUCT_BULK_AGGREGATION => [
                static::TEST_ID_PRODUCT_ABSTRACT => [
                    static::KEY_RATING_AGGREGATION => [
                        5 => 3,
                        2 => 1,
                    ],
                ],
                static::TEST_ID_PRODUCT_ABSTRACT_2 => [
                    static::KEY_RATING_AGGREGATION => [
                        5 => 3,
                        1 => 10,
                    ],
                ],
                static::TEST_ID_PRODUCT_ABSTRACT_3 => [
                    static::KEY_RATING_AGGREGATION => [
                        5 => 130,
                        4 => 33,
                        3 => 21,
                        2 => 10,
                        1 => 5,
                    ],
                ],
                static::TEST_ID_PRODUCT_ABSTRACT_4 => [
                    static::KEY_RATING_AGGREGATION => [
                        5 => 3,
                        1 => 1,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getExpectedAverageRating(): array
    {
        return [
            static::TEST_ID_PRODUCT_ABSTRACT => 4.3,
            static::TEST_ID_PRODUCT_ABSTRACT_2 => 1.9,
            static::TEST_ID_PRODUCT_ABSTRACT_3 => 4.4,
            static::TEST_ID_PRODUCT_ABSTRACT_4 => 4.0,
        ];
    }
}
