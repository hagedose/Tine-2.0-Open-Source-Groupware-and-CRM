<?php
/**
 * Tine 2.0
 * 
 * @package     Tinebase
 * @subpackage  Scheduler
 * @license     http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @copyright   Copyright (c) 2010-2011 Metaways Infosystems GmbH (http://www.metaways.de)
 * @author      Goekmen Ciyiltepe <g.ciyiltepe@metaways.de>
 */

/**
 * Task class with handle and run functions
 * 
 * @package     Tinebase
 * @subpackage  Server
 */
class Tinebase_Scheduler_Task extends Zend_Scheduler_Task 
{
    /**
     * minutely task (default)
     * 
     * @var string
     */
    const TASK_TYPE_MINUTELY = 'minutely';
    
    /**
     * hourly task
     * 
     * @var string
     */
    const TASK_TYPE_HOURLY = 'hourly';

    /**
     * daily task
     * 
     * @var string
     */
    const TASK_TYPE_DAILY = 'daily';
    
    /**
     * static task getter
     * 
     * @param  string $_type
     * @param  array $_requestOptions
     * @param  array $_taskOptions
     * @return Tinebase_Scheduler_Task
     */
    public static function getPreparedTask($_type, array $_requestOptions, array $_taskOptions = array())
    {
        $request = new Zend_Controller_Request_Simple();
        $request->setControllerName($_requestOptions['controller']);
        $request->setActionName($_requestOptions['action']);
        if ((isset($_requestOptions['params']) || array_key_exists('params', $_requestOptions))) {
            foreach ($_requestOptions['params'] as $key => $value) {
                $request->setParam($key, $value);
            }
        }
        
        $task = new Tinebase_Scheduler_Task($_taskOptions);
        $task->setMonths("Jan-Dec");
        $task->setWeekdays("Sun-Sat");
        $task->setDays("1-31");
        $task->setRequest($request);
        
        switch ($_type) {
            case self::TASK_TYPE_MINUTELY:
                $task->setHours("0-23");
                $task->setMinutes("0/1");
                break;
            case self::TASK_TYPE_HOURLY:
                $task->setHours("0-23");
                $task->setMinutes("0");
                break;
            case self::TASK_TYPE_DAILY:
                $task->setHours("0");
                $task->setMinutes("0");
                break;
        }
        
        return $task;
    }
    
    /**
     * add alarm task to scheduler
     * 
     * @param Zend_Scheduler $_scheduler
     */
    public static function addAlarmTask(Zend_Scheduler $_scheduler)
    {
        $task = self::getPreparedTask(self::TASK_TYPE_MINUTELY, array(
            'controller'    => 'Tinebase_Alarm',
            'action'        => 'sendPendingAlarms',
            'params'        => array(
                'eventName' => 'Tinebase_Event_Async_Minutely'
            ),
        ));
        $_scheduler->addTask('Tinebase_Alarm', $task);
        $_scheduler->saveTask();
        
        if (Tinebase_Core::isLogLevel(Zend_Log::INFO)) Tinebase_Core::getLogger()->info(__METHOD__ . '::' . __LINE__ . ' Saved task Tinebase_Alarm::sendPendingAlarms in scheduler.');
    }
    
    /**
     * add queue task to scheduler
     * 
     * @param Zend_Scheduler $_scheduler
     */
    public static function addQueueTask(Zend_Scheduler $_scheduler)
    {
        $task = self::getPreparedTask(self::TASK_TYPE_MINUTELY, array(
            'controller'    => 'Tinebase_ActionQueue',
            'action'        => 'processQueue',
        ));
        
        $_scheduler->addTask('Tinebase_ActionQueue', $task);
        $_scheduler->saveTask();
        
        if (Tinebase_Core::isLogLevel(Zend_Log::INFO)) Tinebase_Core::getLogger()->info(__METHOD__ . '::' . __LINE__ . ' Saved task Tinebase_ActionQueue::processQueue in scheduler.');
    }
    
    /**
     * @todo update script / analog
     * @todo tinebase setup initialize
     * 
     * @param Zend_Scheduler $_scheduler
     */
    public static function addImportTask(Zend_Scheduler $_scheduler)
    {
        $task = self::getPreparedTask(self::TASK_TYPE_MINUTELY, array(
            'controller'    => 'Tinebase_Controller_ScheduledImport',
            'action'        => 'runNextScheduledImport',
        ));
        
        $_scheduler->addTask('Tinebase_Controller_ScheduledImport', $task);
        $_scheduler->saveTask();
        
        if (Tinebase_Core::isLogLevel(Zend_Log::INFO)) Tinebase_Core::getLogger()->info(__METHOD__ . '::' . __LINE__ 
            . ' Saved task Tinebase_Controller_ScheduledImport::runNextScheduledImport in scheduler.');
    }
    
    /**
     * add cache cleanup task to scheduler
     * 
     * @param Zend_Scheduler $_scheduler
     */
    public static function addCacheCleanupTask(Zend_Scheduler $_scheduler)
    {
        $task = self::getPreparedTask(self::TASK_TYPE_DAILY, array(
            'controller'    => 'Tinebase_Controller',
            'action'        => 'cleanupCache',
        ));
        
        $_scheduler->addTask('Tinebase_CacheCleanup', $task);
        $_scheduler->saveTask();
        
        if (Tinebase_Core::isLogLevel(Zend_Log::INFO)) Tinebase_Core::getLogger()->info(__METHOD__ . '::' . __LINE__ 
            . ' Saved task Tinebase_Controller::cleanupCache in scheduler.');
    }
    
