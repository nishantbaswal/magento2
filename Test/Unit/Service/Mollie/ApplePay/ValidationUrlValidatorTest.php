<?php
/*
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mollie\Payment\Test\Unit\Service\Mollie\ApplePay;

use Magento\Framework\Exception\LocalizedException;
use Mollie\Payment\Service\Mollie\ApplePay\ValidationUrlValidator;
use Mollie\Payment\Test\Unit\UnitTestCase;

class ValidationUrlValidatorTest extends UnitTestCase
{
    /**
     * @dataProvider validUrlProvider
     */
    public function testAllowsValidUrls(string $url)
    {
        $this->expectNotToPerformAssertions();

        $validator = new ValidationUrlValidator();

        $validator->validate($url);
    }

    public function validUrlProvider(): array
    {
        return [
            ['https://apple-pay-gateway.apple.com/paymentservices/startSession'],
            ['https://apple-pay-gateway-nc-pod1.apple.com/paymentservices/startSession'],
            ['https://apple-pay-gateway-cn.apple.com.cn/paymentservices/startSession'],
        ];
    }

    /**
     * @dataProvider invalidUrlProvider
     */
    public function testRejectsInvalidUrls(string $url)
    {
        $validator = new ValidationUrlValidator();

        $this->expectException(LocalizedException::class);
        $validator->validate($url);
    }

    public function invalidUrlProvider(): array
    {
        return [
            ['http://apple-pay-gateway.apple.com/paymentservices/startSession'],
            ['https://apple-pay-gateway.apple.com.evil.com/paymentservices/startSession'],
            ['https://apple-pay-gateway--pod1.apple.com/paymentservices/startSession'],
            ['https://apple-pay-gateway.apple.com/paymentservices/startSession/extra'],
            ['https://example.com/paymentservices/startSession'],
            ['https://apple.com/paymentservices/startSession'],
            ['https://foo.apple.com.cn/paymentservices/startSession'],
            ['not-a-url'],
            [''],
        ];
    }
}
