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

use Db;
use Exception;

abstract class AbstractInstaller
{
    /**
     * @return bool
     */
    abstract public function init();

    /**
     * used to parse sql files, replace variables and prepare for execution
     *
     * @param string $fileName
     * @return string
     */
    abstract protected function getSqlStatements($fileName);

    /**
     * Gets current file name. Used for translations
     *
     * @param string $classInstance
     *
     * @return string
     *
     * @throws \ReflectionException
     */
    protected function getFileName($classInstance)
    {
        $reflection = new \ReflectionClass($classInstance);
        return $reflection->getShortName();
    }

    /**
     * Executes sql statements
     *
     * @param Db $database
     * @param string $sqlStatements
     *
     * @return bool
     * @throws Exception
     */
    protected function execute(Db $database, $sqlStatements)
    {
        try {
            $result = $database->execute($sqlStatements);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $result;
    }
}
