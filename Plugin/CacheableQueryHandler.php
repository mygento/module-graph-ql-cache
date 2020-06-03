<?php

/**
 * @author Mygento Team
 * @copyright 2020 Mygento (https://www.mygento.ru)
 * @package Mygento_GraphQlCache
 */

namespace Mygento\GraphQlCache\Plugin;

use Magento\Framework\App\Request\Http;

class CacheableQueryHandler
{
    /**
     * @var \Magento\GraphQlCache\Model\Resolver\IdentityPool
     */
    private $identityPool;

    /**
     * @var \Magento\GraphQlCache\Model\CacheableQuery
     */
    private $cacheableQuery;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @param \Magento\GraphQlCache\Model\CacheableQuery $cacheableQuery
     * @param \Magento\GraphQlCache\Model\Resolver\IdentityPool $identityPool
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\GraphQlCache\Model\CacheableQuery $cacheableQuery,
        \Magento\GraphQlCache\Model\Resolver\IdentityPool $identityPool,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
        $this->cacheableQuery = $cacheableQuery;
        $this->identityPool = $identityPool;
    }

    /**
     * @param \Magento\GraphQlCache\Model\CacheableQueryHandler $subject
     * @param mixed $result
     * @param array $resolvedValue
     * @param array $cacheAnnotation
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterHandleCacheFromResolverResponse($subject, $result, $resolvedValue, $cacheAnnotation)
    {
        if ($this->cacheableQuery->isCacheable() || !$this->request->isPost()) {
            return $result;
        }
        $cacheable = $cacheAnnotation['cacheable'] ?? true;
        $cacheIdentityClass = $cacheAnnotation['cacheIdentity'] ?? '';

        if ($this->request instanceof Http && !$this->isRequestMutation() && !empty($cacheIdentityClass)) {
            $cacheIdentity = $this->identityPool->get($cacheIdentityClass);
            $cacheTags = $cacheIdentity->getIdentities($resolvedValue);
            $this->cacheableQuery->addCacheTags($cacheTags);
        } else {
            $cacheable = false;
        }

        $this->cacheableQuery->setCacheValidity($cacheable);
    }

    /**
     * Check for Mutation
     * @return bool
     */
    private function isRequestMutation(): bool
    {
        return false;
    }
}
