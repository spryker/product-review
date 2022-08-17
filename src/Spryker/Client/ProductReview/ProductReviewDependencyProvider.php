<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductReview;

use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;
use Spryker\Client\ProductReview\Dependency\Client\ProductReviewToSearchBridge;
use Spryker\Client\ProductReview\Dependency\Client\ProductReviewToStorageBridge;
use Spryker\Client\ProductReview\Dependency\Client\ProductReviewToZedRequestBridge;
use Spryker\Client\ProductReview\Plugin\Elasticsearch\QueryExpander\PaginatedProductReviewsQueryExpanderPlugin;
use Spryker\Client\ProductReview\Plugin\Elasticsearch\QueryExpander\RatingAggregationQueryExpanderPlugin;
use Spryker\Client\ProductReview\Plugin\Elasticsearch\QueryExpander\SortByCreatedAtQueryExpanderPlugin;
use Spryker\Client\ProductReview\Plugin\Elasticsearch\ResultFormatter\PaginatedProductReviewsResultFormatterPlugin;
use Spryker\Client\ProductReview\Plugin\Elasticsearch\ResultFormatter\ProductReviewsResultFormatterPlugin;
use Spryker\Client\ProductReview\Plugin\Elasticsearch\ResultFormatter\RatingAggregationResultFormatterPlugin;
use Spryker\Client\Search\Plugin\Config\PaginationConfigBuilder;

/**
 * @method \Spryker\Client\ProductReview\ProductReviewConfig getConfig()
 */
class ProductReviewDependencyProvider extends AbstractDependencyProvider
{
    /**
     * @var string
     */
    public const CLIENT_ZED_REQUEST = 'CLIENT_ZED_REQUEST';

    /**
     * @var string
     */
    public const CLIENT_STORAGE = 'CLIENT_STORAGE';

    /**
     * @var string
     */
    public const CLIENT_SEARCH = 'CLIENT_SEARCH';

    /**
     * @var string
     */
    public const PRODUCT_REVIEWS_QUERY_EXPANDER_PLUGINS = 'PRODUCT_REVIEWS_QUERY_EXPANDER_PLUGINS';

    /**
     * @var string
     */
    public const PLUGINS_PRODUCT_REVIEWS_BULK_QUERY_EXPANDER = 'PLUGINS_PRODUCT_REVIEWS_BULK_QUERY_EXPANDER';

    /**
     * @var string
     */
    public const PRODUCT_REVIEWS_SEARCH_RESULT_FORMATTER_PLUGINS = 'PRODUCT_REVIEWS_SEARCH_RESULT_FORMATTER_PLUGINS';

    /**
     * @var string
     */
    public const PLUGINS_PRODUCT_REVIEWS_BULK_SEARCH_RESULT_FORMATTER = 'PLUGINS_PRODUCT_REVIEWS_BULK_SEARCH_RESULT_FORMATTER';

    /**
     * @var string
     */
    public const PAGINATION_CONFIG_BUILDER_PLUGIN = 'PAGINATION_CONFIG_BUILDER_PLUGIN';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container)
    {
        $container = $this->addZedRequestClient($container);
        $container = $this->addStorageClient($container);
        $container = $this->addSearchClient($container);
        $container = $this->addProductReviewsQueryExpanderPlugins($container);
        $container = $this->addProductReviewsSearchResultFormatterPlugins($container);
        $container = $this->addPaginationConfigBuilderPlugin($container);
        $container = $this->addProductReviewsBulkQueryExpanderPlugins($container);
        $container = $this->addProductReviewsBulkSearchResultFormatterPlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addPaginationConfigBuilderPlugin(Container $container)
    {
        $container->set(static::PAGINATION_CONFIG_BUILDER_PLUGIN, function (Container $container) {
            return new PaginationConfigBuilder();
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addZedRequestClient(Container $container)
    {
        $container->set(static::CLIENT_ZED_REQUEST, function (Container $container) {
            return new ProductReviewToZedRequestBridge($container->getLocator()->zedRequest()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addStorageClient(Container $container)
    {
        $container->set(static::CLIENT_STORAGE, function (Container $container) {
            return new ProductReviewToStorageBridge($container->getLocator()->storage()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addSearchClient(Container $container)
    {
        $container->set(static::CLIENT_SEARCH, function (Container $container) {
            return new ProductReviewToSearchBridge($container->getLocator()->search()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addProductReviewsQueryExpanderPlugins(Container $container)
    {
        $container->set(static::PRODUCT_REVIEWS_QUERY_EXPANDER_PLUGINS, function () {
            return $this->getProductReviewsQueryExpanderPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Client\Search\Dependency\Plugin\QueryExpanderPluginInterface>
     */
    protected function getProductReviewsQueryExpanderPlugins()
    {
        return [
            new PaginatedProductReviewsQueryExpanderPlugin(),
            new RatingAggregationQueryExpanderPlugin(),
            new SortByCreatedAtQueryExpanderPlugin(),
        ];
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addProductReviewsBulkQueryExpanderPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_PRODUCT_REVIEWS_BULK_QUERY_EXPANDER, function () {
            return $this->getProductReviewsBulkQueryExpanderPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Client\SearchExtension\Dependency\Plugin\QueryExpanderPluginInterface>
     */
    protected function getProductReviewsBulkQueryExpanderPlugins(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addProductReviewsSearchResultFormatterPlugins(Container $container)
    {
        $container->set(static::PRODUCT_REVIEWS_SEARCH_RESULT_FORMATTER_PLUGINS, function () {
            return $this->getProductReviewsSearchResultFormatterPlugins();
        });

        return $container;
    }

    /**
     * @phpstan-return array<\Spryker\Client\SearchExtension\Dependency\Plugin\ResultFormatterPluginInterface>
     *
     * @return array<\Spryker\Client\Search\Dependency\Plugin\ResultFormatterPluginInterface>
     */
    public function getProductReviewsSearchResultFormatterPlugins()
    {
        return [
            new ProductReviewsResultFormatterPlugin(),
            new PaginatedProductReviewsResultFormatterPlugin(),
            new RatingAggregationResultFormatterPlugin(),
        ];
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addProductReviewsBulkSearchResultFormatterPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_PRODUCT_REVIEWS_BULK_SEARCH_RESULT_FORMATTER, function () {
            return $this->getProductReviewsBulkSearchResultFormatterPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Client\SearchExtension\Dependency\Plugin\ResultFormatterPluginInterface>
     */
    public function getProductReviewsBulkSearchResultFormatterPlugins(): array
    {
        return [];
    }
}
