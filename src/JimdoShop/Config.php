<?php

namespace JimdoShop;

use Exception;

class Config
{
    public string $websiteId;
    public string $userAccountId;
    public string $clickAndChange;
    public string $jdi;
    public string $csToken;

    public function __construct(string $websiteId, string $userAccountId, string $clickAndChange, string $jdi, string $csToken)
    {
        $this->websiteId = $websiteId;
        $this->userAccountId = $userAccountId;
        $this->clickAndChange = $clickAndChange;
        $this->jdi = $jdi;
        $this->csToken = $csToken;

        if (empty($this->websiteId)) {
            throw new Exception('Website ID is empty');
        }
        if (empty($this->userAccountId)) {
            throw new Exception('User account ID is empty');
        }
        if (empty($this->clickAndChange)) {
            throw new Exception('Click and change is empty');
        }
        if (empty($this->jdi)) {
            throw new Exception('JDI is empty');
        }
        if (empty($this->csToken)) {
            throw new Exception('CS token is empty');
        }
    }

    public static function fromEnv(): Config
    {
        return new Config(
            getenv('JIMDO_WEBSITE_ID'),
            getenv('JIMDO_USER_ACCOUNT_ID'),
            getenv('JIMDO_CLICK_AND_CHANGE'),
            getenv('JIMDO_JDI'),
            getenv('JIMDO_CS_TOKEN'),
        );
    }
}