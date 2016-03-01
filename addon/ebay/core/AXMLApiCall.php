<?php

namespace bamboo\addon\ebay\core;

abstract class AXMLApiCall
{
    protected $compatibilityLevel;
    protected $callName;
    protected $ebaySiteId;
    protected $contentType;
    protected $contentLength;
	protected $xmlVersion = '1.0';
	protected $encoding = 'utf-8';
}