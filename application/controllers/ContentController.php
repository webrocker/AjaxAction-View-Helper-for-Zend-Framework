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
 * @package    ContentController
 * @copyright  Copyright (c) 2010-2011 Web Rocker (http://web-rocker.de)
 * @license    http://web-rocker.de/projekte/new-bsd-license/     New BSD License
 */

/**
 * Demo ContentController
 *
 * @category   Zend
 * @package    ContentController
 * @copyright  Copyright (c) 2010-2011 Web Rocker (http://web-rocker.de)
 * @license    http://web-rocker.de/projekte/new-bsd-license/     New BSD License
 */
class ContentController extends Zend_Controller_Action
{
    public function getnavigationAction()
    {
        sleep(1);
        $this->view->assign('navi', 'This Navigation takes 1 second');
    }
    
    public function getcontentAction()
    {
        sleep(2);
        $this->view->assign('content', 'Delicious content takes 2 seconds');
    }
    
    public function gettweetsAction()
    {
        sleep(2);
        $twitter = new Zend_Service_Twitter_Search();
        $this->view->assign(
            'tweets', 
            $twitter->search('php', array('lang' => 'en', 'rpp' => 5))
        );
    }
}