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

use ReflectionClass;

/**
 * Class Tab - module admin tab settings
 * @package Invertus\Skeleton\Install
 */
class Tab
{
    /**
     * @var \Skeleton
     */
    private $module;

    private $controllerInvisible = 'AdminSkeletonTab';
    private $controllerInfo = 'AdminSkeletonInfo';

    /**
     * Tab constructor.
     *
     * @param \Skeleton $module
     */
    public function __construct(\Skeleton $module)
    {
        $this->module = $module;
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
        $reflection = new ReflectionClass($this);
        $shortClassName = $reflection->getShortName();

        return [
            [
                'name' => $this->module->displayName,
                'ParentClassName' => 'AdminParentModulesSf',
                'class_name' => $this->controllerInvisible,
                'visible' => false
            ],
            [
                'name' => $this->module->l('Info', $shortClassName),
                'ParentClassName' => 'AdminParentModulesSf',
                'class_name' => $this->controllerInfo,
            ]
        ];
    }
}
