<?php
/*
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mollie\Payment\Service\Mollie\ApplePay;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mollie\Payment\Config;
use Mollie\Payment\Service\Mollie\MollieApiClient;

class Validation
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var MollieApiClient
     */
    private $mollieApiClient;
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        StoreManagerInterface $storeManager,
        UrlInterface $url,
        MollieApiClient $mollieApiClient,
        Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->url = $url;
        $this->mollieApiClient = $mollieApiClient;
        $this->config = $config;
    }

    public function execute(string $validationUrl, ?string $domain = null): string
    {
        $this->validateApplePayUrl($validationUrl);

        $store = $this->storeManager->getStore();
        $api = $this->mollieApiClient->loadByApiKey($this->getLiveApiKey((int)$store->getId()));

        if ($domain === null) {
            $domain = parse_url($this->url->getBaseUrl(), PHP_URL_HOST);
        }

        return $api->wallets->requestApplePayPaymentSession(
            $domain,
            $validationUrl
        );
    }

    /**
     * Validates that the Apple Pay validation URL is from an Apple domain (SSRF prevention).
     *
     * @param string $url
     * @throws \InvalidArgumentException
     */
    public function validateApplePayUrl(string $url): void
    {
        $parsed = parse_url($url);

        if (!$parsed || empty($parsed['scheme']) || empty($parsed['host'])) {
            throw new \InvalidArgumentException((string)__('Invalid Apple Pay validation URL'));
        }

        if ($parsed['scheme'] !== 'https') {
            throw new \InvalidArgumentException((string)__('Apple Pay validation URL must use HTTPS'));
        }

        // Normalize the host to ASCII (punycode) to prevent IDN homograph attacks,
        // e.g. a Cyrillic 'а' that visually looks like a Latin 'a'.
        $host = $parsed['host'];
        if (function_exists('idn_to_ascii')) {
            $normalized = idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
            if ($normalized !== false) {
                $host = $normalized;
            }
        }

        if (!preg_match('/(?:^|\.)apple\.com$/', $host)) {
            throw new \InvalidArgumentException((string)__('Apple Pay validation URL must be from apple.com'));
        }
    }

    private function getLiveApiKey(int $storeId): string
    {
        $liveApikey = $this->config->getLiveApiKey($storeId);
        if (!$liveApikey) {
            throw new \Exception(__('For Apple Pay the live API key is required, even when in test mode'));
        }

        return $liveApikey;
    }
}
