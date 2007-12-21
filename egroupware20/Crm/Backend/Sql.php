<?php

/**
 * interface for projects class
 *
 * @package     Crm
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @author      Thomas Wadewitz <t.wadewitz@metaways.de>
 * @copyright   Copyright (c) 2007-2007 Metaways Infosystems GmbH (http://www.metaways.de)
 * @version     $Id: Sql.php 221 2007-11-12 12:35:08Z twadewitz  $
 *
 */
class Crm_Backend_Sql implements Crm_Backend_Interface
{
	/**
	* Instance of Crm_Backend_Sql_Projects
	*
	* @var Crm_Backend_Sql_Projects
	*/
    protected $projectsTable;

	/**
	* Instance of Crm_Backend_Sql_Leadsources
	*
	* @var Crm_Backend_Sql_Leadsources
	*/
    protected $leadsourcesTable;

	/**
	* Instance of Crm_Backend_Sql_Leadtypes
	*
	* @var Crm_Backend_Sql_Leadtypes
	*/
    protected $leadtypesTable;
    
	/**
	* Instance of Crm_Backend_Sql_Products
	*
	* @var Crm_Backend_Sql_Products
	*/
    protected $productsourceTable;
    
	/**
	* Instance of Crm_Backend_Sql_Projectstates
	*
	* @var Crm_Backend_Sql_Projectstates
	*/
    protected $projectstatesTable;    
        
    
	/**
	* the constructor
	*
	*/
    public function __construct()
    {
        $this->projectsTable      = new Egwbase_Db_Table(array('name' => 'egw_metacrm_project'));
        $this->leadsourcesTable   = new Egwbase_Db_Table(array('name' => 'egw_metacrm_leadsource'));
        $this->leadtypesTable     = new Egwbase_Db_Table(array('name' => 'egw_metacrm_leadtype'));
        $this->productsourceTable = new Egwbase_Db_Table(array('name' => 'egw_metacrm_productsource'));
        $this->projectstatesTable = new Egwbase_Db_Table(array('name' => 'egw_metacrm_projectstate'));
        $this->productsTable      = new Egwbase_Db_Table(array('name' => 'egw_metacrm_product'));
    }

    
	// handle LEADSOURCES
	/**
	* get Leadsources
	*
	* @return unknown
	*/
    public function getLeadsources($sort, $dir)
    {	
		$result = $this->leadsourcesTable->fetchAll(NULL, $sort, $dir);
        return $result;
	}

	/**
	* add or updates an option
	*
	* @param Crm_Leadsource $_optionData the optiondata
	* @return unknown
	*/
    public function saveLeadsource(Crm_Leadsource $_optionData)
    {

                
        $optionData = $_optionData->toArray();

        if($_optionData->pj_leadsource_id === NULL) {        
            $result = $this->leadsourcesTable->insert($optionData);
            $_optionData->pj_leadsource_id = $this->leadsourcesTable->getAdapter()->lastInsertId();
        } else {
            $where  = array(
                $this->leadsourcesTable->getAdapter()->quoteInto('pj_leadsource_id = (?)', $_optionData->pj_leadsource_id),
            );
            $result = $this->leadsourcesTable->update($optionData, $where);
        }

        return $_optionData;
    }

    /**
     * delete option identified by id and table
     *
     * @param int $_Id option id
     * @param $_table which option section
     * @return int the number of rows deleted
     */
    public function deleteLeadsourceById($_Id)
    {
        $Id = (int)$_Id;
        if($Id != $_Id) {
            throw new InvalidArgumentException('$_Id must be integer');
        }
            $where  = array(
                $this->leadsourcesTable->getAdapter()->quoteInto('pj_leadsource_id = ?', $Id),
            );
             
            $result = $this->leadsourcesTable->delete($where);

        return $result;
    }
    
    
	// handle LEADTYPES
	/**
	* get Leadtypes
	*
	* @return unknown
	*/
    public function getLeadtypes($sort, $dir)
    {	
		$result = $this->leadtypesTable->fetchAll(NULL, $sort, $dir);
        return $result;
	}	
    