    /**
     * add sessions cleanup task to scheduler
     * 
     * @param Zend_Scheduler $_scheduler
     */
    public static function addSessionsCleanupTask(Zend_Scheduler $_scheduler)
    {
        $task = self::getPreparedTask(self::TASK_TYPE_HOURLY, array(
            'controller'    => 'Tinebase_Controller',
            'action'        => 'cleanupSessions',
        ));
        
        $_scheduler->addTask('Tinebase_cleanupSessions', $task);
        $_scheduler->saveTask();
        
        if (Tinebase_Core::isLogLevel(Zend_Log::INFO)) Tinebase_Core::getLogger()->info(__METHOD__ . '::' . __LINE__ 
            . ' Saved task Tinebase_Controller::cleanupSessions in scheduler.');
    }
    
    /**
     * add credential cache cleanup task to scheduler
     * 
     * @param Zend_Scheduler $_scheduler
     */
    public static function addCredentialCacheCleanupTask(Zend_Scheduler $_scheduler)
    {
        $task = self::getPreparedTask(self::TASK_TYPE_DAILY, array(
            'controller'    => 'Tinebase_Auth_CredentialCache',
            'action'        => 'clearCacheTable',
        ));
        
        $_scheduler->addTask('Tinebase_CredentialCacheCleanup', $task);
        $_scheduler->saveTask();
        
        if (Tinebase_Core::isLogLevel(Zend_Log::INFO)) Tinebase_Core::getLogger()->info(__METHOD__ . '::' . __LINE__ 
            . ' Saved task Tinebase_Auth_CredentialCache::clearCacheTable in scheduler.');
    }
    
    /**
     * add temp_file table cleanup task to scheduler
     * 
     * @param Zend_Scheduler $_scheduler
     */
    public static function addTempFileCleanupTask(Zend_Scheduler $_scheduler)
    {
        $task = self::getPreparedTask(self::TASK_TYPE_DAILY, array(
            'controller'    => 'Tinebase_TempFile',
            'action'        => 'clearTableAndTempdir',
        ));
        
        $_scheduler->addTask('Tinebase_TempFileCleanup', $task);
        $_scheduler->saveTask();
        
        if (Tinebase_Core::isLogLevel(Zend_Log::INFO)) Tinebase_Core::getLogger()->info(__METHOD__ . '::' . __LINE__ 
            . ' Saved task Tinebase_TempFile::clearTableAndTempdir in scheduler.');
    }
    
    /**
     * add deleted file cleanup task to scheduler
     * 
     * @param Zend_Scheduler $_scheduler
     */
    public static function addDeletedFileCleanupTask(Zend_Scheduler $_scheduler)
    {
        $task = Tinebase_Scheduler_Task::getPreparedTask(Tinebase_Scheduler_Task::TASK_TYPE_DAILY, array(
            'controller'    => 'Tinebase_FileSystem',
            'action'        => 'clearDeletedFiles',
        ));
        
        $_scheduler->addTask('Tinebase_DeletedFileCleanup', $task);
        $_scheduler->saveTask();
        
        if (Tinebase_Core::isLogLevel(Zend_Log::INFO)) Tinebase_Core::getLogger()->info(__METHOD__ . '::' . __LINE__ 
            . ' Saved task Tinebase_FileSystem::clearDeletedFiles in scheduler.');
    }

    /**
     * add access log cleanup task to scheduler
     * 
     * @param Zend_Scheduler $_scheduler
     */
    public static function addAccessLogCleanupTask(Zend_Scheduler $_scheduler)
    {
        $task = Tinebase_Scheduler_Task::getPreparedTask(Tinebase_Scheduler_Task::TASK_TYPE_DAILY, array(
            'controller'    => 'Tinebase_AccessLog',
            'action'        => 'clearTable',
        ));
        
        $_scheduler->addTask('Tinebase_AccessLogCleanup', $task);
        $_scheduler->saveTask();
        
        if (Tinebase_Core::isLogLevel(Zend_Log::INFO)) Tinebase_Core::getLogger()->info(__METHOD__ . '::' . __LINE__ 
            . ' Saved task Tinebase_AccessLog::clearTable in scheduler.');
    }
    
    /**
     * run requests
     * 
     * @see tine20/Zend/Scheduler/Zend_Scheduler_Task::run()
     * 
     * @todo remove the loop? can there be multiple requests?)
     * 
     * @return mixed (FALSE on error)
     */
    public function run()
    {
        if (Tinebase_Core::isLogLevel(Zend_Log::DEBUG)) Tinebase_Core::getLogger()->debug(__METHOD__ . '::' . __LINE__ 
            . ' Fetching requests .... ');
        
        foreach ($this->getRequests() as $request) {
            if (Tinebase_Core::isLogLevel(Zend_Log::INFO)) Tinebase_Core::getLogger()->info(__METHOD__ . '::' . __LINE__ 
                . ' Running request: ' . $request->getControllerName() . '::' . $request->getActionName());
            
            try {
                $controllerName = $request->getControllerName();
                list($appName) = explode('_', $controllerName);

                if (true !== Tinebase_Application::getInstance()->isInstalled($appName)) {
                    if (Tinebase_Core::isLogLevel(Zend_Log::WARN)) Tinebase_Core::getLogger()->warn(__METHOD__ . '::' . __LINE__
                        . ' Application ' . $appName . ' is not installed for scheduler job');
                    return false;
                }

                $controller = Tinebase_Controller_Abstract::getController($controllerName);
            } catch (Exception $e) {
                if (Tinebase_Core::isLogLevel(Zend_Log::NOTICE)) Tinebase_Core::getLogger()->notice(__METHOD__ . '::' . __LINE__ 
                    . ' Could not get controller for scheduler job: ' . $e->getMessage());
                return false;
            }
            
            // only the first request is processed because of this return 
            return call_user_func_array(array($controller, $request->getActionName()), $request->getUserParams());
        }
    }
}
