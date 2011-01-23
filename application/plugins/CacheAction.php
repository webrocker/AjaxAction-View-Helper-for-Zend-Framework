<?php
/**
 * CacheAction Plugin for Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://web-rocker.de/projekte/new-bsd-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to community@web-rocker.de so we can send you a copy immediately.
 *
 * @category   Webrocker
 * @package    Webrocker_Plugin_CacheAction
 * @copyright  Copyright (c) 2010-2011 Web Rocker (http://web-rocker.de)
 * @license    http://web-rocker.de/projekte/new-bsd-license/     New BSD License
 */

/**
 * CacheAction FrontController Plugin
 *
 * @category   Webrocker
 * @package    Webrocker_Plugin_CacheAction
 * @copyright  Copyright (c) 2010-2011 Web Rocker (http://web-rocker.de)
 * @license    http://web-rocker.de/projekte/new-bsd-license/     New BSD License
 */
class Webrocker_Plugin_CacheAction extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var string
     */
    protected $cacheKey = '';
    
    /**
     * @var Zend_Cache_Core
     */
    protected $cache = null;
    
    /**
     * @var boolean
     */
    protected $enabled = true;

    protected $ttl = false;
    
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $this->ttl = $request->getParam('cacheaction-ttl', false);
        
        if (!$request->isGet() || false === $this->ttl) 
        {
            $this->getResponse()->setHeader('x-cacheaction-state', 'route not cached');
            $this->enabled = false;
            return;
        }
        
        $front = Zend_Controller_Front::getInstance();
        $config = $front->getParam('bootstrap')->getOption('cacheaction');
        
        if(isset($config['enabled']) && "false" == $config['enabled'])
        {
            $this->getResponse()->setHeader('x-cacheaction-state', 'disabled by config');
            $this->enabled = false;
            return;
        }
        
        $cache = 'action';
        if(isset($config['cache']))
        {
            $cache = $config['cache'];
        }
        
        if (null === $this->cache)
        {
            $manager = $front
                ->getParam('bootstrap')
                ->getResource('cachemanager');
            $this->cache = $manager->getCache($cache);
        }
    }
    
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if (!$this->enabled) 
        {
            return;
        }

        $this->cacheKey = md5($request->getPathInfo());

        if (false !== ($response = $this->cache->load($this->cacheKey))) 
        {
            $response->setHeader('x-cacheaction-hit', $this->cacheKey);
            $response->sendResponse();
            exit;
        }
    }

    public function dispatchLoopShutdown()
    {
        if (!$this->enabled || $this->getResponse()->isRedirect())
        {
            return;
        }
        
        $this->cache->save($this->getResponse(), $this->cacheKey, array(), $this->ttl);
        $this->getResponse()->setHeader('x-cacheaction-stored', $this->cacheKey . ' ttl ' . $this->ttl);
    }
    
}