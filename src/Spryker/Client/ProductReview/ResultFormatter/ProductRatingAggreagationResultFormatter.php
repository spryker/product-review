<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductReview\ResultFormatter;

class ProductRatingAggreagationResultFormatter implements ResultFormatterInterface
{
    public const SUB_NAME = 'ratingAggregation';

    /**
     * @param array $records
     *
     * @return mixed
     */
    public function formatBatch(array $records)
    {
        $result = [];
        foreach ($records['buckets'] as $bucket) {
            $result[$bucket['key']] = [
                static::SUB_NAME => [],
            ];

            $ratingAggregation = $bucket['rating-aggregation'];

            foreach ($ratingAggregation['buckets'] as $subBucket) {
                $result[$bucket['key']][static::SUB_NAME][$subBucket['key']] = $subBucket['doc_count'];
            }
        }

        $result = $this->sortResults($result);

        return $result;
    }

    /**
     * @param array $result
     *
     * @return array
     */
    protected function sortResults(array $result)
    {
        krsort($result);

        return $result;
    }
}
