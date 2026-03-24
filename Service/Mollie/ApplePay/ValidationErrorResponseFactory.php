<?php
/*
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Mollie\Payment\Service\Mollie\ApplePay;

use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;

class ValidationErrorResponseFactory
{
    /**
     * @var ResultFactory
     */
    private $resultFactory;

    public function __construct(
        ResultFactory $resultFactory
    ) {
        $this->resultFactory = $resultFactory;
    }

    public function create(string $message, int $httpCode = 400): Json
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $response->setHttpResponseCode($httpCode);
        $response->setData([
            'error' => true,
            'message' => $message,
        ]);

        return $response;
    }
}
