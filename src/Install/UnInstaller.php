<?php


namespace Invertus\Skeleton\Install;

use Configuration;
use Tools;

/**
 * Class UnInstaller - responsible for module installation process
 * @package Invertus\Skeleton\Install
 */
class UnInstaller extends AbstractInstaller
{
    /**
     * @var \Skeleton
     */
    private $module;
    /**
     * @var array
     */
    private $configuration;

    /**
     * Installer constructor.
     *
     * @param \Skeleton $module
     * @param array $configuration
     */
    public function __construct(\Skeleton $module, array $configuration)
    {
        $this->module = $module;
        $this->configuration = $configuration;
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->uninstallConfiguration();

        if (!$this->uninstallDb()) {
            return false;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function getSqlStatements($fileName)
    {
        $sqlStatements = Tools::file_get_contents($fileName);
        $sqlStatements = str_replace(['PREFIX_', 'ENGINE_TYPE'], [_DB_PREFIX_, _MYSQL_ENGINE_], $sqlStatements);

        return $sqlStatements;
    }

    private function uninstallConfiguration()
    {
        $configuration = $this->configuration['configuration'];

        if (null === $configuration || empty($configuration)) {
            return;
        }

        $configurationNames = array_keys($configuration);

        if (empty($configurationNames) || null === $configurationNames) {
            return;
        }

        foreach ($configurationNames as $name) {
            if (!Configuration::deleteByName($name)) {
                continue;
            }
        }
    }

    /**
     * Executes sql in uninstall.sql file which is used for uninstalling
     *
     * @return bool
     * @throws \Exception
     */
    private function uninstallDb()
    {
        $uninstallSqlFileName = $this->module->getLocalPath().'sql/uninstall/uninstall.sql';
        if (!file_exists($uninstallSqlFileName)) {
            return true;
        }

        $database = \Db::getInstance();
        $sqlStatements = $this->getSqlStatements($uninstallSqlFileName);
        return (bool) $this->execute($database, $sqlStatements);
    }
}
