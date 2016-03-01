<?php

namespace bamboo\addon\ebay\core;

abstract class AXMLApiCall
{
    protected $compatibilityLevel;
    protected $callName;
    protected $ebaySiteId;
    protected $contentType;
    protected $contentLength;
}