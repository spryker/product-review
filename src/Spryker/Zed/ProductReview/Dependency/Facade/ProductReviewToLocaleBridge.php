<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductReview\Dependency\Facade;

class ProductReviewToLocaleBridge implements ProductReviewToLocaleInterface
{
    /**
     * @var \Spryker\Zed\Locale\Business\LocaleFacadeInterface
     */
    protected $localeFacade;

    /**
     * @param \Spryker\Zed\Locale\Business\LocaleFacadeInterface $localeFacade
     */
    public function __construct($localeFacade)
    {
        $this->localeFacade = $localeFacade;
    }

    /**
     * @param string $localeName
     *
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    public function getLocale($localeName)
    {
        return $this->localeFacade->getLocale($localeName);
    }

    /**
     * @return array<\Generated\Shared\Transfer\LocaleTransfer>
     */
    public function getLocaleCollection()
    {
        return $this->localeFacade->getLocaleCollection();
    }
}
