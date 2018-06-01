<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   MIT
 * @see       /LICENSE
 *
 *  International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\Skeleton\Install;

/**
 * Class Tab - module admin tab settings
 * @package Invertus\Skeleton\Install
 */
class Tab
{
    private $controllerInfo = 'AdminSkeletonInfo';
    /**
     * @var array
     */
    private $configuration;

    /**
     * Tab constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getControllerInfo()
    {
        return $this->controllerInfo;
    }

    public function getTabs()
    {
        return isset($this->configuration['tabs']) ? $this->configuration['tabs'] : [];
    }
}
