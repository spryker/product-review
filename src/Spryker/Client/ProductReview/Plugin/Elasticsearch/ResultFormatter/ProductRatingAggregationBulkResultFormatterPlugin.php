<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductReview\Plugin\Elasticsearch\ResultFormatter;

use Spryker\Client\ProductReview\Plugin\Elasticsearch\QueryExpander\ProductRatingAggregationBulkQueryExpanderPlugin;
use Spryker\Client\SearchElasticsearch\Plugin\ResultFormatter\AbstractElasticsearchResultFormatterPlugin;

/**
 * @method \Spryker\Client\ProductReview\ProductReviewFactory getFactory()
 */
class ProductRatingAggregationBulkResultFormatterPlugin extends AbstractElasticsearchResultFormatterPlugin
{
    protected const NAME = 'productAggregation';

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * @param mixed $searchResult
     * @param array $requestParameters
     *
     * @return array
     */
    protected function formatSearchResult($searchResult, array $requestParameters)
    {
        return $this->getFactory()
            ->createProductRatingAggreagationResultFormatter()
            ->formatBulk($searchResult->getAggregation(ProductRatingAggregationBulkQueryExpanderPlugin::PRODUCT_AGGREGATOIN_NAME));
    }
}