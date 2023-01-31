<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductReview\Business\MessageBroker;

use Generated\Shared\Transfer\AddReviewsTransfer;
use Generated\Shared\Transfer\ProductReviewTransfer;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\Product\Business\Exception\MissingProductException;
use Spryker\Zed\Product\Business\ProductFacadeInterface;
use Spryker\Zed\ProductReview\Business\Model\ProductReviewCreatorInterface;
use Spryker\Zed\ProductReview\Business\Model\ProductReviewReaderInterface;
use Spryker\Zed\ProductReview\Dependency\Facade\ProductReviewToLocaleInterface;
use Spryker\Zed\ProductReview\Dependency\Facade\ProductReviewToProductInterface;

class ProductReviewMessageHandler implements ProductReviewMessageHandlerInterface
{
    use LoggerTrait;

    /**
     * @var \Spryker\Zed\ProductReview\Business\Model\ProductReviewCreatorInterface
     */
    protected ProductReviewCreatorInterface $productReviewCreator;

    /**
     * @var \Spryker\Zed\ProductReview\Dependency\Facade\ProductReviewToLocaleInterface
     */
    protected ProductReviewToLocaleInterface $localeFacade;

    /**
     * @var \Spryker\Zed\ProductReview\Dependency\Facade\ProductReviewToProductInterface
     */
    protected ProductReviewToProductInterface $productFacade;

    /**
     * @var \Spryker\Zed\ProductReview\Business\Model\ProductReviewReaderInterface
     */
    protected ProductReviewReaderInterface $productReviewReader;

    /**
     * @param \Spryker\Zed\ProductReview\Business\Model\ProductReviewCreatorInterface $productReviewCreator
     * @param \Spryker\Zed\ProductReview\Dependency\Facade\ProductReviewToLocaleInterface $localeFacade
     * @param \Spryker\Zed\ProductReview\Dependency\Facade\ProductReviewToProductInterface $productFacade
     * @param \Spryker\Zed\ProductReview\Business\Model\ProductReviewReaderInterface $productReviewReader
     */
    public function __construct(
        ProductReviewCreatorInterface $productReviewCreator,
        ProductReviewToLocaleInterface $localeFacade,
        ProductReviewToProductInterface $productFacade,
        ProductReviewReaderInterface $productReviewReader
    ) {
        $this->productReviewCreator = $productReviewCreator;
        $this->localeFacade = $localeFacade;
        $this->productFacade = $productFacade;
        $this->productReviewReader = $productReviewReader;
    }

