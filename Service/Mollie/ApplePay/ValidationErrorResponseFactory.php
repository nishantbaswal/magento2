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
    public function create(ResultFactory $resultFactory, string $message, int $httpCode = 400): Json
    {
        $response = $resultFactory->create(ResultFactory::TYPE_JSON);
        $response->setHttpResponseCode($httpCode);
        $response->setData([
            'error' => true,
            'message' => $message,
        ]);

        return $response;
    }
}
