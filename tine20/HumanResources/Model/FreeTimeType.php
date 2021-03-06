<?php
/**
 * Tine 2.0
 *
 * @package     HumanResources
 * @subpackage  Model
 * @license     http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @author      Alexander Stintzing <a.stintzing@metaways.de>
 * @copyright   Copyright (c) 2012 Metaways Infosystems GmbH (http://www.metaways.de)
 *
 */

/**
 * HumanResources FreeTimeType Record Class
 *
 * @package     HumanResources
 * @subpackage  Model
 */
class HumanResources_Model_FreeTimeType extends Tinebase_Config_KeyFieldRecord
{
    /**
     * application the record belongs to
     *
     * @var string
     */
    protected $_application = 'HumanResources';

    public function __construct($_data = NULL, $_bypassFilters = FALSE, $_convertDates = TRUE) {
        $this->_validators['states'] =  array('allowEmpty' => TRUE);
        parent::__construct($_data, $_bypassFilters, $_convertDates);
    }
}