	/**
	* add or updates an option
	*
	* @param Crm_Leadtype $_optionData the optiondata
	* @return unknown
	*/
    public function saveLeadtype(Crm_Leadtype $_optionData)
    {

                
        $optionData = $_optionData->toArray();

        if($_optionData->pj_leadtype_id === NULL) {        
            $result = $this->leadtypesTable->insert($optionData);
            $_optionData->pj_leadtype_id = $this->leadtypesTable->getAdapter()->lastInsertId();
        } else {
            $where  = array(
                $this->leadtypesTable->getAdapter()->quoteInto('pj_leadtype_id = (?)', $_optionData->pj_leadtype_id),
            );
            $result = $this->leadtypesTable->update($optionData, $where);
        }

        return $_optionData;
    }

    /**
     * delete option identified by id and table
     *
     * @param int $_Id option id
     * @param $_table which option section
     * @return int the number of rows deleted
     */
    public function deleteLeadtypeById($_Id)
    {
        $Id = (int)$_Id;
        if($Id != $_Id) {
            throw new InvalidArgumentException('$_Id must be integer');
        }
            $where  = array(
                $this->leadtypesTable->getAdapter()->quoteInto('pj_leadtype_id = ?', $Id),
            );
             
            $result = $this->leadtypesTable->delete($where);

        return $result;
    }    
    
  
	// handle PRODUCTS AVAILABLE
	/**
	* get Products available
	*
	* @return unknown
	*/
    public function getProductsAvailable($sort, $dir)
    {	
		$result = $this->productsourceTable->fetchAll(NULL, $sort, $dir);
        return $result;
	}    
    
	/**
	* add or updates an option
	*
	* @param Crm_Productsource $_optionData the optiondata
	* @return unknown
	*/
    public function saveProductsource(Crm_Productsource $_optionData)
    {
      
        
        $optionData = $_optionData->toArray();

        if($_optionData->pj_productsource_id === NULL) {        
            $result = $this->productsourceTable->insert($optionData);
            $_optionData->pj_productsource_id = $this->productsourceTable->getAdapter()->lastInsertId();
        } else {
            $where  = array(
                $this->productsourceTable->getAdapter()->quoteInto('pj_productsource_id = (?)', $_optionData->pj_productsource_id),
            );
            $result = $this->productsourceTable->update($optionData, $where);
        }

        return $_optionData;
    }

    /**
     * delete option identified by id and table
     *
     * @param int $_Id option id
     * @param $_table which option section
     * @return int the number of rows deleted
     */
    public function deleteProductsourceById($_Id)
    {
        $Id = (int)$_Id;
        if($Id != $_Id) {
            throw new InvalidArgumentException('$_Id must be integer');
        }      
            $where  = array(
                $this->productsourceTable->getAdapter()->quoteInto('pj_productsource_id = ?', $Id),
            );
             
            $result = $this->productsourceTable->delete($where);

        return $result;
    }    
    
  
	// handle PROJECTSTATES    
	/**
	* get Projectstates
	*
	* @return unknown
	*/
    public function getProjectstates($sort, $dir)
    {	
    	$result = $this->projectstatesTable->fetchAll(NULL, $sort, $dir);
   
        return $result;
	}    
  
	/**
	* add or updates an option
	*
	* @param Crm_Projectstate $_optionData the optiondata
	* @return unknown
	*/
    public function saveProjectstate(Crm_Projectstate $_optionData)
    {   
        $optionData = $_optionData->toArray();

        if($_optionData->pj_projectstate_id === NULL) {        
            $result = $this->projectstatesTable->insert($optionData);
            $_optionData->pj_projectstate_id = $this->projectstatesTable->getAdapter()->lastInsertId();
        } else {
            $where  = array(
                $this->projectstatesTable->getAdapter()->quoteInto('pj_projectstate_id = (?)', $_optionData->pj_projectstate_id),
            );
            $result = $this->projectstatesTable->update($optionData, $where);
        }

        return $_optionData;
    }

    /**
     * delete option identified by id and table
     *
     * @param int $_Id option id
     * @param $_table which option section
     * @return int the number of rows deleted
     */
    public function deleteProjectstateById($_Id)
    {
        $Id = (int)$_Id;
        if($Id != $_Id) {
            throw new InvalidArgumentException('$_Id must be integer');
        }      
            $where  = array(
                $this->projectstatesTable->getAdapter()->quoteInto('pj_projectstate_id = ?', $Id),
            );
             
            $result = $this->projectstatesTable->delete($where);

        return $result;
    }


