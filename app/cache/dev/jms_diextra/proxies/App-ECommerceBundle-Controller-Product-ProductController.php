<?php

namespace EnhancedProxy7bf30588_e9ba15be1ed50f9df92b9921f3d0156f3a528ff9\__CG__\App\ECommerceBundle\Controller\Product;

/**
 * CG library enhanced proxy class.
 *
 * This code was generated automatically by the CG library, manual changes to it
 * will be lost upon next generation.
 */
class ProductController extends \App\ECommerceBundle\Controller\Product\ProductController
{
    private $__CGInterception__loader;

    public function updateAction(\Symfony\Component\HttpFoundation\Request $request, $id, $lang = '')
    {
        $ref = new \ReflectionMethod('App\\ECommerceBundle\\Controller\\Product\\ProductController', 'updateAction');
        $interceptors = $this->__CGInterception__loader->loadInterceptors($ref, $this, array($request, $id, $lang));
        $invocation = new \CG\Proxy\MethodInvocation($ref, $this, array($request, $id, $lang), $interceptors);

        return $invocation->proceed();
    }

    public function newAction()
    {
        $ref = new \ReflectionMethod('App\\ECommerceBundle\\Controller\\Product\\ProductController', 'newAction');
        $interceptors = $this->__CGInterception__loader->loadInterceptors($ref, $this, array());
        $invocation = new \CG\Proxy\MethodInvocation($ref, $this, array(), $interceptors);

        return $invocation->proceed();
    }

    public function indexAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        $ref = new \ReflectionMethod('App\\ECommerceBundle\\Controller\\Product\\ProductController', 'indexAction');
        $interceptors = $this->__CGInterception__loader->loadInterceptors($ref, $this, array($request));
        $invocation = new \CG\Proxy\MethodInvocation($ref, $this, array($request), $interceptors);

        return $invocation->proceed();
    }

    public function editAction($id, \Symfony\Component\HttpFoundation\Request $request)
    {
        $ref = new \ReflectionMethod('App\\ECommerceBundle\\Controller\\Product\\ProductController', 'editAction');
        $interceptors = $this->__CGInterception__loader->loadInterceptors($ref, $this, array($id, $request));
        $invocation = new \CG\Proxy\MethodInvocation($ref, $this, array($id, $request), $interceptors);

        return $invocation->proceed();
    }

    public function createAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        $ref = new \ReflectionMethod('App\\ECommerceBundle\\Controller\\Product\\ProductController', 'createAction');
        $interceptors = $this->__CGInterception__loader->loadInterceptors($ref, $this, array($request));
        $invocation = new \CG\Proxy\MethodInvocation($ref, $this, array($request), $interceptors);

        return $invocation->proceed();
    }

    public function __CGInterception__setLoader(\CG\Proxy\InterceptorLoaderInterface $loader)
    {
        $this->__CGInterception__loader = $loader;
    }
}