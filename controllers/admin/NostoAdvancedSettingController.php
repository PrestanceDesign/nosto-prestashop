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

require_once 'NostoBaseController.php';

class NostoAdvancedSettingController extends NostoBaseController
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        NostoHelperConfig::saveMultiCurrencyMethod(Tools::getValue('multi_currency_method'));
        NostoHelperConfig::saveSkuEnabled((bool)Tools::getValue('nosto_sku_switch'));
        NostoHelperConfig::saveNostoTaggingRenderPosition(Tools::getValue('nostotagging_position'));
        NostoHelperConfig::saveImageType(Tools::getValue('image_type'));
        $account = Nosto::getAccount();
        $account_meta = NostoAccountSignup::loadData($this->context, $this->context->language->id);

        if (!empty($account) && $account->isConnectedToNosto()) {
            try {
                $operation = new NostoSettingsService($account);
                $operation->update($account_meta);
                NostoHelperFlash::add('success', $this->l('The settings have been saved.'));
            } catch (NostoSDKException $e) {
                NostoHelperLogger::error($e, 'Unable to update Nosto account settings');
                NostoHelperFlash::add('error',
                    $this->l('There was an error saving the settings. Please, see log for details.'));
            }

            // Also update the exchange rates if multi currency is used
            if ($account_meta->getUseExchangeRates()) {
                $operation = new NostoRatesService();
                $operation->updateCurrencyExchangeRates($account, $this->context);
            }
        } else {
            //TODO: Flash and log error
        }

        return true;
    }
}