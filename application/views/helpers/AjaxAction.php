<?php
/**
 * Ajax Action Helper for Zend Framework
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
 * @category   Zend
 * @package    Zend_View_Helper
 * @copyright  Copyright (c) 2010-2011 Web Rocker (http://web-rocker.de)
 * @license    http://web-rocker.de/projekte/new-bsd-license/     New BSD License
 */

/**
 * Ajax Action Helper
 *
 * @category   Zend
 * @package    Zend_View_Helper
 * @copyright  Copyright (c) 2010-2011 Web Rocker (http://web-rocker.de)
 * @license    http://web-rocker.de/projekte/new-bsd-license/     New BSD License
 */
class Zend_View_Helper_AjaxAction extends Zend_View_Helper_Abstract
{
    /**
     * @var string
     */
    public $defaultModule;
    
    /**
     * @var Zend_Cache_Core
     */
    protected $cache = false;
    
    /**
     * @var boolean
     */
    protected $enabled = true;
    
    public function __construct()
    {
        $front = Zend_Controller_Front::getInstance();
        $config = $front->getParam('bootstrap')->getOption('cacheaction');
        
        if (!$config)
        {
            $this->enabled = false;
            return;
        }
        
        if(isset($config['enabled']) && "false" == $config['enabled'])
        {
            $this->enabled = false;
            return;
        }
        
        $cache = 'action';
        if(isset($config['cache']))
        {
            $cache = $config['cache'];
        }
        
        $this->defaultModule = $front->getDefaultModule();
        $manager = $front
                  ->getParam('bootstrap')
                  ->getResource('cachemanager');
        $this->cache = $manager->getCache($cache);
    }

    public function ajaxAction($action, $controller, $module = null, array $params = array(), $partial='')
    {
        if ($module === null)
        {
            $module = $this->defaultModule;
        }
        
        $params['controller'] = $controller;
        $params['action']     = $action;
        $params['module']     = $module;
        
        $url = $this->view->url($params);
        $cacheKey = md5($url);

        /*
         * if we have a response, deliver the body
         */
        if ($this->cache && false !==($cachedResponse = $this->cache->load($cacheKey)))
        {
            return $cachedResponse->getBody();
        }

        /*
         * return patial that does the ajax
         */
        $params['ajaxActionUrl'] = $url;
        $params['ajaxActionId']  = $cacheKey;

        return $this->view->partial($partial, $module, $params);
    }
}