	// handle PRODUCTS (associated to project)
	/**
	* get products by project id
	*
	* 
	* 
	* 
	* @return unknown
	*/
     public function getProductsById($_id)
    {
        $id = (int) $_id;
        if($id != $_id) {
            throw new InvalidArgumentException('$_id must be integer');
        }

        $where  = array(
            $this->productsTable->getAdapter()->quoteInto('pj_project_id = ?', $_id)
        );

        $result = $this->productsTable->fetchAll($where);

        return $result;
    }      
   
	/**
	* get a single product by Id, which belong to one project
	* (needed for deleting to get project owner)
	* 
	* 
	* 
	* @return unknown
	*/
     public function getProductByProjectId($_id)
    {    
        $id = (int)$_id;
        
        if($id != $_id) {
            throw new InvalidArgumentException('$_id must be integer');
        }

        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select()
        ->from(array('egw_metacrm_product','egw_metacrm_project'))
        ->join('egw_metacrm_project','egw_metacrm_project.pj_id = egw_metacrm_project.pj_project_id')
        ->where('egw_metacrm_product.pj_id = ?', $id);     
    } 
	/**
	* add or updates an product (which belongs to one project)
	*
	* @param int $_productId the id of the product, NULL if new, else gets updated
	* @param Crm_Product $_productData the productdata
	* @param int $_projectId the project id
	* @return unknown
	*/
    public function saveProduct(Crm_Product $_productData)
    {
        /*  if(!Zend_Registry::get('currentAccount')->hasGrant($_projectData->pj_owner, Egwbase_Container::GRANT_EDIT)) {
            throw new Exception('write access to project->product denied');
        }    
    */        
        $productData = $_productData->toArray();

        if($_productData->pj_id === NULL) {
            $result = $this->productsTable->insert($productData);
            $_productData->pj_id = $this->productsTable->getAdapter()->lastInsertId();
        } else {
            $where  = array(
                $this->productsTable->getAdapter()->quoteInto('pj_id = (?)', $_productData->pj_id),
            );

            $result = $this->productsTable->update($productData, $where);
        }

        return $_productData;
    }

   /**
     * delete product identified by product id
     *
     * @param int $_productId product id
     * @return int the number of rows deleted
     */
    public function deleteProductById($_productId)
    {
        $productId = (int)$_productId;
        if($productId != $_productId) {
            throw new InvalidArgumentException('$_productId must be integer');
        }
/*
        $oldProductData = $this->getProductById($_productId);

        if(!Zend_Registry::get('currentAccount')->hasGrant($oldProductData->pj_owner, Egwbase_Container::GRANT_DELETE)) {
            throw new Exception('delete access to CRM denied');
        } */
        
        $where  = array(
            $this->productsTable->getAdapter()->quoteInto('pj_id = ?', $productId),
        );
         
        $result = $this->productsTable->delete($where);

        return $result;
    }
    
   
	// handle PROJECTS    
	/**
	* get single project by id
	*
	* 
	* 
	* 
	* @return unknown
	*/
     public function getProjectById($_id)
    {
        $id = (int) $_id;
        if($id != $_id) {
            throw new InvalidArgumentException('$_id must be integer');
        }

        $accountId = Zend_Registry::get('currentAccount')->account_id;

        $where  = array(
            $this->projectsTable->getAdapter()->quoteInto('pj_id = ?', $_id)
        );

        $result = $this->projectsTable->fetchRow($where);

        if($result === NULL) {
            throw new UnderFlowExecption('project not found');
        }
        
        if(!Zend_Registry::get('currentAccount')->hasGrant($result->pj_owner, Egwbase_Container::GRANT_READ)) {
            throw new Exception('permission to project denied');
        }
        
        return $result;
    }    
    
