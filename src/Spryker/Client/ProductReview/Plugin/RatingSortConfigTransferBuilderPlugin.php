<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductReview\Plugin;

use Generated\Shared\Search\PageIndexMap;
use Generated\Shared\Transfer\SortConfigTransfer;
use Spryker\Client\Catalog\Dependency\Plugin\SortConfigTransferBuilderPluginInterface;
use Spryker\Client\Kernel\AbstractPlugin;

class RatingSortConfigTransferBuilderPlugin extends AbstractPlugin implements SortConfigTransferBuilderPluginInterface
{
    /**
     * @var string
     */
    public const NAME = 'rating';

    /**
     * @var string
     */
    public const PARAMETER_NAME = 'rating';

    /**
     * @var string
     */
    protected const UNMAPPED_TYPE = 'integer';

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\SortConfigTransfer
     */
    public function build()
    {
        return (new SortConfigTransfer())
            ->setName(static::NAME)
            ->setParameterName(static::PARAMETER_NAME)
            ->setFieldName(PageIndexMap::INTEGER_SORT)
            ->setIsDescending(true)
            ->setUnmappedType(static::UNMAPPED_TYPE);
    }
}
