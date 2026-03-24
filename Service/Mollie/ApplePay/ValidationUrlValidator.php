<?php
/*
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Mollie\Payment\Service\Mollie\ApplePay;

use Magento\Framework\Exception\LocalizedException;

class ValidationUrlValidator
{
    public function validate(string $validationUrl): void
    {
        if (!$this->isValid($validationUrl)) {
            throw new LocalizedException(__('Invalid Apple Pay validation URL.'));
        }
    }

    private function isValid(string $validationUrl): bool
    {
        if (trim($validationUrl) === '') {
            return false;
        }

        $parts = parse_url($validationUrl);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            return false;
        }

        if (strtolower($parts['scheme']) !== 'https') {
            return false;
        }

        if (($parts['path'] ?? '') !== '/paymentservices/startSession') {
            return false;
        }

        $host = strtolower($parts['host']);
        if (!preg_match('/^apple-pay-gateway(?P<suffix>-[a-z0-9]+(?:-[a-z0-9]+)*)?\\.apple\\.com(?:\\.cn)?$/i', $host, $matches)) {
            return false;
        }

        return empty($matches['suffix']) || strlen($matches['suffix']) <= 33;
    }
}
