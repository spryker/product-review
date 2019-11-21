<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductReview\Calculator;

use Generated\Shared\Transfer\ProductReviewSummaryTransfer;
use Spryker\Client\ProductReview\ProductReviewConfig;

class ProductReviewSummaryCalculator implements ProductReviewSummaryCalculatorInterface
{
    public const MINIMUM_RATING = 1;
    public const RATING_PRECISION = 1;

    /**
     * @var \Spryker\Client\ProductReview\ProductReviewConfig
     */
    protected $productReviewConfig;

    /**
     * @param \Spryker\Client\ProductReview\ProductReviewConfig $productReviewConfig
     */
    public function __construct(ProductReviewConfig $productReviewConfig)
    {
        $this->productReviewConfig = $productReviewConfig;
    }

    /**
     * @param array $ratingAggregation
     *
     * @return \Generated\Shared\Transfer\ProductReviewSummaryTransfer
     */
    public function execute(array $ratingAggregation): ProductReviewSummaryTransfer
    {
        $totalReview = $this->getTotalReview($ratingAggregation);

        $summary = (new ProductReviewSummaryTransfer())
            ->setRatingAggregation($this->formatRatingAggregation($ratingAggregation))
            ->setMaximumRating($this->productReviewConfig->getMaximumRating())
            ->setAverageRating($this->getAverageRating($ratingAggregation, $totalReview))
            ->setTotalReview($totalReview);

        return $summary;
    }

    /**
     * @param array $ratingAggregation
     * @param int $totalReview
     *
     * @return float
     */
    protected function getAverageRating(array $ratingAggregation, $totalReview): float
    {
        if ($totalReview === 0) {
            return 0.0;
        }

        $totalRating = $this->getTotalRating($ratingAggregation);

        return round($totalRating / $totalReview, static::RATING_PRECISION);
    }

    /**
     * @param array $ratingAggregation
     *
     * @return array
     */
    protected function formatRatingAggregation(array $ratingAggregation): array
    {
        $ratingAggregation = $this->fillRatings($ratingAggregation);
        $ratingAggregation = $this->sortRatings($ratingAggregation);

        return $ratingAggregation;
    }

    /**
     * @param array $ratingAggregation
     *
     * @return array
     */
    protected function fillRatings(array $ratingAggregation): array
    {
        $maximumRating = $this->productReviewConfig->getMaximumRating();

        for ($rating = static::MINIMUM_RATING; $rating <= $maximumRating; $rating++) {
            $ratingAggregation[$rating] = array_key_exists($rating, $ratingAggregation) ? $ratingAggregation[$rating] : 0;
        }

        return $ratingAggregation;
    }

    /**
     * @param array $ratingAggregation
     *
     * @return array
     */
    protected function sortRatings(array $ratingAggregation): array
    {
        krsort($ratingAggregation);

        return $ratingAggregation;
    }

    /**
     * @param array $ratingAggregation
     *
     * @return int
     */
    protected function getTotalReview(array $ratingAggregation): int
    {
        $totalReview = 0;

        foreach ($ratingAggregation as $reviewCount) {
            $totalReview += $reviewCount;
        }

        return $totalReview;
    }

    /**
     * @param array $ratingAggregation
     *
     * @return int
     */
    protected function getTotalRating(array $ratingAggregation): int
    {
        $totalRating = 0;

        foreach ($ratingAggregation as $rating => $reviewCount) {
            $totalRating += $reviewCount * $rating;
        }

        return $totalRating;
    }
}