    public function getProjectsByOwner($_owner, $_filter, $_sort, $_dir, $_limit = NULL, $_start = NULL)
    {
        $owner = (int)$_owner;
        if($owner != $_owner) {
            throw new InvalidArgumentException('$_owner must be integer');
        }
        $ownerContainer = Egwbase_Container::getInstance()->getPersonalContainer('crm', $owner);
        
        $containerIds = array();
        
        foreach($ownerContainer as $container) {
            $containerIds[] = $container->container_id;
        }
        
        $where = array(
            $this->projectsTable->getAdapter()->quoteInto('pj_owner IN (?)', $containerIds)
        );

        $result = $this->_getProjectsFromTable($where, $_filter, $_sort, $_dir, $_limit, $_start);
         
        return $result;
    }
    
    public function getCountByOwner($_owner, $_filter)
    {
        $owner = (int)$_owner;
        if($owner != $_owner) {
            throw new InvalidArgumentException('$_owner must be integer');
        }
        $ownerContainer = Egwbase_Container::getInstance()->getPersonalContainer('crm', $owner);
        
        $containerIds = array();
        
        foreach($ownerContainer as $container) {
            $containerIds[] = $container->container_id;
        }
        
        $where = array(
            $this->projectsTable->getAdapter()->quoteInto('pj_owner IN (?)', $containerIds)
        );
        
        $where = $this->_addQuickSearchFilter($where, $_filter);
        
        $result = $this->projectsTable->getTotalCount($where);

        return $result;
    }    

	/**
	* add or updates an project
	*
	* @param int $_projectOwner the owner of the Crm entry
	* @param Crm_Project $_projectData the projectdata
	* @param int $_projectId the project to update, if NULL the project gets added
	* @return unknown
	*/
    public function saveProject(Crm_Project $_projectData)
    {
        if(empty($_projectData->pj_owner)) {
            throw new UnderflowException('pj_owner can not be empty');
        }
        
        if(!Zend_Registry::get('currentAccount')->hasGrant($_projectData->pj_owner, Egwbase_Container::GRANT_EDIT)) {
            throw new Exception('write access to project denied');
        }

        $currentAccount = Zend_Registry::get('currentAccount');

        $projectData = $_projectData->toArray();

        unset($projectData['pj_id']);
        if(empty($projectData['pj_owner'])) {
            $projectData['pj_owner'] = $currentAccount->account_id;
        }

        if($_projectData->pj_id === NULL) {
            $result = $this->projectsTable->insert($projectData);
            $_projectData->pj_id = $this->projectsTable->getAdapter()->lastInsertId();
        } else {      
            $where  = array(
                $this->projectsTable->getAdapter()->quoteInto('pj_id = (?)', $_projectData->pj_id),
            );

            $result = $this->projectsTable->update($projectData, $where);
        }

        return $_projectData;
    }

    /**
     * delete project identified by pj_id
     *
     * @param int $_projects project ids
     * @return int the number of rows deleted
     */
    public function deleteProjectById($_projectId)
    {
        $projectId = (int)$_projectId;
        if($projectId != $_projectId) {
            throw new InvalidArgumentException('$_projectId must be integer');
        }

        $oldProjectData = $this->getProjectById($_projectId);

        if(!Zend_Registry::get('currentAccount')->hasGrant($oldProjectData->pj_owner, Egwbase_Container::GRANT_DELETE)) {
            throw new Exception('delete access to CRM denied');
        }
       
        $where  = array(
            $this->projectsTable->getAdapter()->quoteInto('pj_id = ?', $projectId),
        );

        $result = $this->projectsTable->delete($where);

        return $result;
    }


	// handle FOLDERS  
    public function addFolder($_name, $_type) 
    {
        $egwbaseContainer = Egwbase_Container::getInstance();
        $accountId   = Zend_Registry::get('currentAccount')->account_id;
        $allGrants = array(
            Egwbase_Container::GRANT_ADD,
            Egwbase_Container::GRANT_ADMIN,
            Egwbase_Container::GRANT_DELETE,
            Egwbase_Container::GRANT_EDIT,
            Egwbase_Container::GRANT_READ
        );
        
        if($_type == Egwbase_Container::TYPE_SHARED) {
            $folderId = $egwbaseContainer->addContainer('crm', $_name, Egwbase_Container::TYPE_SHARED, Crm_Backend::SQL);

            // add admin grants to creator
            $egwbaseContainer->addGrants($folderId, $accountId, $allGrants);
            // add read grants to any other user
            $egwbaseContainer->addGrants($folderId, NULL, array(Egwbase_Container::GRANT_READ));
        } else {
            $folderId = $egwbaseContainer->addContainer('crm', $_name, Egwbase_Container::TYPE_PERSONAL, Crm_Backend::SQL);
        
            // add admin grants to creator
            $egwbaseContainer->addGrants($folderId, $accountId, $allGrants);
        }
        
        return $folderId;
    }
    
