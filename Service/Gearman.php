<?php

namespace Mmoreramerino\GearmanBundle\Service;

use Mmoreramerino\GearmanBundle\Service\GearmanService;
use Mmoreramerino\GearmanBundle\Service\GearmanInterface;

/**
 * Implementation of GearmanInterface
 * 
 * @author Marc Morera <marc@ulabox.com>
 */
class Gearman extends GearmanService implements GearmanInterface
{
    /**
     * If workers are not loaded, they're loaded and returned.
     * Otherwise, they are simply returned
     *
     * @return Array Workers array getted from cache and saved
     */
    public function getWorkers()
    {
        /**
         * Always will be an Array
         */
        return $this->setWorkers();
    }
    
    
    /**
     * Runs a single task and returns a string representation of the result. 
     * It is up to the GearmanClient and GearmanWorker to agree on the format of the result. 
     * 
     * @param string $name A GearmanBundle registered function the worker is to execute 
     * @params Mixed $params
     * 
     * @return string A string representing the results of running a task. 
     */
    public function doJob($name, $params = array())
    {
        return $this->enqueue($name, $params, 'do');
    }
    
    
    /**
     * Runs a task in the background, returning a job handle which 
     *     can be used to get the status of the running task. 
     * 
     * @param string $name A GearmanBundle registered function the worker is to execute 
     * @params Mixed $params
     * 
     * @return string Job handle for the submitted task.
     */
    public function doBackgroundJob($name, $params = array())
    {
        return $this->enqueue($name, $params, 'doBackground');
    }
    
    
    /**
     * Runs a single high priority task and returns a string representation of the result. 
     * It is up to the GearmanClient and GearmanWorker to agree on the format of the result. 
     * High priority tasks will get precedence over normal and low priority tasks in the job queue. 
     * 
     * @param string $name A GearmanBundle registered function the worker is to execute 
     * @params Mixed $params
     * 
     * @return string A string representing the results of running a task. 
     */
    public function doHighJob($name, $params = array())
    {
        return $this->enqueue($name, $params, 'doHigh');
    }
    
    /**
     * Runs a high priority task in the background, returning a job handle which can be used to get the status of the running task.
     * High priority tasks take precedence over normal and low priority tasks in the job queue. 
     * 
     * @param string $name A GearmanBundle registered function the worker is to execute 
     * @params Mixed $params
     * 
     * @return string The job handle for the submitted task.
     */
    public function doHighBackgroundJob($name, $params = array())
    {
        return $this->enqueue($name, $params, 'doHighBackground');
    }
    
    /**
     * Runs a single low priority task and returns a string representation of the result. 
     * It is up to the GearmanClient and GearmanWorker to agree on the format of the result. 
     * Normal and high priority tasks will get precedence over low priority tasks in the job queue.
     * 
     * @param string $name A GearmanBundle registered function the worker is to execute 
     * @params Mixed $params
     * 
     * @return string A string representing the results of running a task. 
     */
    public function doLowJob($name, $params = array())
    {
        return $this->enqueue($name, $params, 'doLow');
    }
    
    /**
     * Runs a low priority task in the background, returning a job handle which can be used to get the status of the running task.
     * Normal and high priority tasks will get precedence over low priority tasks in the job queue.
     * 
     * @param string $name A GearmanBundle registered function the worker is to execute 
     * @params Mixed $params
     * 
     * @return string The job handle for the submitted task.
     */
    public function doLowBackgroundJob($name, $params = array())
    {
        return $this->enqueue($name, $params, 'doLowBackground');
    }
    
    
    /**
     * Get real worker from job name and enqueues the action given one
     *     method.
     *
     * @param string $jobName A GearmanBundle registered function the worker is to execute 
     * @param mixed $params
     * @param string $method
     * 
     * @return mixed Return result of the call
     */
    private function enqueue($jobName, $params, $method)
    {
        $worker = $this->getWorker($jobName);
        if (false !== $worker) {
            return $this->doEnqueue($worker, $params, $method);
        }
        
        return false;
    }
    
    /**
     * Execute a GearmanClient call given a worker, params and a method.
     * If any method is given, it performs a "do" call
     * 
     * If he GarmanClient call is asyncronous, result value will be a handler.
     * Otherwise, will return job result.
     *
     * @param array $worker
     * @param mixed $params
     * @param string $method
     * 
     * @return mixed  Return result of the GearmanClient call
     */
    private function doEnqueue(Array $worker, $params='', $method='do')
    {
        $gmclient= new \GearmanClient();
        $gmclient->addServer();
        
        return $gmclient->$method($worker['job']['realCallableName'], serialize($params));
    }
}