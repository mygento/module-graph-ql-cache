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
    const BLOCKED_LIST = ['cart'];

    /**
     * @var \Magento\Framework\GraphQl\Config\Element\FieldFactory
     */
    private $fieldFactory;

    /**
     * @var bool
     */
    private $blockCaching;

    /**
     * @param \Magento\Framework\GraphQl\Config\Element\FieldFactory $fieldFactory
     */
    public function __construct(
        \Magento\Framework\GraphQl\Config\Element\FieldFactory $fieldFactory
    ) {
        $this->blockCaching = false;
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * Plugin
     * @param \Magento\GraphQlCache\Model\Plugin\Query\Resolver $resolver
     * @param ResolverInterface $subject
     * @param mixed $resolvedValue
     * @param Field $field
     * @param \Magento\GraphQl\Model\Query\Resolver\Context $context
     * @param ResolveInfo $info
     * @param array $value
     * @param array $args
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
        if (in_array($field->getName(), self::BLOCKED_LIST)) {
            $this->blockCaching = true;
        }

        if ($this->blockCaching) {
            $config = [
                'name' => $field->getName(),
                'type' => $field->getTypeName(),
                'required' => $field->isRequired(),
                'resolver' => $field->getResolver() ?: '',
                'description' => $field->getDescription() ?: '',
            ];
            if ($field->isList()) {
                $config['itemType'] = $field->getTypeName();
            }

            $field = $this->fieldFactory->createFromConfigData($config, $field->getArguments());
        }

        return [$subject, $resolvedValue, $field, $context, $info, $value, $args];
    }
}