    public function deleteFolder($_folderId)
    {
        $egwbaseContainer = Egwbase_Container::getInstance();
        
        $egwbaseContainer->deleteContainer($_folderId);
        
        $where = array(
            $this->projectsTable->getAdapter()->quoteInto('pj_owner = ?', (int)$_folderId)
        );
        
        //$this->projectsTable->delete($where);
        
        return true;
    }
    
    public function renameFolder($_folderId, $_name)
    {
        $egwbaseContainer = Egwbase_Container::getInstance();
        
        $egwbaseContainer->renameContainer($_folderId, $_name);
                
        return true;
    }    
     
     
    public function getFoldersByOwner($_owner) 
    {
        $personalFolders = Egwbase_Container::getInstance()->getPersonalContainer('crm', $_owner);
                
        return $personalFolders;
    }   
 
    public function getSharedFolders() {
        $sharedFolders = Egwbase_Container::getInstance()->getSharedContainer('crm');
                
        return $sharedFolders;
    }
    
    public function getOtherUsers() 
    {
        $rows = Egwbase_Container::getInstance()->getOtherUsers('crm');

        $accountData = array();

        foreach($rows as $account) {
            $accountData[] = array(
                'account_id'      => $account['account_id'],
                'account_loginid' => 'loginid',
                'account_name'    => 'Account ' . $account['account_id']
            );
        }

        $result = new Egwbase_Record_RecordSet($accountData, 'Egwbase_Record_Account');
        
        return $result;
    }


    //handle for FOLDER->PROJECTS functions
    protected function _getProjectsFromTable(array $_where, $_filter, $_sort, $_dir, $_limit, $_start)
    {
        $where = $this->_addQuickSearchFilter($_where, $_filter);

        $result = $this->projectsTable->fetchAll($where, $_sort, $_dir, $_limit, $_start);
         
        return $result;
    }   

    protected function _addQuickSearchFilter($_where, $_filter)
    {
        if(!empty($_filter)) {
            $_where[] = $this->projectsTable->getAdapter()->quoteInto('(pj_name LIKE ? OR pj_description LIKE ?)', '%' . $_filter . '%');
        }
        
        return $_where;
    }


// handle FOLDER->PROJECTS overview
    /**
     * get list of projects from all shared folders the current user has access to
     *
     * @param string $_filter string to search for in projects
     * @param unknown_type $_sort fieldname to sort by
     * @param unknown_type $_dir sort ascending or descending (ASC | DESC)
     * @param unknown_type $_limit how many projects to display
     * @param unknown_type $_start how many projects to skip
     * @return unknown The row results per the Zend_Db_Adapter fetch mode.
     */
    public function getAllProjects($_filter, $_sort, $_dir, $_limit = NULL, $_start = NULL)
    {
        $allContainer = Zend_Registry::get('currentAccount')->getContainerByACL('crm', Egwbase_Container::GRANT_READ);
        
        $containerIds = array();
        
        foreach($allContainer as $container) {
            $containerIds[] = $container->container_id;
        }
        
        $where = array(
            $this->projectsTable->getAdapter()->quoteInto('pj_owner IN (?)', $containerIds)
        );

        $result = $this->_getProjectsFromTable($where, $_filter, $_sort, $_dir, $_limit, $_start);
         
        return $result;
    }

