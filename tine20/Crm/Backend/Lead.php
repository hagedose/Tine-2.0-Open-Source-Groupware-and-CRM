<?php
/**
 * Tine 2.0
 *
 * @package     Crm
 * @subpackage  Backend
 * @license     http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @author      Philipp Schüle <p.schuele@metaways.de>
 * @copyright   Copyright (c) 2007-2015 Metaways Infosystems GmbH (http://www.metaways.de)
 * 
 */

/**
 * backend for leads
 *
 * @package     Crm
 * @subpackage  Backend
 */
class Crm_Backend_Lead extends Tinebase_Backend_Sql_Abstract
{
    /**
     * Table name without prefix
     *
     * @var string
     */
    protected $_tableName = 'metacrm_lead';
    
    /**
     * Model name
     *
     * @var string
     */
    protected $_modelName = 'Crm_Model_Lead';

    /**
     * if modlog is active, we add 'is_deleted = 0' to select object in _getSelect()
     *
     * @var boolean
     */
    protected $_modlogActive = TRUE;

    /**
     * default column(s) for count
     *
     * @var string
     */
    protected $_defaultCountCol = 'id';

    /**
     * getGroupCountForField
     * 
     * @param $_filter
     * @param $_field
     * @return integer
     * 
     * @todo generalize
     */
    public function getGroupCountForField($_filter, $_field)
    {
        $select = $this->_db->select();
        
        if ($this->_modlogActive) {
            // don't fetch deleted objects
            $select->where($this->_db->quoteIdentifier($this->_tableName . '.is_deleted') . ' = 0');
        }
        
        $select->from(array($this->_tableName => $this->_tablePrefix . $this->_tableName), array(
            $_field             => $_field,
            'count'             => 'COUNT(' . $this->_db->quoteIdentifier($_field) . ')',
        ));
        $select->group($_field);
        $this->_addFilter($select, $_filter);
        
        $stmt = $this->_db->query($select);
        $rows = (array)$stmt->fetchAll(Zend_Db::FETCH_ASSOC);

        $result = array();
        foreach ($rows as $row) {
            $result[$row[$_field]] = $row['count'];
        }
        
        return $result;
    }

    /**
     * get the basic select object to fetch records from the database
     *  
     * @param array|string|Zend_Db_Expr $_cols columns to get, * per default
     * @param boolean $_getDeleted get deleted records (if modlog is active)
     * @return Zend_Db_Select
     */
    protected function _getSelect($_cols = '*', $_getDeleted = FALSE)
    {
        $select = parent::_getSelect($_cols, $_getDeleted);
        
        // return probableTurnover (turnover * probability)
        if ($_cols == '*' || array_key_exists('probableTurnover', (array)$_cols)) {
            $select->columns(
                array('probableTurnover' => '(' . $this->_db->quoteIdentifier($this->_tableName . '.turnover') 
                    . '*' . $this->_db->quoteIdentifier($this->_tableName . '.probability') . '*0.01)'
                )
            );
        }
        
        return $select;
    }
    
    /**
     * (non-PHPdoc)
     * @see Tinebase_Backend_Sql_Abstract::_appendForeignSort()
     * 
     * @todo generalize this: find a place (in model config?) for foreign record sorting information
     * @todo maybe we can use a temp table with joins here
     * @todo allow to to use it with keyfields, too (and/or switch those settings to keyfield configs)
     */
    protected function _appendForeignSort(Tinebase_Model_Pagination $pagination, Zend_Db_Select $select)
    {
        $virtualSortColumns = array(
            'leadstate_id'  => Crm_Model_Config::LEADSTATES,
            'leadsource_id' => Crm_Model_Config::LEADSOURCES,
            'leadtype_id'   => Crm_Model_Config::LEADTYPES,
        );
        
        $col = $pagination->sort;
        if (isset($virtualSortColumns[$col])) {
            $settings = Crm_Controller::getInstance()->getConfigSettings();
            $setting = $settings->{$virtualSortColumns[$col]};
            
            // create cases (when => then) for sql switch (CASE) command
            $cases = array();
            foreach ($setting as $settingRecord) {
                $cases[$settingRecord['id']] = $settingRecord[str_replace('_id', '', $col)];
            }
            
            $foreignSortCase = $this->_dbCommand->getSwitch($col, $cases);
            $select->columns(array('foreignSortCol' => $foreignSortCase));
            $pagination->sort = 'foreignSortCol';
        }
    }
}
