<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Vassetmanager for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Vassetmanager;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractController', 'dispatch', array($this, 'setEdpModuleLayouts'), 100);
        $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractController', 'dispatch', array($this, 'setHeadValues'), 100);
    }

    /**
     * Creates stylesheet and javascript links based on Config['head_values'][module] settings
     * Additionally links also automatically CSS and JS basing on controller/action
     * Function should be attached to dispatch event
     *
     *
     * @param MvcEvent $e
     */
    public function setHeadValues(MvcEvent $e) {
        $controller         = $e->getTarget();
        $controllerClass    = get_class($controller);
        $moduleNamespace    = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        $controllerName     = str_replace('Controller', "", substr($controllerClass, strrpos($controllerClass, '\\')+1 ));
        $config             = $e->getApplication()->getServiceManager()->get('config');

//         $defaultFilePathSegment = str_replace("/-", "/", strtolower(preg_replace('/([A-Z])/', '-$1', "/" . $moduleNamespace . "/" . $controllerName . "/" . $controller->params("action"))));
        $defaultFilePathSegment = str_replace("/-", "/", strtolower(preg_replace('/([A-Z])/', '-$1', "/asset/%s/" . $moduleNamespace . "/" . $controllerName . "/" . $controller->params("action"))));


        if (isset($config['head_values'][$moduleNamespace]['title'])) {
            $HeadTitleHelper = $e->getApplication()->getServiceManager()->get('ViewHelperManager')->get('HeadTitle');
            $HeadTitleHelper($config['head_values'][$moduleNamespace]['title']);
        }

        $scripts = array();
        if (isset($config['head_values'][$moduleNamespace]['inherit_scripts']))
            if (is_array($config['head_values'][$moduleNamespace]['inherit_scripts']))
                foreach($config['head_values'][$moduleNamespace]['inherit_scripts'] as $inheritScriptsModule)
                    if (isset($config['head_values'][$inheritScriptsModule]['scripts']))
                        $scripts = array_merge($scripts, $config['head_values'][$inheritScriptsModule]['scripts']);
        if (isset($config['head_values'][$moduleNamespace]['scripts']))
            $scripts = array_merge($scripts, $config['head_values'][$moduleNamespace]['scripts']);
        if (isset($config['head_values'][$moduleNamespace]['final_scripts']))
            $scripts = array_merge($scripts, $config['head_values'][$moduleNamespace]['final_scripts']);

        $defaultJSPath      = sprintf($defaultFilePathSegment, 'js');
//         if (file_exists($_SERVER["DOCUMENT_ROOT"] . $defaultJSPath))
            $scripts[] = $defaultJSPath;

        if (sizeof($scripts) > 0) {
            $scripts = array_unique($scripts);
            $HeadScriptHelper= $e->getApplication()->getServiceManager()->get('ViewHelperManager')->get('HeadScript');
            foreach($scripts as $scriptFile)
                $HeadScriptHelper()->appendFile($scriptFile);
        }

        $stylesheets = array();
        if (isset($config['head_values'][$moduleNamespace]['inherit_stylesheets']))
            if (is_array($config['head_values'][$moduleNamespace]['inherit_stylesheets']))
                foreach($config['head_values'][$moduleNamespace]['inherit_stylesheets'] as $inheritStylesheetsModule)
                    if (isset($config['head_values'][$inheritStylesheetsModule]['stylesheets']))
                        $stylesheets = array_merge($stylesheets, $config['head_values'][$inheritStylesheetsModule]['stylesheets']);
        if (isset($config['head_values'][$moduleNamespace]['stylesheets']))
            $stylesheets = array_merge($stylesheets, $config['head_values'][$moduleNamespace]['stylesheets']);
        if (isset($config['head_values'][$moduleNamespace]['final_stylesheets']))
            $stylesheets = array_merge($stylesheets, $config['head_values'][$moduleNamespace]['final_stylesheets']);

        $defaultCSSPath     = sprintf($defaultFilePathSegment, 'css');
//         if (file_exists($_SERVER["DOCUMENT_ROOT"] . $defaultCSSPath))
            $stylesheets[] = $defaultCSSPath;

        if (sizeof($stylesheets) > 0) {
            $stylesheets = array_unique($stylesheets);
            $HeadLinkHelper= $e->getApplication()->getServiceManager()->get('ViewHelperManager')->get('HeadLink');
            foreach($stylesheets as $stylesheetFile)
                $HeadLinkHelper()->appendStylesheet($stylesheetFile);
        }

    }

    /**
     * Sets module layouts basing on Config['module_layouts][module] settings
     * Based on EdpModuleLayouts
     * Function should be attached to dispatch event
     *
     * @link https://github.com/EvanDotPro/EdpModuleLayouts
     *
     * @param MvcEvent $e
     */
    public function setEdpModuleLayouts(MvcEvent $e) {
        $controller      = $e->getTarget();
        $controllerClass = get_class($controller);
        $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        $config          = $e->getApplication()->getServiceManager()->get('config');
        if (isset($config['module_layouts'][$moduleNamespace])) {
            $controller->layout($config['module_layouts'][$moduleNamespace]);
        }
    }
}
