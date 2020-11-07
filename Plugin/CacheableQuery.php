<?php

/**
 * @author Mygento Team
 * @copyright 2020 Mygento (https://www.mygento.ru)
 * @package Mygento_GraphQlCache
 */

namespace Mygento\GraphQlCache\Plugin;

class CacheableQuery
{
    /**
     * @param \Magento\GraphQlCache\Model\CacheableQuery $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetCacheTags($subject, $result): array
    {
        return array_unique($result);
    }
}
