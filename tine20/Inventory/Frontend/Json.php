<?php
/**
 * Tine 2.0
 * @package     Inventory
 * @subpackage  Frontend
 * @license     http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @author      Philipp Schüle <p.schuele@metaways.de>
 * @copyright   Copyright (c) 2007-2013 Metaways Infosystems GmbH (http://www.metaways.de)
 * 
 */

/**
 *
 * This class handles all Json requests for the Inventory application
 *
 * @package     Inventory
 * @subpackage  Frontend
 */
class Inventory_Frontend_Json extends Tinebase_Frontend_Json_Abstract
{
    protected $_applicationName = 'Inventory';
    
    /**
     * the models handled by this frontend
     * @var array
     */
    protected $_configuredModels = array('InventoryItem');
    
    /**
     * user fields (created_by, ...) to resolve in _multipleRecordsToJson and _recordToJson
     *
     * @var array
     */
    protected $_resolveUserFields = array(
        'Inventory_Model_InventoryItem' => array('created_by', 'last_modified_by')
    );

    /**
     * get inventory import definitions
     *
     * @return Tinebase_Record_RecordSet
     *
     * @todo generalize this
     */
    protected function _getImportDefinitions()
    {
        $filter = new Tinebase_Model_ImportExportDefinitionFilter(array(
            array('field' => 'application_id',  'operator' => 'equals', 'value' => Tinebase_Application::getInstance()->getApplicationByName('Inventory')->getId()),
            array('field' => 'type',            'operator' => 'equals', 'value' => 'import'),
        ));
        
        $importDefinitions = Tinebase_ImportExportDefinition::getInstance()->search($filter);
        
        return $importDefinitions;
    }
    
    /**
     * import inventory items
     *
     * @param string $tempFileId to import
     * @param string $definitionId
     * @param array $importOptions
     * @param array $clientRecordData
     * @return array
     *
     * @todo generalize
     */
    public function importInventoryItems($tempFileId, $definitionId, $importOptions, $clientRecordData = array())
    {
        return $this->_import($tempFileId, $definitionId, $importOptions, $clientRecordData);
    }
    
    /**
     * get default definition
     *
     * @param Tinebase_Record_RecordSet $_importDefinitions
     * @return Tinebase_Model_ImportExportDefinition
     *
     * @todo generalize this
     */
    protected function _getDefaultImportDefinition($_importDefinitions)
    {
        try {
            $defaultDefinition = Tinebase_ImportExportDefinition::getInstance()->getByName('inv_tine_import_csv');
        } catch (Tinebase_Exception_NotFound $tenf) {
            if (count($_importDefinitions) > 0) {
                $defaultDefinition = $_importDefinitions->getFirstRecord();
            } else {
                $defaultDefinition = NULL;
            }
        }
        return $defaultDefinition;
    }
    
    /**
     * Returns registry data of the inventory.
     * @see Tinebase_Application_Json_Abstract
     *
     * @return mixed array 'variable name' => 'data'
     *
     * @todo generalize
     */
    public function getRegistryData()
    {
        $definitionConverter = new Tinebase_Convert_ImportExportDefinition_Json();
        $importDefinitions = $this->_getImportDefinitions();
        $defaultDefinition = $this->_getDefaultImportDefinition($importDefinitions);
        
        $defaultContainerArray = Tinebase_Container::getInstance()->getDefaultContainer('Inventory_Model_InventoryItem', NULL, 'defaultInventoryItemContainer')->toArray();
        $defaultContainerArray['account_grants'] = Tinebase_Container::getInstance()->getGrantsOfAccount(Tinebase_Core::getUser(), $defaultContainerArray['id'])->toArray();

        $registryData = array(
                'defaultInventoryItemContainer' => $defaultContainerArray,
                'defaultImportDefinition'   => $definitionConverter->fromTine20Model($defaultDefinition),
                'importDefinitions'         => array(
                    'results'               => $definitionConverter->fromTine20RecordSet($importDefinitions),
                    'totalcount'            => count($importDefinitions),
                ),
        );
        return $registryData;
    }
}
