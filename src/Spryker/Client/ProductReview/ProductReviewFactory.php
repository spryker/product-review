<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductReview;

use Generated\Shared\Transfer\BulkProductReviewSearchRequestTransfer;
use Generated\Shared\Transfer\ProductReviewSearchRequestTransfer;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ProductReview\Calculator\ProductReviewSummaryCalculator;
use Spryker\Client\ProductReview\Calculator\ProductReviewSummaryCalculatorInterface;
use Spryker\Client\ProductReview\Plugin\Elasticsearch\Query\BulkProductReviewsQueryPlugin;
use Spryker\Client\ProductReview\Plugin\Elasticsearch\Query\ProductReviewsQueryPlugin;
use Spryker\Client\ProductReview\ProductViewExpander\ProductViewExpander;
use Spryker\Client\ProductReview\ProductViewExpander\ProductViewExpanderInterface;
use Spryker\Client\ProductReview\ResultFormatter\ProductRatingAggreagationResultFormatter;
use Spryker\Client\ProductReview\ResultFormatter\ResultFormatterInterface;
use Spryker\Client\ProductReview\Search\ProductReviewSearchReader;
use Spryker\Client\ProductReview\Search\ProductReviewSearchReaderInterface;
use Spryker\Client\ProductReview\Storage\ProductAbstractReviewStorageReader;
use Spryker\Client\ProductReview\Zed\ProductReviewStub;
use Spryker\Shared\ProductReview\KeyBuilder\ProductAbstractReviewResourceKeyBuilder;

class ProductReviewFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Client\ProductReview\Zed\ProductReviewStubInterface
     */
    public function createProductReviewStub()
    {
        return new ProductReviewStub($this->getZedRequestClient());
    }

    /**
     * @param \Generated\Shared\Transfer\ProductReviewSearchRequestTransfer $productReviewSearchRequestTransfer
     *
     * @return \Spryker\Client\Search\Dependency\Plugin\QueryInterface
     */
    public function createProductReviewsQueryPlugin(ProductReviewSearchRequestTransfer $productReviewSearchRequestTransfer)
    {
        $productReviewsQueryPlugin = new ProductReviewsQueryPlugin($productReviewSearchRequestTransfer);

        return $this->getSearchClient()->expandQuery(
            $productReviewsQueryPlugin,
            $this->getProductReviewsQueryExpanderPlugins(),
            $productReviewSearchRequestTransfer->getRequestParams()
        );
    }

    /**
     * @return \Spryker\Client\ProductReview\Dependency\Client\ProductReviewToSearchInterface
     */
    public function getSearchClient()
    {
        return $this->getProvidedDependency(ProductReviewDependencyProvider::CLIENT_SEARCH);
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\QueryExpanderPluginInterface[]
     */
    protected function getProductReviewsQueryExpanderPlugins()
    {
        return $this->getProvidedDependency(ProductReviewDependencyProvider::PRODUCT_REVIEWS_QUERY_EXPANDER_PLUGINS);
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\ResultFormatterPluginInterface[]
     */
    public function getProductReviewsSearchResultFormatterPlugins()
    {
        return $this->getProvidedDependency(ProductReviewDependencyProvider::PRODUCT_REVIEWS_SEARCH_RESULT_FORMATTER_PLUGINS);
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\PaginationConfigBuilderInterface
     */
    public function getPaginationConfigBuilder()
    {
        $paginationConfigBuilder = $this->getProvidedDependency(ProductReviewDependencyProvider::PAGINATION_CONFIG_BUILDER_PLUGIN);
        $paginationConfigBuilder->setPagination($this->getConfig()->getPaginationConfig());

        return $paginationConfigBuilder;
    }

    /**
     * @return \Spryker\Client\ProductReview\Storage\ProductAbstractReviewStorageReaderInterface
     */
    public function createProductAbstractReviewStorageReader()
    {
        return new ProductAbstractReviewStorageReader(
            $this->getStorageClient(),
            $this->createProductAbstractReviewResourceKeyBuilder()
        );
    }

    /**
     * @deprecated use getProductReviewConfig
     *
     * @return \Spryker\Client\ProductReview\ProductReviewConfig|\Spryker\Client\Kernel\AbstractBundleConfig
     */
    public function getConfig()
    {
        return parent::getConfig();
    }

    /**
     * @return \Spryker\Client\ProductReview\ProductReviewConfig|\Spryker\Client\Kernel\AbstractBundleConfig
     */
    public function getProductReviewConfig()
    {
        return parent::getConfig();
    }

    /**
     * @return \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected function getZedRequestClient()
    {
        return $this->getProvidedDependency(ProductReviewDependencyProvider::CLIENT_ZED_REQUEST);
    }

    /**
     * @return \Spryker\Client\ProductReview\Dependency\Client\ProductReviewToStorageInterface
     */
    protected function getStorageClient()
    {
        return $this->getProvidedDependency(ProductReviewDependencyProvider::CLIENT_STORAGE);
    }

    /**
     * @return \Spryker\Shared\KeyBuilder\KeyBuilderInterface
     */
    protected function createProductAbstractReviewResourceKeyBuilder()
    {
        return new ProductAbstractReviewResourceKeyBuilder();
    }

    /**
     * @return \Spryker\Client\ProductReview\Calculator\ProductReviewSummaryCalculatorInterface
     */
    public function createProductReviewSummaryCalculator(): ProductReviewSummaryCalculatorInterface
    {
        return new ProductReviewSummaryCalculator($this->getProductReviewConfig());
    }

    /**
     * @param \Generated\Shared\Transfer\ProductReviewSearchRequestTransfer $productReviewSearchRequestTransfer
     *
     * @return \Spryker\Client\ProductReview\ProductViewExpander\ProductViewExpanderInterface
     */
    public function createProductViewExpander(ProductReviewSearchRequestTransfer $productReviewSearchRequestTransfer): ProductViewExpanderInterface
    {
        return new ProductViewExpander(
            $this->createProductReviewSummaryCalculator(),
            $this->createProductReviewSearchReader($productReviewSearchRequestTransfer)
        );
    }

    /**
     * @param \Generated\Shared\Transfer\BulkProductReviewSearchRequestTransfer $bulkProductReviewSearchRequestTransfer
     *
     * @return \Spryker\Client\ProductReview\ProductViewExpander\ProductViewExpanderInterface
     */
    public function createProductViewBulkExpander(
        BulkProductReviewSearchRequestTransfer $bulkProductReviewSearchRequestTransfer
    ): ProductViewExpanderInterface {
        return new ProductViewExpander(
            $this->createProductReviewSummaryCalculator(),
            $this->createProductReviewSearchBulkReader($bulkProductReviewSearchRequestTransfer)
        );
    }

    /**
     * @param \Generated\Shared\Transfer\BulkProductReviewSearchRequestTransfer $bulkProductReviewSearchRequestTransfer
     *
     * @return \Spryker\Client\ProductReview\ProductViewExpander\ProductViewExpanderInterface
     */
    public function createBulkProductViewBatchExpander(
        BulkProductReviewSearchRequestTransfer $bulkProductReviewSearchRequestTransfer
    ): ProductViewExpanderInterface {
        return new ProductViewExpander(
            $this->createProductReviewSummaryCalculator(),
            $this->createProductReviewSearchBatchReader($bulkProductReviewSearchRequestTransfer)
        );
    }

    /**
     * @param \Generated\Shared\Transfer\BulkProductReviewSearchRequestTransfer $bulkProductReviewSearchRequestTransfer
     *
     * @return \Spryker\Client\ProductReview\Search\ProductReviewSearchReaderInterface
     */
    public function createProductReviewSearchBatchReader(
        BulkProductReviewSearchRequestTransfer $bulkProductReviewSearchRequestTransfer
    ): ProductReviewSearchReaderInterface {
        return new ProductReviewSearchReader(
            $this->createBulkProductReviewsQueryPlugin($bulkProductReviewSearchRequestTransfer),
            $this->getSearchClient(),
            $this->getProductReviewsBatchSearchResultFormatterPlugins()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\BulkProductReviewSearchRequestTransfer $bulkProductReviewSearchRequestTransfer
     *
     * @return \Spryker\Client\Search\Dependency\Plugin\QueryInterface
     */
    public function createBulkProductReviewsQueryPlugin(BulkProductReviewSearchRequestTransfer $bulkProductReviewSearchRequestTransfer)
    {
        $bulkProductReviewsQueryPlugin = new BulkProductReviewsQueryPlugin($bulkProductReviewSearchRequestTransfer);
        $queryExpanderPlugins = $this->getProductReviewsQueryExpanderPlugins();

        if (count($this->getProductReviewsBulkQueryExpanderPlugins())) {
            $queryExpanderPlugins = $this->getProductReviewsBulkQueryExpanderPlugins();
        }

        return $this->getSearchClient()->expandQuery(
            $bulkProductReviewsQueryPlugin,
            $queryExpanderPlugins,
            $bulkProductReviewSearchRequestTransfer->getFilter()->toArray()
        );
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\QueryExpanderPluginInterface[]
     */
    public function getProductReviewsBulkQueryExpanderPlugins(): array
    {
        return $this->getProvidedDependency(ProductReviewDependencyProvider::PLUGINS_PRODUCT_REVIEWS_BULK_QUERY_EXPANDER);
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\ResultFormatterPluginInterface[]
     */
    public function getProductReviewsBulkSearchResultFormatterPlugins(): array
    {
        return $this->getProvidedDependency(ProductReviewDependencyProvider::PLUGINS_PRODUCT_REVIEWS_BULK_SEARCH_RESULT_FORMATTER);
    }

    /**
     * @param \Generated\Shared\Transfer\BulkProductReviewSearchRequestTransfer $bulkProductReviewSearchRequestTransfer
     *
     * @return \Spryker\Client\ProductReview\Search\ProductReviewSearchReaderInterface
     */
    public function createProductReviewSearchBulkReader(
        BulkProductReviewSearchRequestTransfer $bulkProductReviewSearchRequestTransfer
    ): ProductReviewSearchReaderInterface {
        return new ProductReviewSearchReader(
            $this->createBulkProductReviewsQueryPlugin($bulkProductReviewSearchRequestTransfer),
            $this->getSearchClient(),
            $this->getProductReviewsBulkSearchResultFormatterPlugins()
        );
    }

    /**
     * @return \Spryker\Client\ProductReview\ResultFormatter\ResultFormatterInterface
     */
    public function createProductRatingAggreagationResultFormatter(): ResultFormatterInterface
    {
        return new ProductRatingAggreagationResultFormatter();
    }
}
