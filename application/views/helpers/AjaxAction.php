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

    public function __construct()
    {
        $front = Zend_Controller_Front::getInstance();
        $this->defaultModule = $front->getDefaultModule();
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

        /*
         * return patial that does the ajax magic
         */
        $url = $this->view->url($params);
        
        $params['ajaxActionUrl'] = $url;
        $params['ajaxActionId']  = md5($url);

        return $this->view->partial($partial, $module, $params);
    }
}