    /**
     * get total count of all projects from shared folders
     *
     * @todo return the correct count (the accounts are missing)
     *
     * @return int count of all other users projects
     */
    public function getCountOfAllProjects($_filter)
    {
        $allContainer = Zend_Registry::get('currentAccount')->getContainerByACL('crm', Egwbase_Container::GRANT_READ);
        
        $containerIds = array();
        
        foreach($allContainer as $container) {
            $containerIds[] = $container->container_id;
        }
        
        $where = array(
            $this->projectsTable->getAdapter()->quoteInto('pj_owner IN (?)', $containerIds)
        );
        
        $where = $this->_addQuickSearchFilter($where, $_filter);
        
        $result = $this->projectsTable->getTotalCount($where);

        return $result;
    }
   
   
    public function getProjectsByFolderId($_folderId, $_filter, $_sort, $_dir, $_limit = NULL, $_start = NULL)
    {
        // convert to int
        $folderId = (int)$_folderId;
        if($folderId != $_folderId) {
            throw new InvalidArgumentException('$_folderId must be integer');
        }
        
        if(!Zend_Registry::get('currentAccount')->hasGrant($_folderId, Egwbase_Container::GRANT_READ)) {
            throw new Exception('read access denied to folder');
        }
        
        $where = array(
            $this->projectsTable->getAdapter()->quoteInto('pj_owner = ?', $folderId)
        );

        $result = $this->_getProjectsFromTable($where, $_filter, $_sort, $_dir, $_limit, $_start);
         
        return $result;
    }
    
    public function getCountByFolderId($_folderId, $_filter)
    {
        $folderId = (int)$_folderId;
        if($folderId != $_folderId) {
            throw new InvalidArgumentException('$_folderId must be integer');
        }
        
        if(!Zend_Registry::get('currentAccount')->hasGrant($folderId, Egwbase_Container::GRANT_READ)) {
            throw new Exception('read access denied to folder');
        }
        
        $where = array(
            $this->projectsTable->getAdapter()->quoteInto('pj_owner = ?', $folderId)
        );
                
        $where = $this->_addQuickSearchFilter($where, $_filter);
        
        $result = $this->projectsTable->getTotalCount($where);

        return $result;
    } 

    
    public function getSharedProjects($_filter, $_sort, $_dir, $_limit = NULL, $_start = NULL) 
    {
        $sharedContainer = Egwbase_Container::getInstance()->getSharedContainer('crm');
        
        $containerIds = array();
        
        foreach($sharedContainer as $container) {
            $containerIds[] = $container->container_id;
        }
        
        $where = array(
            $this->projectsTable->getAdapter()->quoteInto('pj_owner IN (?)', $containerIds)
        );

        $result = $this->_getProjectsFromTable($where, $_filter, $_sort, $_dir, $_limit, $_start);
         
        return $result;
    }
    
    /**
     * get total count of all projects from shared folders
     *
     * @return int count of all other users projects
     */
    public function getCountOfSharedProjects()
    {
        $currentAccount = Zend_Registry::get('currentAccount');

        $acl = $this->egwbaseAcl->getGrants($currentAccount->account_id, 'crm', Egwbase_Acl::READ, Egwbase_Acl::GROUP_GRANTS);

        if(empty($acl)) {
            return false;
        }

        $groupIds = array_keys($acl);

        $result = $this->projectsTable->getCountByAcl($groupIds);

        return $result;
    }        
 
   
   public function getOtherPeopleProjects($_filter, $_sort, $_dir, $_limit = NULL, $_start = NULL) 
    {
        $otherPeoplesContainer = Egwbase_Container::getInstance()->getOtherUsersContainer('crm');
        
        $containerIds = array();
        
        foreach($otherPeoplesContainer as $container) {
            $containerIds[] = $container->container_id;
        }
        
        $where = array(
            $this->projectsTable->getAdapter()->quoteInto('pj_owner IN (?)', $containerIds)
        );

        $result = $this->_getProjectsFromTable($where, $_filter, $_sort, $_dir, $_limit, $_start);
         
        return $result;
    }
    
    /**
     * get total count of all other users projects
     *
     * @return int count of all other users projects
     * 
     */
    public function getCountOfOtherPeopleProjects()
    {
        $currentAccount = Zend_Registry::get('currentAccount');

        $acl = $this->egwbaseAcl->getGrants($currentAccount->account_id, 'crm', Egwbase_Acl::READ, Egwbase_Acl::ACCOUNT_GRANTS);

        if(empty($acl)) {
            return false;
        }

        $groupIds = array_keys($acl);

        $result = $this->projectsTable->getCountByAcl($groupIds);

        return $result;
    }   
 
    
}