    /**
     * @param \Generated\Shared\Transfer\AddReviewsTransfer $addReviewsTransfer
     *
     * @return void
     */
    public function handleAddReviews(AddReviewsTransfer $addReviewsTransfer): void
    {
        $localeNameToForeignKeyMap = $this->mapLocaleNamesToIdLocales($addReviewsTransfer);
        $productIdentifierToIdProductAbstractMap = $this->mapProductIdentifierToIdProductAbstract($addReviewsTransfer);

        foreach ($addReviewsTransfer->getReviews() as $reviewTransfer) {
            $idLocale = $localeNameToForeignKeyMap[$reviewTransfer->getLocale()];
            $idProductAbstract = $productIdentifierToIdProductAbstractMap[$reviewTransfer->getProductIdentifier()];

            // We simply ignore Reviews where no matching locale was found.
            if (!$idLocale) {
                continue;
            }
            // We simply ignore Reviews where no matching product was found.
            if (!$idProductAbstract) {
                continue;
            }

            $productReviewTransfer = new ProductReviewTransfer();
            $productReviewTransfer->fromArray($reviewTransfer->toArray(), true);
            $productReviewTransfer->setFkLocale($idLocale);
            $productReviewTransfer->setFkProductAbstract($idProductAbstract);
            $productReviewTransfer->setSummary($reviewTransfer->getReviewTitle());
            $productReviewTransfer->setDescription($reviewTransfer->getReviewText());
            $productReviewTransfer->setCustomerReference($reviewTransfer->getCustomerIdentifier());
            $productReviewTransfer->setCreatedAt($reviewTransfer->getCreatedAt());

            // We simply ignore Reviews that already exist for same Customer and Date.
            if ($this->productReviewReader->isProductReviewAlreadySubmittedByCustomer($productReviewTransfer)) {
                $this->getLogger()->info(sprintf('Product Review for Customer with reference `%s` and created at `%s` already exists.', $productReviewTransfer->getCustomerReference(), $productReviewTransfer->getCreatedAt()));

                continue;
            }

            $this->productReviewCreator->createProductReview($productReviewTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\AddReviewsTransfer $addReviewsTransfer
     *
     * @return array<string, int>
     */
    protected function mapLocaleNamesToIdLocales(AddReviewsTransfer $addReviewsTransfer): array
    {
        $localeNames = [];

        foreach ($addReviewsTransfer->getReviews() as $reviewTransfer) {
            // For performance reasons we only need unique names to be searched in the LocalTransfer collection.
            $localeNames[$reviewTransfer->getLocale()] = $reviewTransfer->getLocale();
        }

        $localeNameToForeignKeyMap = [];

        $localeTransferCollection = $this->localeFacade->getLocaleCollection();

        foreach ($localeNames as $localeName) {
            $localeNameToForeignKeyMap[$localeName] = $this->findLocaleIdInCollectionByLocaleName($localeName, $localeTransferCollection);
        }

        return $localeNameToForeignKeyMap;
    }

    /**
     * @param string $localeName
     * @param array<\Generated\Shared\Transfer\LocaleTransfer> $localeTransferCollection
     *
     * @return int|null
     */
    protected function findLocaleIdInCollectionByLocaleName(string $localeName, array $localeTransferCollection): ?int
    {
        foreach ($localeTransferCollection as $localeTransfer) {
            if ($localeTransfer->getLocaleName() === $localeName) {
                return $localeTransfer->getIdLocale();
            }
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\AddReviewsTransfer $addReviewsTransfer
     *
     * @return array<string, int>
     */
    protected function mapProductIdentifierToIdProductAbstract(AddReviewsTransfer $addReviewsTransfer): array
    {
        $productIdentifiers = [];

        foreach ($addReviewsTransfer->getReviews() as $reviewTransfer) {
            // For performance reasons we only need unique names to be searched in the ProductAbstractTransferCollection collection.
            $productIdentifiers[$reviewTransfer->getProductIdentifier()] = $reviewTransfer->getProductIdentifier();
        }

        // Reviews are made for concretes, find the concrete products by the given identifier.
        $productIdentifierToIdProductAbstractMap = $this->getIdProductAbstractMap($productIdentifiers);

        return $productIdentifierToIdProductAbstractMap;
    }

    /**
     * @param string $productIdentifier
     * @param array<\Generated\Shared\Transfer\ProductConcreteTransfer> $productConcreteTransferCollection
     *
     * @return int|null
     */
    protected function findIdProductConcreteByProductIdentifier(string $productIdentifier, array $productConcreteTransferCollection): ?int
    {
        foreach ($productConcreteTransferCollection as $productConcreteTransfer) {
            if ($productConcreteTransfer->getSku() === $productIdentifier) {
                // We need to save the id_product_abstract in the database return this instead of the id_product_concrete.
                return $productConcreteTransfer->getFkProductAbstract();
            }
        }

        $this->getLogger()->info(sprintf('Product with SKU %s not found, could be not a product review.', $productIdentifier));

        return null;
    }

    /**
     * @param array $productIdentifiers
     *
     * @return array
     */
    protected function getIdProductAbstractMap(array $productIdentifiers): array
    {
        $productIdentifierToIdProductAbstractMap = [];

        if (method_exists(ProductFacadeInterface::class, 'getRawProductConcreteTransfersByConcreteSkus')) {
            $productConcreteTransferCollection = $this->productFacade->getRawProductConcreteTransfersByConcreteSkus(array_values($productIdentifiers));
            foreach ($productIdentifiers as $productIdentifier) {
                $productIdentifierToIdProductAbstractMap[$productIdentifier] = $this->findIdProductConcreteByProductIdentifier($productIdentifier, $productConcreteTransferCollection);
            }

            return $productIdentifierToIdProductAbstractMap;
        }
        // fallback for Product v5
        foreach ($productIdentifiers as $productIdentifier) {
            try {
                $productIdentifierToIdProductAbstractMap[$productIdentifier] = $this->productFacade->getProductAbstractIdByConcreteSku($productIdentifier);
            } catch (MissingProductException) {
                $this->getLogger()->info(sprintf('Product with SKU %s not found, could be not a product review.', $productIdentifier));
                $productIdentifierToIdProductAbstractMap[$productIdentifier] = null;
            }
        }

        return $productIdentifierToIdProductAbstractMap;
    }
}
