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

        return (bool)preg_match('/(^|\\.)apple\\.com(\\.cn)?$/i', strtolower($parts['host']));
    }
}
