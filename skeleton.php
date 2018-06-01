<?php

use Invertus\Skeleton\Install\Installer;
use Invertus\Skeleton\Install\UnInstaller;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
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
        $this->displayName = $this->l('Skeleton');
        $this->description = $this->l('This is module description');
        $this->author = 'Invertus';

        parent::__construct();
        $this->autoLoad();
        $this->compile();
    }


    public function getTabs()
    {
        /**
         * @var Tab $tab
         */
        $tab = $this->getContainer()->get('install.tab');
        return $tab->getTabs();
    }

    public function getContent()
    {
        /**
         * @var Tab $tab
         */
        $tab = $this->getContainer()->get('install.tab');

        $redirectLink = $this->context->link->getAdminLink($tab->getControllerInfo());
        Tools::redirectAdmin($redirectLink);
    }

    public function install()
    {
        /**
         * @var Installer $installer
         */
        $installer = $this->getContainer()->get('installer');

        return parent::install() && $installer->init();
    }

    public function uninstall()
    {
        /**
         * @var UnInstaller $unInstaller
         */
        $unInstaller = $this->getContainer()->get('unInstaller');

        return parent::uninstall() && $unInstaller->init();
    }

    /**
     * Used for module auto load in admin controllers
     */
    public function hookModuleRoutes()
    {
        $tabs = $this->getTabs();

        $controllers = [];

        foreach ($tabs as $tab) {
            $controllers[] = $tab['class_name'];
        }

        if (in_array(Tools::getValue('controller'), $controllers)) {
            $this->autoLoad();
        }
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
     * Auto loads project files from /src directory
     */
    private function autoLoad()
    {
        $autoLoadPath = $this->getLocalPath().'/vendor/autoload.php';

        if (!file_exists($autoLoadPath)) {
            throw new FileNotFoundException('autoload.php file was not found on your module. Run composer install.');
        }

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
