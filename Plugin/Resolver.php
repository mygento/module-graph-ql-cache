<?php

/**
 * @author Mygento Team
 * @copyright 2020 Mygento (https://www.mygento.ru)
 * @package Mygento_GraphQlCache
 */

namespace Mygento\GraphQlCache\Plugin;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Resolver
{
    /**
     * @var bool
     */
    private $blockCaching;

    public function __construct()
    {
        $this->blockCaching = false;
    }

    /**
     * @param \Magento\GraphQlCache\Model\Plugin\Query\Resolver $resolver
     * @param ResolverInterface $subject
     * @param mixed $resolvedValue
     * @param Field $field
     * @param \Magento\GraphQl\Model\Query\Resolver\Context $context
     * @param ResolveInfo $info
     * @param array $value
     * @param array $args
     */
    public function beforeAfterResolve(
        \Magento\GraphQlCache\Model\Plugin\Query\Resolver $resolver,
        ResolverInterface $subject,
        $resolvedValue,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        return [$subject, $resolvedValue, $field, $context, $info, $value, $args];
    }
}
