<?php

namespace Invertus\Skeleton\Controller;

/**
 * Class AdminController - an abstraction for all admin controllers
 * @package Invertus\Skeleton\Controller
 */
class AdminController extends \ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
    }
}
