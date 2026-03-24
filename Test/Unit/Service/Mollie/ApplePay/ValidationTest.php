<?php

namespace Mollie\Payment\Test\Unit\Service\Mollie\ApplePay;

use Mollie\Payment\Service\Mollie\ApplePay\Validation;
use Mollie\Payment\Test\Unit\UnitTestCase;

class ValidationTest extends UnitTestCase
{
    /**
     * @var Validation
     */
    private $instance;

    protected function setUpWithoutVoid()
    {
        $this->instance = $this->objectManager->getObject(Validation::class);
    }

    /**
     * @dataProvider validApplePayUrls
     */
    public function testValidApplePayUrlsPassValidation(string $url): void
    {
        // Should not throw
        $this->instance->validateApplePayUrl($url);
        $this->addToAssertionCount(1);
    }

    /**
     * @dataProvider invalidApplePayUrls
     */
    public function testInvalidApplePayUrlsFailValidation(string $url): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->instance->validateApplePayUrl($url);
    }

    public function validApplePayUrls(): array
    {
        return [
            ['https://apple-pay-gateway.apple.com/paymentservices/startSession'],
            ['https://cn-apple-pay-gateway.apple.com/paymentservices/startSession'],
            ['https://apple-pay-gateway-cert.apple.com/paymentservices/startSession'],
        ];
    }

    public function invalidApplePayUrls(): array
    {
        return [
            'http scheme'               => ['http://apple-pay-gateway.apple.com/paymentservices/startSession'],
            'non-apple domain'          => ['https://evil.example.com/collect'],
            'internal host'             => ['https://localhost/internal-endpoint'],
            'internal IP'               => ['https://192.168.1.1/secret'],
            'apple.com in path only'    => ['https://evil.com/redirect?url=apple.com'],
            'apple.com in query string' => ['https://evil.com/?host=apple.com'],
            'subdomain spoof'           => ['https://apple.com.evil.com/session'],
            'empty string'              => [''],
            'no scheme'                 => ['apple-pay-gateway.apple.com/paymentservices/startSession'],
        ];
    }
}
