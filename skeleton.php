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

use Invertus\Skeleton\Install\Installer;
use Invertus\Skeleton\Install\Uninstaller;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use \Invertus\Skeleton\Install\Tab;

class Skeleton extends Module
{
    /**
     * If false, then SkeletonContainer is in immutable state
     */
    const DISABLE_CACHE = true;

    /**
     * @var SkeletonContainer
     */
    private $moduleContainer;

    public function __construct()
    {
        $this->tab = 'other_modules';
        $this->name = 'skeleton';
        $this->version = '1.0.0';
        $this->author = 'Invertus';

        parent::__construct();
        $this->autoLoad();
        $this->compile();
        $this->displayName = $this->l('Skeleton');
        $this->description = $this->l('This is module description');
    }

    public function getTabs()
    {
        /** @var Tab $tab */
        $tab = $this->getContainer()->get('install.tab');
        return $tab->getTabs();
    }

    public function getContent()
    {
        /** @var Tab $tab */
        $tab = $this->getContainer()->get('install.tab');

        $redirectLink = $this->context->link->getAdminLink($tab->getControllerInfo());
        Tools::redirectAdmin($redirectLink);
    }

    public function install()
    {
        /** @var Installer $installer */
        $installer = $this->getContainer()->get('installer');

        return parent::install() && $installer->init();
    }

    public function uninstall()
    {
        /** @var Uninstaller $unInstaller */
        $unInstaller = $this->getContainer()->get('uninstaller');

        return parent::uninstall() && $unInstaller->init();
    }

    public function hookActionDispatcherBefore()
    {
        $this->autoLoad();
    }

    /**
     * Gets container with loaded classes defined in src folder
     *
     * @return SkeletonContainer
     */
    public function getContainer()
    {
        return $this->moduleContainer;
    }

    /**
     * Autoload's project files from /src directory
     */
    private function autoLoad()
    {
        $autoLoadPath = $this->getLocalPath().'vendor/autoload.php';

        require_once $autoLoadPath;
    }

    /**
     * Creates compiled dependency injection container which holds data configured in config/config.yml file.
     *
     * @throws Exception
     */
    private function compile()
    {
        $containerCache = $this->getLocalPath().'var/cache/container.php';
        $containerConfigCache = new ConfigCache($containerCache, self::DISABLE_CACHE);

        $containerClass = get_class($this).'Container';

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = new ContainerBuilder();
            $locator = new FileLocator($this->getLocalPath().'/config');
            $loader  = new YamlFileLoader($containerBuilder, $locator);
            $loader->load('config.yml');
            $containerBuilder->compile();
            $dumper = new PhpDumper($containerBuilder);

            $containerConfigCache->write(
                $dumper->dump(['class' => $containerClass]),
                $containerBuilder->getResources()
            );
        }

        require_once $containerCache;
        $this->moduleContainer = new $containerClass();
    }
}
