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
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $jsonSerializer;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\GraphQl\Config\Element\FieldFactory
     */
    private $fieldFactory;

    /**
     * @var bool|null
     */
    private $uncachebleRequest;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Serialize\SerializerInterface $jsonSerializer
     * @param \Magento\Framework\GraphQl\Config\Element\FieldFactory $fieldFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Serialize\SerializerInterface $jsonSerializer,
        \Magento\Framework\GraphQl\Config\Element\FieldFactory $fieldFactory
    ) {
        $this->fieldFactory = $fieldFactory;
        $this->request = $request;
        $this->jsonSerializer = $jsonSerializer;

        $this->uncachebleRequest = null;
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
        if (!$this->request->isPost()) {
            return null;
        }

        $this->checkRequestForMutation();
        $this->checkField($field);

        if (!$this->uncachebleRequest) {
            return null;
        }

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

        return [$subject, $resolvedValue, $field, $context, $info, $value, $args];
    }

    /**
     * Check for Mutation
     * @return void
     */
    private function checkRequestForMutation()
    {
        if ($this->uncachebleRequest === null) {
            $data = $this->jsonSerializer->unserialize($this->request->getContent());
            $query = $data['query'] ?? '';
            $this->uncachebleRequest = strpos(trim($query), 'mutation') === 0;
        }
    }

    /**
     * Check Field
     * @param Field $field
     * @return void
     */
    private function checkField(Field $field)
    {
        if (!$this->uncachebleRequest) {
            if (in_array($field->getName(), self::BLOCKED_LIST)) {
                $this->uncachebleRequest = true;
            }
        }
    }
}
