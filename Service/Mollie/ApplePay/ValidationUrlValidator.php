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
    private const MAX_SUFFIX_LENGTH = 32;

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
        $prefix = 'apple-pay-gateway';
        $appleComCn = '.apple.com.cn';
        $appleCom = '.apple.com';

        if (substr($host, -strlen($appleComCn)) === $appleComCn) {
            $baseHost = substr($host, 0, -strlen($appleComCn));
        } elseif (substr($host, -strlen($appleCom)) === $appleCom) {
            $baseHost = substr($host, 0, -strlen($appleCom));
        } else {
            return false;
        }

        if (strpos($baseHost, $prefix) !== 0) {
            return false;
        }

        $suffix = substr($baseHost, strlen($prefix));
        if ($suffix === '') {
            return true;
        }

        if ($suffix[0] !== '-') {
            return false;
        }

        // Defensive cap: Apple Pay gateway suffixes are short (e.g. "-nc-pod1"); adjust if Apple extends naming.
        if (strlen($suffix) > self::MAX_SUFFIX_LENGTH) {
            return false;
        }

        $segments = explode('-', substr($suffix, 1));
        foreach ($segments as $segment) {
            // Empty segments indicate consecutive or trailing hyphens, which are not allowed.
            if ($segment === '' || !ctype_alnum($segment)) {
                return false;
            }
        }

        return true;
    }
}
