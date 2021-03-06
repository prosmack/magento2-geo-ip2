<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model\Database\Data;

use Tobai\GeoIp2\Model\CountryInterface;

class Country implements CountryInterface
{
    /**
     * @var \Tobai\GeoIp2\Model\Database\ReaderFactory
     */
    protected $readerFactory;

    /**
     * @var \GeoIp2\Database\Reader
     */
    protected $reader;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    protected $httpRequest;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Tobai\GeoIp2\Model\Database\ReaderFactory $readerFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $httpRequest
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Tobai\GeoIp2\Model\Database\ReaderFactory $readerFactory,
        \Magento\Framework\HTTP\PhpEnvironment\Request $httpRequest,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->readerFactory = $readerFactory;
        $this->httpRequest = $httpRequest;
        $this->logger = $logger;
    }

    /**
     * @return \GeoIp2\Model\Country|bool
     */
    public function getCountry()
    {
        try {
            $clientIp = $this->httpRequest->getClientIp();
            $country = $this->getReader()->country($clientIp);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $country = false;
        }
        return $country;
    }

    /**
     * @return bool|string
     */
    public function getCountryCode()
    {
        $country = $this->getCountry();
        return $country ? $country->country->isoCode : false;
    }

    /**
     * @return \GeoIp2\Database\Reader
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getReader()
    {
        if (null === $this->reader) {
            $this->reader = $this->readerFactory->create('country');
        }
        return $this->reader;
    }
}
