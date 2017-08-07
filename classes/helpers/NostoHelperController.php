<?php

/**
 * 2013-2017 Nosto Solutions Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@nosto.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Nosto Solutions Ltd <contact@nosto.com>
 * @copyright 2013-2017 Nosto Solutions Ltd
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class NostoHelperController
{

    /**
     * Checks if the given controller is the current one.
     *
     * @param string $name the controller name
     * @return bool true if the given name is the same as the controllers php_self variable, false
     *     otherwise.
     */
    public static function isController($name)
    {
        $result = false;
        // For prestashop 1.5 and 1.6 we can in most cases access the current controllers php_self property.
        if (!empty(Context::getContext()->controller->php_self)) {
            $result = Context::getContext()->controller->php_self === $name;
        } elseif (($controller = Tools::getValue('controller')) !== false) {
            $result = $controller === $name;
        }

        // Fallback when controller cannot be recognised.
        return $result;
    }

    /**
     * Tries to resolve current object by inspecting the underlying controller or checking for an
     * identifier. Assuming that a field called object needs to be accessed, this method will
     * first look if the controller has a method called getObject, if not, it will check if the
     * current request has a parameter called id_object and then use that identifier to instantiate
     * the object
     *
     * @return mixed the resolved object or null
     */
    public static function resolveObject($id_name, $klass, $method)
    {
        $object = null;
        if (method_exists(Context::getContext()->controller, $method)) {
            $object = Context::getContext()->controller->$method();
        }
        if ($object instanceof $klass == false) {
            $id = null;
            if (Tools::getValue($id_name)) {
                $id = Tools::getValue($id_name);
            }
            if ($id) {
                $object = new $klass
                (
                    (int)$id,
                    Context::getContext()->language->id,
                    Context::getContext()->shop->id
                );
            }
        }

        if ($object instanceof $klass === false || !Validate::isLoadedObject($object)) {
            $object = null;
        }

        return $object;
    }
}