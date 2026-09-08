<?php

class DcShopCompleteAutoloader
{
    private static $map = array (
  'RMAShipmentController' => 
  array (
    0 => '//module/dcshop\\b2c\\RMAShipmentController.php',
  ),
  'campaignConditionTrait' => 
  array (
    0 => '//module/dcshop\\campaigns\\campaignConditionTrait.php',
  ),
  'CampaignDiscountAction' => 
  array (
    0 => '//module/dcshop\\campaigns\\CampaignDiscountAction.php',
  ),
  'CampaignFreeItemAction' => 
  array (
    0 => '//module/dcshop\\campaigns\\CampaignFreeItemAction.php',
  ),
  'CampaignRuleFactory' => 
  array (
    0 => '//module/dcshop\\campaigns\\CampaignRuleFactory.php',
  ),
  'CampaignRuleHelper' => 
  array (
    0 => '//module/dcshop\\campaigns\\CampaignRuleHelper.php',
  ),
  'campaignRulePartTrait' => 
  array (
    0 => '//module/dcshop\\campaigns\\campaignRulePartTrait.php',
  ),
  'CampaignSpecialShippingAction' => 
  array (
    0 => '//module/dcshop\\campaigns\\CampaignSpecialShippingAction.php',
  ),
  'EachItemAmntGTERuleCondition' => 
  array (
    0 => '//module/dcshop\\campaigns\\EachItemAmntGTERuleCondition.php',
  ),
  'EachItemQtyGTERuleCondition' => 
  array (
    0 => '//module/dcshop\\campaigns\\EachItemQtyGTERuleCondition.php',
  ),
  'GenericCampaign' => 
  array (
    0 => '//module/dcshop\\campaigns\\GenericCampaign.php',
  ),
  'GenericCampaignElement' => 
  array (
    0 => '//module/dcshop\\campaigns\\GenericCampaignElement.php',
  ),
  'GenericCampaignElementCollection' => 
  array (
    0 => '//module/dcshop\\campaigns\\GenericCampaignElementCollection.php',
  ),
  'GenericCampaignElementConfig' => 
  array (
    0 => '//module/dcshop\\campaigns\\GenericCampaignElementConfig.php',
  ),
  'GenericCampaignElementRepository' => 
  array (
    0 => '//module/dcshop\\campaigns\\GenericCampaignElementRepository.php',
  ),
  'GenericCampaignHeaderCollection' => 
  array (
    0 => '//module/dcshop\\campaigns\\GenericCampaignHeaderCollection.php',
  ),
  'GenericCampaignHeaderConfig' => 
  array (
    0 => '//module/dcshop\\campaigns\\GenericCampaignHeaderConfig.php',
  ),
  'GenericCampaignHeaderRepository' => 
  array (
    0 => '//module/dcshop\\campaigns\\GenericCampaignHeaderRepository.php',
  ),
  'GenericCampaignRepository' => 
  array (
    0 => '//module/dcshop\\campaigns\\GenericCampaignRepository.php',
  ),
  'NoDiffItemsGTERuleCondition' => 
  array (
    0 => '//module/dcshop\\campaigns\\NoDiffItemsGTERuleCondition.php',
  ),
  'SingleItemAmntGTERuleCondition' => 
  array (
    0 => '//module/dcshop\\campaigns\\SingleItemAmntGTERuleCondition.php',
  ),
  'SingleItemQtyGTERuleCondition' => 
  array (
    0 => '//module/dcshop\\campaigns\\SingleItemQtyGTERuleCondition.php',
  ),
  'SumAmntAllItemsGTERuleCondition' => 
  array (
    0 => '//module/dcshop\\campaigns\\SumAmntAllItemsGTERuleCondition.php',
  ),
  'SumBasketAmntGTERuleCondition' => 
  array (
    0 => '//module/dcshop\\campaigns\\SumBasketAmntGTERuleCondition.php',
  ),
  'SumQtyItemRuleCondition' => 
  array (
    0 => '//module/dcshop\\campaigns\\SumQtyItemRuleCondition.php',
  ),
  'CustomerDecorator' => 
  array (
    0 => '//module/dcshop\\common\\abstracts\\CustomerDecorator.php',
  ),
  'DiscountBase' => 
  array (
    0 => '//module/dcshop\\common\\abstracts\\DiscountBase.php',
  ),
  'ItemPriceDataDecorator' => 
  array (
    0 => '//module/dcshop\\common\\abstracts\\ItemPriceDataDecorator.php',
  ),
  'WebshopItemDecorator' => 
  array (
    0 => '//module/dcshop\\common\\abstracts\\WebshopItemDecorator.php',
  ),
  'AddressFormBuilder' => 
  array (
    0 => '//module/dcshop\\common\\classes\\AddressFormBuilder.php',
  ),
  'AdvancedPriceProvider' => 
  array (
    0 => '//module/dcshop\\common\\classes\\AdvancedPriceProvider.php',
  ),
  'AppliedDiscount' => 
  array (
    0 => '//module/dcshop\\common\\classes\\AppliedDiscount.php',
  ),
  'B2BStdPriceStrategy' => 
  array (
    0 => '//module/dcshop\\common\\classes\\B2BStdPriceStrategy.php',
  ),
  'B2C4StepOrderHeaderTemplate' => 
  array (
    0 => '//module/dcshop\\common\\classes\\B2C4StepOrderHeaderTemplate.php',
  ),
  'B2C4StepOrderHeaderViewModel' => 
  array (
    0 => '//module/dcshop\\common\\classes\\B2C4StepOrderHeaderViewModel.php',
  ),
  'B2C4StepOrderStep1InfoContentLeftTemplate' => 
  array (
    0 => '//module/dcshop\\common\\classes\\B2C4StepOrderStep1InfoContentLeftTemplate.php',
  ),
  'B2C4StepOrderStep1InfoContentLeftViewModel' => 
  array (
    0 => '//module/dcshop\\common\\classes\\B2C4StepOrderStep1InfoContentLeftViewModel.php',
  ),
  'B2CStep1OrderTemplate' => 
  array (
    0 => '//module/dcshop\\common\\classes\\B2CStep1OrderTemplate.php',
  ),
  'B2CUserOrderStep1ViewModel' => 
  array (
    0 => '//module/dcshop\\common\\classes\\B2CUserOrderStep1ViewModel.php',
  ),
  'BasicPriceProvider' => 
  array (
    0 => '//module/dcshop\\common\\classes\\BasicPriceProvider.php',
  ),
  'BasketEntity' => 
  array (
    0 => '//module/dcshop\\common\\classes\\BasketEntity.php',
  ),
  'BasketValueSource' => 
  array (
    0 => '//module/dcshop\\common\\classes\\BasketValueSource.php',
  ),
  'Category' => 
  array (
    0 => '//module/dcshop\\common\\classes\\Category.php',
  ),
  'CategoryCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CategoryCollection.php',
  ),
  'CategoryConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CategoryConfig.php',
  ),
  'CategoryRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CategoryRepository.php',
  ),
  'ComputopPaygateWrapper' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ComputopPaygateWrapper.php',
  ),
  'Country' => 
  array (
    0 => '//module/dcshop\\common\\classes\\Country.php',
  ),
  'CountryCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CountryCollection.php',
  ),
  'CountryConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CountryConfig.php',
  ),
  'CountryRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CountryRepository.php',
  ),
  'CouponApplicator' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CouponApplicator.php',
  ),
  'CouponHeader' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CouponHeader.php',
  ),
  'CouponHeaderCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CouponHeaderCollection.php',
  ),
  'CouponHeaderConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CouponHeaderConfig.php',
  ),
  'CouponHeaderRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CouponHeaderRepository.php',
  ),
  'CouponLine' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CouponLine.php',
  ),
  'CouponLineCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CouponLineCollection.php',
  ),
  'CouponLineConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CouponLineConfig.php',
  ),
  'CouponLineRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CouponLineRepository.php',
  ),
  'CurrShopConfiguration' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CurrShopConfiguration.php',
  ),
  'Customer' => 
  array (
    0 => '//module/dcshop\\common\\classes\\Customer.php',
  ),
  'CustomerCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CustomerCollection.php',
  ),
  'CustomerConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CustomerConfig.php',
  ),
  'CustomerPermissionGroupDecorator' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CustomerPermissionGroupDecorator.php',
  ),
  'CustomerPseudoPayData' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CustomerPseudoPayData.php',
  ),
  'CustomerPseudoPayDataCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CustomerPseudoPayDataCollection.php',
  ),
  'CustomerPseudoPayDataConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CustomerPseudoPayDataConfig.php',
  ),
  'CustomerPseudoPayDataRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CustomerPseudoPayDataRepository.php',
  ),
  'CustomerRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\CustomerRepository.php',
  ),
  'DefaultItemAvailabilityProvider' => 
  array (
    0 => '//module/dcshop\\common\\classes\\DefaultItemAvailabilityProvider.php',
  ),
  'DefaultOrder' => 
  array (
    0 => '//module/dcshop\\common\\classes\\DefaultOrder.php',
  ),
  'GenericInvoiceDiscount' => 
  array (
    0 => '//module/dcshop\\common\\classes\\GenericInvoiceDiscount.php',
  ),
  'GenericLineDiscount' => 
  array (
    0 => '//module/dcshop\\common\\classes\\GenericLineDiscount.php',
  ),
  'GenericUserBasket' => 
  array (
    0 => '//module/dcshop\\common\\classes\\GenericUserBasket.php',
  ),
  'GenericUserItemPermissionProvider' => 
  array (
    0 => '//module/dcshop\\common\\classes\\GenericUserItemPermissionProvider.php',
  ),
  'GenericViewModel' => 
  array (
    0 => '//module/dcshop\\common\\classes\\GenericViewModel.php',
  ),
  'GraduatedItemPriceData' => 
  array (
    0 => '//module/dcshop\\common\\classes\\GraduatedItemPriceData.php',
  ),
  'InventoryStrategyFactory' => 
  array (
    0 => '//module/dcshop\\common\\classes\\InventoryStrategyFactory.php',
  ),
  'InvoiceCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\InvoiceCollection.php',
  ),
  'InvoiceConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\InvoiceConfig.php',
  ),
  'InvoiceDiscount' => 
  array (
    0 => '//module/dcshop\\common\\classes\\InvoiceDiscount.php',
  ),
  'InvoiceDiscountCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\InvoiceDiscountCollection.php',
  ),
  'InvoiceDiscountConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\InvoiceDiscountConfig.php',
  ),
  'InvoiceDiscountRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\InvoiceDiscountRepository.php',
  ),
  'InvoiceDocument' => 
  array (
    0 => '//module/dcshop\\common\\classes\\InvoiceDocument.php',
  ),
  'InvoiceLine' => 
  array (
    0 => '//module/dcshop\\common\\classes\\InvoiceLine.php',
  ),
  'InvoiceLineCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\InvoiceLineCollection.php',
  ),
  'InvoiceLineConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\InvoiceLineConfig.php',
  ),
  'InvoiceLineRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\InvoiceLineRepository.php',
  ),
  'InvoiceRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\InvoiceRepository.php',
  ),
  'ItemOrderbuttonbuilder' =>
  array (
    0 => '//module/dcshop\\common\\classes\\ItemOrderbuttonbuilder.php',
  ),
  'ItemInventoryProvider' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ItemInventoryProvider.php',
  ),
  'ItemPriceData' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ItemPriceData.php',
  ),
  'NavOrderCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\NavOrderCollection.php',
  ),
  'NavOrderConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\NavOrderConfig.php',
  ),
  'NavOrderDocument' => 
  array (
    0 => '//module/dcshop\\common\\classes\\NavOrderDocument.php',
  ),
  'NavOrderLine' => 
  array (
    0 => '//module/dcshop\\common\\classes\\NavOrderLine.php',
  ),
  'NavOrderLineCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\NavOrderLineCollection.php',
  ),
  'NavOrderLineConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\NavOrderLineConfig.php',
  ),
  'NavOrderLineRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\NavOrderLineRepository.php',
  ),
  'NavOrderRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\NavOrderRepository.php',
  ),
  'NAVVariantsInventoryStrategy' => 
  array (
    0 => '//module/dcshop\\common\\classes\\NAVVariantsInventoryStrategy.php',
  ),
  'OldBasketDeletetionService' => 
  array (
    0 => '//module/dcshop\\common\\classes\\OldBasketDeletetionService.php',
  ),
  'OrderCouponDTO' => 
  array (
    0 => '//module/dcshop\\common\\classes\\OrderCouponDTO.php',
  ),
  'OrderItem' => 
  array (
    0 => '//module/dcshop\\common\\classes\\OrderItem.php',
  ),
  'OrderItemCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\OrderItemCollection.php',
  ),
  'OrderPayData' => 
  array (
    0 => '//module/dcshop\\common\\classes\\OrderPayData.php',
  ),
  'PaymentOption' => 
  array (
    0 => '//module/dcshop\\common\\classes\\PaymentOption.php',
  ),
  'PaymentOptionCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\PaymentOptionCollection.php',
  ),
  'PaymentOptionConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\PaymentOptionConfig.php',
  ),
  'PaymentOptionRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\PaymentOptionRepository.php',
  ),
  'PriceProvider' => 
  array (
    0 => '//module/dcshop\\common\\classes\\PriceProvider.php',
  ),
  'PriceStrategyBase' => 
  array (
    0 => '//module/dcshop\\common\\classes\\PriceStrategyBase.php',
  ),
  'RelatedDocumentFinderService' => 
  array (
    0 => '//module/dcshop\\common\\classes\\RelatedDocumentFinderService.php',
  ),
  'SalesPrice' => 
  array (
    0 => '//module/dcshop\\common\\classes\\SalesPrice.php',
  ),
  'SalesPriceCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\SalesPriceCollection.php',
  ),
  'SalesPriceConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\SalesPriceConfig.php',
  ),
  'SalesPriceRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\SalesPriceRepository.php',
  ),
  'SelectionCriteriaValidationService' => 
  array (
    0 => '//module/dcshop\\common\\classes\\SelectionCriteriaValidationService.php',
  ),
  'ShipmentAddress' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShipmentAddress.php',
  ),
  'ShipmentAddressCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShipmentAddressCollection.php',
  ),
  'ShipmentAddressConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShipmentAddressConfig.php',
  ),
  'ShipmentAddressRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShipmentAddressRepository.php',
  ),
  'ShipmentCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShipmentCollection.php',
  ),
  'ShipmentConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShipmentConfig.php',
  ),
  'ShipmentDocument' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShipmentDocument.php',
  ),
  'ShipmentLine' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShipmentLine.php',
  ),
  'ShipmentLineCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShipmentLineCollection.php',
  ),
  'ShipmentLineConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShipmentLineConfig.php',
  ),
  'ShipmentLineRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShipmentLineRepository.php',
  ),
  'ShipmentRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShipmentRepository.php',
  ),
  'ShippingOption' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShippingOption.php',
  ),
  'ShippingOptionCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShippingOptionCollection.php',
  ),
  'ShippingOptionConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShippingOptionConfig.php',
  ),
  'ShippingOptionRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShippingOptionRepository.php',
  ),
  'Shop' => 
  array (
    0 => '//module/dcshop\\common\\classes\\Shop.php',
  ),
  'ShopCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShopCollection.php',
  ),
  'ShopConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShopConfig.php',
  ),
  'ShopLanguage' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShopLanguage.php',
  ),
  'ShopLanguageCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShopLanguageCollection.php',
  ),
  'ShopLanguageConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShopLanguageConfig.php',
  ),
  'ShopLanguageRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShopLanguageRepository.php',
  ),
  'ShopRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\ShopRepository.php',
  ),
  'SimplePriceStrategy' => 
  array (
    0 => '//module/dcshop\\common\\classes\\SimplePriceStrategy.php',
  ),
  'StdOrderFrontController' => 
  array (
    0 => '//module/dcshop\\common\\classes\\StdOrderFrontController.php',
  ),
  'StdOrderHelper' => 
  array (
    0 => '//module/dcshop\\common\\classes\\StdOrderHelper.php',
  ),
  'TextModule' => 
  array (
    0 => '//module/dcshop\\common\\classes\\TextModule.php',
  ),
  'TextModuleCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\TextModuleCollection.php',
  ),
  'TextModuleConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\TextModuleConfig.php',
  ),
  'TextModuleRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\TextModuleRepository.php',
  ),
  'User' => 
  array (
    0 => '//module/dcshop\\common\\classes\\User.php',
  ),
  'UserBasketListener' => 
  array (
    0 => '//module/dcshop\\common\\classes\\UserBasketListener.php',
  ),
  'UserBasketLoginHandler' => 
  array (
    0 => '//module/dcshop\\common\\classes\\UserBasketLoginHandler.php',
  ),
  'UserBasketPersistenceHandler' => 
  array (
    0 => '//module/dcshop\\common\\classes\\UserBasketPersistenceHandler.php',
  ),
  'UserBasketRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\UserBasketRepository.php',
  ),
  'UserCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\UserCollection.php',
  ),
  'UserConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\UserConfig.php',
  ),
  'UserRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\UserRepository.php',
  ),
  'VATManager' => 
  array (
    0 => '//module/dcshop\\common\\classes\\VATManager.php',
  ),
  'WebshopItem' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItem.php',
  ),
  'WebshopItemBuilder' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemBuilder.php',
  ),
  'WebshopItemCategoryDecorator' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemCategoryDecorator.php',
  ),
  'WebshopItemCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemCollection.php',
  ),
  'WebshopItemConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemConfig.php',
  ),
  'WebshopItemNAVVariantDecorator' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemNAVVariantDecorator.php',
  ),
  'WebshopItemOrderableEntityDecorator' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemOrderableEntityDecorator.php',
  ),
  'WebshopItemRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemRepository.php',
  ),
  'WebshopItemVariant' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemVariant.php',
  ),
  'WebshopItemVariantCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemVariantCollection.php',
  ),
  'WebshopItemVariantConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemVariantConfig.php',
  ),
  'WebshopItemVariantDecorator' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemVariantDecorator.php',
  ),
  'WebshopItemVariantRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemVariantRepository.php',
  ),
  'WebshopItemVariantService' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemVariantService.php',
  ),
  'WebshopItemWithImagesDecorator' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopItemWithImagesDecorator.php',
  ),
  'WebshopOrderCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopOrderCollection.php',
  ),
  'WebshopOrderConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopOrderConfig.php',
  ),
  'WebshopOrderDocument' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopOrderDocument.php',
  ),
  'WebshopOrderLine' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopOrderLine.php',
  ),
  'WebshopOrderLineCollection' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopOrderLineCollection.php',
  ),
  'WebshopOrderLineConfig' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopOrderLineConfig.php',
  ),
  'WebshopOrderLineRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopOrderLineRepository.php',
  ),
  'WebshopOrderRepository' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopOrderRepository.php',
  ),
  'WebshopVariantsInventoryStrategy' => 
  array (
    0 => '//module/dcshop\\common\\classes\\WebshopVariantsInventoryStrategy.php',
  ),
  'CustomerInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\CustomerInterface.php',
  ),
  'CustomerWithPermissionGroupsInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\CustomerWithPermissionGroupsInterface.php',
  ),
  'Discount' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\Discount.php',
  ),
  'DocLineModelDBConfigInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\DocLineModelDBConfigInterface.php',
  ),
  'DocLineRepositoryInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\DocLineRepositoryInterface.php',
  ),
  'DocumentLineRepository' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\DocumentLineRepository.php',
  ),
  'DocumentModelDBConfigInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\DocumentModelDBConfigInterface.php',
  ),
  'DocumentRepository' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\DocumentRepository.php',
  ),
  'DocumentRepositoryInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\DocumentRepositoryInterface.php',
  ),
  'DynCom\dc\common\interfaces\GenericCollectionInterface' =>
  array (
    0 => '//module/dcshop\\common\\interfaces\\DynCom\dc\common\interfaces\GenericCollectionInterface.php',
  ),
  'GenericDocLineInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\GenericDocLineInterface.php',
  ),
  'GenericDocumentInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\GenericDocumentInterface.php',
  ),
  'GenericRepositoryInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\GenericRepositoryInterface.php',
  ),
  'HasItemConfigInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\HasItemConfigInterface.php',
  ),
  'IHasVATProdPostingGroup' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\IHasVATProdPostingGroup.php',
  ),
  'IPriceProvider' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\IPriceProvider.php',
  ),
  'IPriceProviderStrategy' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\IPriceProviderStrategy.php',
  ),
  'ItemAvailabilityProvider' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\ItemAvailabilityProvider.php',
  ),
  'ItemAvailabilityStrategyInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\ItemAvailabilityStrategyInterface.php',
  ),
  'ItemInventoryStrategyInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\ItemInventoryStrategyInterface.php',
  ),
  'ItemPriceDataInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\ItemPriceDataInterface.php',
  ),
  'IVATManager' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\IVATManager.php',
  ),
  'DynCom\dc\common\interfaces\ModelDBConfigInterface' =>
  array (
    0 => '//module/dcshop\\common\\interfaces\\DynCom\dc\common\interfaces\ModelDBConfigInterface.php',
  ),
  'Order' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\Order.php',
  ),
  'OrderableEntityInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\OrderableEntityInterface.php',
  ),
  'OrderItemInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\OrderItemInterface.php',
  ),
  'OrderStep' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\OrderStep.php',
  ),
  'TemplatingInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\TemplatingInterface.php',
  ),
  'TextProviderInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\TextProviderInterface.php',
  ),
  'UserBasket' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\UserBasket.php',
  ),
  'UserItemPermissionProvider' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\UserItemPermissionProvider.php',
  ),
  'WebshopItemInterface' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\WebshopItemInterface.php',
  ),
  'WebshopItemWithCategories' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\WebshopItemWithCategories.php',
  ),
  'WebshopItemWithImages' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\WebshopItemWithImages.php',
  ),
  'WebshopItemWithVariants' => 
  array (
    0 => '//module/dcshop\\common\\interfaces\\WebshopItemWithVariants.php',
  ),
  'docLineRepositoryTrait' => 
  array (
    0 => '//module/dcshop\\common\\traits\\docLineRepositoryTrait.php',
  ),
  'documentConfigTrait' => 
  array (
    0 => '//module/dcshop\\common\\traits\\documentConfigTrait.php',
  ),
  'documentRepositoryTrait' => 
  array (
    0 => '//module/dcshop\\common\\traits\\documentRepositoryTrait.php',
  ),
  'documentTrait' => 
  array (
    0 => '//module/dcshop\\common\\traits\\documentTrait.php',
  ),
  'genericDecoratorTrait' => 
  array (
    0 => '//module/dcshop\\common\\traits\\genericDecoratorTrait.php',
  ),
  'genericDocLineTrait' => 
  array (
    0 => '//module/dcshop\\common\\traits\\genericDocLineTrait.php',
  ),
  'hasItemTrait' => 
  array (
    0 => '//module/dcshop\\common\\traits\\hasItemTrait.php',
  ),
  'PomSoapClient' => 
  array (
    0 => '//module/dcshop\\newsletter_copernica\\soapclient.php',
  ),
  'RetShipmentCollection' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RetShipmentCollection.php',
  ),
  'RetShipmentConfig' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RetShipmentConfig.php',
  ),
  'RetShipmentDocument' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RetShipmentDocument.php',
  ),
  'RetShipmentFinderService' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RetShipmentFinderService.php',
  ),
  'RetShipmentLine' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RetShipmentLine.php',
  ),
  'RetShipmentLineCollection' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RetShipmentLineCollection.php',
  ),
  'RetShipmentLineConfig' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RetShipmentLineConfig.php',
  ),
  'RetShipmentLineRepository' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RetShipmentLineRepository.php',
  ),
  'RetShipmentRepository' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RetShipmentRepository.php',
  ),
  'RetShipmentView' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RetShipmentView.php',
  ),
  'ReturnReason' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\ReturnReason.php',
  ),
  'ReturnReasonCollection' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\ReturnReasonCollection.php',
  ),
  'ReturnReasonConfig' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\ReturnReasonConfig.php',
  ),
  'ReturnReasonRepository' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\ReturnReasonRepository.php',
  ),
  'RMAConfirmationMailPHTMLTemplate' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RMAConfirmationMailPHTMLTemplate.php',
  ),
  'RMAConfirmationMailViewModel' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RMAConfirmationMailViewModel.php',
  ),
  'RMAFrontController' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RMAFrontController.php',
  ),
  'RMAGenericRMAPageViewModel' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RMAGenericRMAPageViewModel.php',
  ),
  'RMAGenericViewModel' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RMAGenericViewModel.php',
  ),
  'RMAGenericViewPHTMLTemplate' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RMAGenericViewPHTMLTemplate.php',
  ),
  'RMAOrderHelper' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RMAOrderHelper.php',
  ),
  'RMASearchController' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RMASearchController.php',
  ),
  'RMASearchPHTMLTemplate' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RMASearchPHTMLTemplate.php',
  ),
  'RMASearchViewModel' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RMASearchViewModel.php',
  ),
  'RMAShipmentViewModel' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RMAShipmentViewModel.php',
  ),
  'RMAShipmentViewPHTMLTemplate' => 
  array (
    0 => '//module/dcshop\\rma\\classes\\RMAShipmentViewPHTMLTemplate.php',
  ),
  'ItemSubscriptionData' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\ItemSubscriptionData.php',
  ),
  'ItemSubscriptionDataBuilder' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\ItemSubscriptionDataBuilder.php',
  ),
  'SubscriptionCustomerLink' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionCustomerLink.php',
  ),
  'SubscriptionCustomerLinkCollection' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionCustomerLinkCollection.php',
  ),
  'SubscriptionCustomerLinkConfig' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionCustomerLinkConfig.php',
  ),
  'SubscriptionCustomerLinkRepository' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionCustomerLinkRepository.php',
  ),
  'SubscriptionDateCalculator' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionDateCalculator.php',
  ),
  'SubscriptionHeader' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionHeader.php',
  ),
  'SubscriptionHeaderCollection' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionHeaderCollection.php',
  ),
  'SubscriptionHeaderConfig' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionHeaderConfig.php',
  ),
  'SubscriptionHeaderRepository' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionHeaderRepository.php',
  ),
  'SubscriptionItemcardButtonViewModel' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionItemcardButtonViewModel.php',
  ),
  'SubscriptionItemcardButtonViewPHTMLTemplate' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionItemcardButtonViewTemplate.php',
  ),
  'SubscriptionItemDecorator' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionItemDecorator.php',
  ),
  'SubscriptionItemLink' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionItemLink.php',
  ),
  'SubscriptionItemLinkCollection' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionItemLinkCollection.php',
  ),
  'SubscriptionItemLinkConfig' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionItemLinkConfig.php',
  ),
  'SubscriptionItemLinkRepository' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionItemLinkRepository.php',
  ),
  'SubscriptionItemPriceDecorator' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionItemPriceDecorator.php',
  ),
  'SubscriptionOrderButtonFormFactory' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionOrderButtonFormFactory.php',
  ),
  'SubscriptionRepository' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionRepository.php',
  ),
  'SubscriptionRequest' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionRequest.php',
  ),
  'SubscriptionSequenceStep' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionSequenceStep.php',
  ),
  'SubscriptionSequenceStepCollection' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionSequenceStepCollection.php',
  ),
  'SubscriptionSequenceStepConfig' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionSequenceStepConfig.php',
  ),
  'SubscriptionSequenceStepRepository' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionSequenceStepRepository.php',
  ),
  'SubscriptionsFrontController' => 
  array (
    0 => '//module/dcshop\\subscriptions\\classes\\SubscriptionsFrontController.php',
  ),
  'SubscriptionItemPriceDataInterface' => 
  array (
    0 => '//module/dcshop\\subscriptions\\interfaces\\SubscriptionItemPriceDataInterface.php',
  ),
  'TemplateInserterBase' => 
  array (
    0 => '//dc\\common\\abstracts\\TemplateInserterBase.php',
  ),
  'URLBase' => 
  array (
    0 => '//dc\\common\\abstracts\\URLBase.php',
  ),
  'Address' => 
  array (
    0 => '//dc\\common\\classes\\Address.php',
  ),
  'AddressCollection' => 
  array (
    0 => '//dc\\common\\classes\\AddressCollection.php',
  ),
  'AddressConfig' => 
  array (
    0 => '//dc\\common\\classes\\AddressConfig.php',
  ),
  'Adminmenu' => 
  array (
    0 => '//dc\\common\\classes\\Adminmenu.php',
  ),
  'BasicControlFlowHandler' => 
  array (
    0 => '//dc\\common\\classes\\BasicControlFlowHandler.php',
  ),
  'CollectionTypes' => 
  array (
    0 => '//dc\\common\\classes\\CollectionTypes.php',
  ),
  'DOMNodeView' => 
  array (
    0 => '//dc\\common\\classes\\DOMNodeView.php',
  ),
  'DOMTest' => 
  array (
    0 => '//dc\\common\\classes\\DOMTemplateTest.php',
  ),
  'DOMViewModelTest' => 
  array (
    0 => '//dc\\common\\classes\\DOMViewModelTest.php',
  ),
  'EMail' => 
  array (
    0 => '//dc\\common\\classes\\EMail.php',
  ),
  'Encryption' => 
  array (
    0 => '//dc\\common\\classes\\Encryption.php',
  ),
  'FlattenedGettableDecorator' => 
  array (
    0 => '//dc\\common\\classes\\FlattenedGettableDecorator.php',
  ),
  'Form' => 
  array (
    0 => '//dc\\common\\classes\\Form.php',
  ),
  'FormBuilder' => 
  array (
    0 => '//dc\\common\\classes\\FormBuilder.php',
  ),
  'FormElement' => 
  array (
    0 => '//dc\\common\\classes\\FormElement.php',
  ),
  'GenericCRUDObjectStorageUnitOfWork' => 
  array (
    0 => '//dc\\common\\classes\\GenericCRUDObjectStorageUnitOfWork.php',
  ),
  'GenericDOMRepositoryListView' => 
  array (
    0 => '//dc\\common\\classes\\GenericDOMRepositoryListView.php',
  ),
  'GenericDOMView' => 
  array (
    0 => '//dc\\common\\classes\\GenericDOMView.php',
  ),
  'GenericObjectStorage' => 
  array (
    0 => '//dc\\common\\classes\\GenericObjectStorage.php',
  ),
  'GenericPHTMLRepositoryListView' => 
  array (
    0 => '//dc\\common\\classes\\GenericPHTMLRepositoryListView.php',
  ),
  'GenericPHTMLView' => 
  array (
    0 => '//dc\\common\\classes\\GenericPHTMLView.php',
  ),
  'GenericPlainRepositoryListView' => 
  array (
    0 => '//dc\\common\\classes\\GenericPlainRepositoryListView.php',
  ),
  'GenericPlainView' => 
  array (
    0 => '//dc\\common\\classes\\GenericPlainView.php',
  ),
  'GenericServicePayload' => 
  array (
    0 => '//dc\\common\\classes\\GenericServicePayload.php',
  ),
  'GenericServicePayloadBuilder' => 
  array (
    0 => '//dc\\common\\classes\\GenericServicePayloadBuilder.php',
  ),
  'GenericView' => 
  array (
    0 => '//dc\\common\\classes\\GenericView.php',
  ),
  'Hook' => 
  array (
    0 => '//dc\\common\\classes\\Hook.php',
  ),
  'HTMLSnippetProvider' => 
  array (
    0 => '//dc\\common\\classes\\HTMLSnippetProvider.php',
  ),
  'Language' => 
  array (
    0 => '//dc\\common\\classes\\Language.php',
  ),
  'LanguageCollection' => 
  array (
    0 => '//dc\\common\\classes\\LanguageCollection.php',
  ),
  'LanguageConfig' => 
  array (
    0 => '//dc\\common\\classes\\LanguageConfig.php',
  ),
  'LanguageRepository' => 
  array (
    0 => '//dc\\common\\classes\\LanguageRepository.php',
  ),
  'MustacheTemplateEngine' => 
  array (
    0 => '//dc\\common\\classes\\MustacheTemplateEngine.php',
  ),
  'MySQLiQueryWrapper' => 
  array (
    0 => '//dc\\common\\classes\\MySQLiQueryWrapper.php',
  ),
  'NAVDateFormulaManagement' => 
  array (
    0 => '//dc\\common\\classes\\NAVDateFormulaManagement.php',
  ),
  'NewValidator' => 
  array (
    0 => '//dc\\common\\classes\\NewValidator.php',
  ),
  'PDOQueryWrapper' => 
  array (
    0 => '//dc\\common\\classes\\PDOQueryWrapper.php',
  ),
  'Registry' => 
  array (
    0 => '//dc\\common\\classes\\Registry.php',
  ),
  'RememberMeHandlerService' => 
  array (
    0 => '//dc\\common\\classes\\RememberMeHandlerService.php',
  ),
  'RenderableElementResolver' => 
  array (
    0 => '//dc\\common\\classes\\RenderableElementResolver.php',
  ),
  'RenderableStringTransformer' => 
  array (
    0 => '//dc\\common\\classes\\RenderableStringTransformer.php',
  ),
  'SalutationOptions' => 
  array (
    0 => '//dc\\common\\classes\\SalutationOptions.php',
  ),
  'SelectionCriteriaHelper' => 
  array (
    0 => '//dc\\common\\classes\\SelectionCriteriaHelper.php',
  ),
  'ServiceRequestID' => 
  array (
    0 => '//dc\\common\\classes\\ServiceRequestID.php',
  ),
  'SessionHandlerService' => 
  array (
    0 => '//dc\\common\\classes\\SessionHandlerService.php',
  ),
  'SessionStorage' => 
  array (
    0 => '//dc\\common\\classes\\SessionStorage.php',
  ),
  'Site' => 
  array (
    0 => '//dc\\common\\classes\\Site.php',
  ),
  'SiteCollection' => 
  array (
    0 => '//dc\\common\\classes\\SiteCollection.php',
  ),
  'SiteConfig' => 
  array (
    0 => '//dc\\common\\classes\\SiteConfig.php',
  ),
  'Siteparts' => 
  array (
    0 => '//dc\\common\\classes\\Siteparts.php',
  ),
  'SiteRepository' => 
  array (
    0 => '//dc\\common\\classes\\SiteRepository.php',
  ),
  'StdMD5PasswordCheckerStrategy' => 
  array (
    0 => '//dc\\common\\classes\\StdMD5PasswordCheckerStrategy.php',
  ),
  'TemplateDefaultInserter' => 
  array (
    0 => '//dc\\common\\classes\\TemplateDefaultInserter.php',
  ),
  'TemplateDOMInserter' => 
  array (
    0 => '//dc\\common\\classes\\TemplateDOMInserter.php',
  ),
  'TemplateInserter' => 
  array (
    0 => '//dc\\common\\classes\\TemplateInserter.php',
  ),
  'TemplateInserterFactory' => 
  array (
    0 => '//dc\\common\\classes\\TemplateInserterFactory.php',
  ),
  'TemplateStringInserter' => 
  array (
    0 => '//dc\\common\\classes\\TemplateStringInserter.php',
  ),
  'Templating' => 
  array (
    0 => '//dc\\common\\classes\\Templating.php',
  ),
  'Translate' => 
  array (
    0 => '//dc\\common\\classes\\Translate.php',
  ),
  'UnitOfWork' => 
  array (
    0 => '//dc\\common\\classes\\UnitOfWork.php',
  ),
  'URL' => 
  array (
    0 => '//dc\\common\\classes\\URL.php',
  ),
  'URLMaker' => 
  array (
    0 => '//dc\\common\\classes\\URLMaker.php',
  ),
  'Validator' => 
  array (
    0 => '//dc\\common\\classes\\Validator.php',
  ),
  'ViewFactory' => 
  array (
    0 => '//dc\\common\\classes\\ViewFactory.php',
  ),
  'Visitor' => 
  array (
    0 => '//dc\\common\\classes\\Visitor.php',
  ),
  'VisitorCollection' => 
  array (
    0 => '//dc\\common\\classes\\VisitorCollection.php',
  ),
  'VisitorConfig' => 
  array (
    0 => '//dc\\common\\classes\\VisitorConfig.php',
  ),
  'VisitorRepository' => 
  array (
    0 => '//dc\\common\\classes\\VisitorRepository.php',
  ),
  'ArrayGettable' => 
  array (
    0 => '//dc\\common\\interfaces\\ArrayGettable.php',
  ),
  'CriteriaHelperInterface' => 
  array (
    0 => '//dc\\common\\interfaces\\CriteriaHelperInterface.php',
  ),
  'CRUDObjectStorage' => 
  array (
    0 => '//dc\\common\\interfaces\\CRUDObjectStorage.php',
  ),
  'DOMRepositoryListView' => 
  array (
    0 => '//dc\\common\\interfaces\\DOMRepositoryListView.php',
  ),
  'DOMTemplate' => 
  array (
    0 => '//dc\\common\\interfaces\\DOMTemplate.php',
  ),
  'DOMView' => 
  array (
    0 => '//dc\\common\\interfaces\\DOMView.php',
  ),
  'Entity' => 
  array (
    0 => '//dc\\common\\interfaces\\Entity.php',
  ),
  'FlattenedGettable' => 
  array (
    0 => '//dc\\common\\interfaces\\FlattenedGettable.php',
  ),
  'GenericDBModelInterface' => 
  array (
    0 => '//dc\\common\\interfaces\\GenericDBModelInterface.php',
  ),
  'GenericDBQueryWrapperInterface' => 
  array (
    0 => '//dc\\common\\interfaces\\GenericDBQueryWrapperInterface.php',
  ),
  'GenericObjectStorageInterface' => 
  array (
    0 => '//dc\\common\\interfaces\\GenericObjectStorageInterface.php',
  ),
  'GenericTemplateInserterInterface' => 
  array (
    0 => '//dc\\common\\interfaces\\GenericTemplateInserterInterface.php',
  ),
  'GenericViewInterface' => 
  array (
    0 => '//dc\\common\\interfaces\\GenericViewInterface.php',
  ),
  'IOCInterface' => 
  array (
    0 => '//dc\\common\\interfaces\\IOCInterface.php',
  ),
  'Observer' => 
  array (
    0 => '//dc\\common\\interfaces\\Observer.php',
  ),
  'PageableViewInterface' => 
  array (
    0 => '//dc\\common\\interfaces\\PageableViewInterface.php',
  ),
  'PasswordCheckerInterface' => 
  array (
    0 => '//dc\\common\\interfaces\\PasswordCheckerInterface.php',
  ),
  'PHTMLRepositoryListView' => 
  array (
    0 => '//dc\\common\\interfaces\\PHTMLRepositoryListView.php',
  ),
  'PHTMLTemplate' => 
  array (
    0 => '//dc\\common\\interfaces\\PHTMLTemplate.php',
  ),
  'PHTMLView' => 
  array (
    0 => '//dc\\common\\interfaces\\PHTMLView.php',
  ),
  'PlainRepositoryListView' => 
  array (
    0 => '//dc\\common\\interfaces\\PlainRepositoryListView.php',
  ),
  'PlainTemplate' => 
  array (
    0 => '//dc\\common\\interfaces\\PlainTemplate.php',
  ),
  'PlainView' => 
  array (
    0 => '//dc\\common\\interfaces\\PlainView.php',
  ),
  'Repository' => 
  array (
    0 => '//dc\\common\\interfaces\\Repository.php',
  ),
  'RepositoryListView' => 
  array (
    0 => '//dc\\common\\interfaces\\RepositoryListView.php',
  ),
  'RepositoryUnitOfWorkInterface' => 
  array (
    0 => '//dc\\common\\interfaces\\RepositoryUnitOfWorkInterface.php',
  ),
  'ServicePayload' => 
  array (
    0 => '//dc\\common\\interfaces\\ServicePayload.php',
  ),
  'Template' => 
  array (
    0 => '//dc\\common\\interfaces\\Template.php',
  ),
  'TemplateEngine' => 
  array (
    0 => '//dc\\common\\interfaces\\TemplateEngine.php',
  ),
  'UnitOfWorkInterface' => 
  array (
    0 => '//dc\\common\\interfaces\\UnitOfWorkInterface.php',
  ),
  'View' => 
  array (
    0 => '//dc\\common\\interfaces\\View.php',
  ),
  'ViewModel' => 
  array (
    0 => '//dc\\common\\interfaces\\ViewModel.php',
  ),
  'log_mysql' => 
  array (
    0 => '//dc\\common\\logparser\\class.log.mysql.php',
  ),
  'log_output' => 
  array (
    0 => '//dc\\common\\logparser\\class.log.output.php',
  ),
  'log' => 
  array (
    0 => '//dc\\common\\logparser\\class.log.php',
  ),
  'log_processor' => 
  array (
    0 => '//dc\\common\\logparser\\class.log.processor.php',
  ),
  'arrayGettableTrait' => 
  array (
    0 => '//dc\\common\\traits\\arrayGettableTrait.php',
  ),
  'arrayMappableTrait' => 
  array (
    0 => '//dc\\common\\traits\\arrayMappableTrait.php',
  ),
  'directoryFileWriterTrait' => 
  array (
    0 => '//dc\\common\\traits\\directoryFileWriterTrait.php',
  ),
  'flattenedGettableTrait' => 
  array (
    0 => '//dc\\common\\traits\\flattenedGettableTrait.php',
  ),
  'genericCollectionTrait' => 
  array (
    0 => '//dc\\common\\traits\\genericCollectionTrait.php',
  ),
  'genericConfigTrait' => 
  array (
    0 => '//dc\\common\\traits\\genericConfigTrait.php',
  ),
  'genericDBModelTrait' => 
  array (
    0 => '//dc\\common\\traits\\genericDBModelTrait.php',
  ),
  'genericPlainTemplateTrait' => 
  array (
    0 => '//dc\\common\\traits\\genericPlainTemplateTrait.php',
  ),
  'genericRepositoryListViewTrait' => 
  array (
    0 => '//dc\\common\\traits\\genericRepositoryListViewTrait.php',
  ),
  'genericRepositoryTrait' => 
  array (
    0 => '//dc\\common\\traits\\genericRepositoryTrait.php',
  ),
  'genericViewModelTrait' => 
  array (
    0 => '//dc\\common\\traits\\genericViewModelTrait.php',
  ),
  'genericViewTrait' => 
  array (
    0 => '//dc\\common\\traits\\genericViewTrait.php',
  ),
  'hasConfigTrait' => 
  array (
    0 => '//dc\\common\\traits\\hasConfigTrait.php',
  ),
  'hookableTrait' => 
  array (
    0 => '//dc\\common\\traits\\hookableTrait.php',
  ),
  'reflectionIDSetter' => 
  array (
    0 => '//dc\\common\\traits\\reflectionIDSetter.php',
  ),
  'universallyGettableTrait' => 
  array (
    0 => '//dc\\common\\traits\\universallyGettableTrait.php',
  ),
  'DcAutoloader' =>
  array (
    0 => '//dc\\DcAutoloader.php',
  ),
  'ComposerAutoloaderInit978a594082a4e49df66ab49110024d5b' => 
  array (
    0 => '//vendor\\composer\\autoload_real.php',
  ),
  'Composer\\Autoload\\ClassLoader' => 
  array (
    0 => '//vendor\\composer\\ClassLoader.php',
  ),
  'Doctrine\\Instantiator\\Exception\\ExceptionInterface' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\src\\Doctrine\\Instantiator\\Exception\\ExceptionInterface.php',
  ),
  'Doctrine\\Instantiator\\Exception\\InvalidArgumentException' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\src\\Doctrine\\Instantiator\\Exception\\InvalidArgumentException.php',
  ),
  'Doctrine\\Instantiator\\Exception\\UnexpectedValueException' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\src\\Doctrine\\Instantiator\\Exception\\UnexpectedValueException.php',
  ),
  'Doctrine\\Instantiator\\Instantiator' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\src\\Doctrine\\Instantiator\\Instantiator.php',
  ),
  'Doctrine\\Instantiator\\InstantiatorInterface' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\src\\Doctrine\\Instantiator\\InstantiatorInterface.php',
  ),
  'DoctrineTest\\InstantiatorPerformance\\InstantiatorPerformanceEvent' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorPerformance\\InstantiatorPerformanceEvent.php',
  ),
  'DoctrineTest\\InstantiatorTest\\Exception\\InvalidArgumentExceptionTest' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTest\\Exception\\InvalidArgumentExceptionTest.php',
  ),
  'DoctrineTest\\InstantiatorTest\\Exception\\UnexpectedValueExceptionTest' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTest\\Exception\\UnexpectedValueExceptionTest.php',
  ),
  'DoctrineTest\\InstantiatorTest\\InstantiatorTest' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTest\\InstantiatorTest.php',
  ),
  'DoctrineTest\\InstantiatorTestAsset\\AbstractClassAsset' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTestAsset\\AbstractClassAsset.php',
  ),
  'DoctrineTest\\InstantiatorTestAsset\\ArrayObjectAsset' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTestAsset\\ArrayObjectAsset.php',
  ),
  'DoctrineTest\\InstantiatorTestAsset\\ExceptionAsset' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTestAsset\\ExceptionAsset.php',
  ),
  'DoctrineTest\\InstantiatorTestAsset\\FinalExceptionAsset' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTestAsset\\FinalExceptionAsset.php',
  ),
  'DoctrineTest\\InstantiatorTestAsset\\PharAsset' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTestAsset\\PharAsset.php',
  ),
  'DoctrineTest\\InstantiatorTestAsset\\PharExceptionAsset' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTestAsset\\PharExceptionAsset.php',
  ),
  'DoctrineTest\\InstantiatorTestAsset\\SerializableArrayObjectAsset' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTestAsset\\SerializableArrayObjectAsset.php',
  ),
  'DoctrineTest\\InstantiatorTestAsset\\SimpleSerializableAsset' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTestAsset\\SimpleSerializableAsset.php',
  ),
  'DoctrineTest\\InstantiatorTestAsset\\SimpleTraitAsset' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTestAsset\\SimpleTraitAsset.php',
  ),
  'DoctrineTest\\InstantiatorTestAsset\\UnCloneableAsset' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTestAsset\\UnCloneableAsset.php',
  ),
  'DoctrineTest\\InstantiatorTestAsset\\UnserializeExceptionArrayObjectAsset' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTestAsset\\UnserializeExceptionArrayObjectAsset.php',
  ),
  'DoctrineTest\\InstantiatorTestAsset\\WakeUpNoticesAsset' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTestAsset\\WakeUpNoticesAsset.php',
  ),
  'DoctrineTest\\InstantiatorTestAsset\\XMLReaderAsset' => 
  array (
    0 => '//vendor\\doctrine\\instantiator\\tests\\DoctrineTest\\InstantiatorTestAsset\\XMLReaderAsset.php',
  ),
  'Monolog\\ErrorHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\ErrorHandler.php',
  ),
  'Monolog\\Formatter\\ChromePHPFormatter' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\ChromePHPFormatter.php',
  ),
  'Monolog\\Formatter\\ElasticaFormatter' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\ElasticaFormatter.php',
  ),
  'Monolog\\Formatter\\FlowdockFormatter' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\FlowdockFormatter.php',
  ),
  'Monolog\\Formatter\\FormatterInterface' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\FormatterInterface.php',
  ),
  'Monolog\\Formatter\\GelfMessageFormatter' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\GelfMessageFormatter.php',
  ),
  'Monolog\\Formatter\\HtmlFormatter' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\HtmlFormatter.php',
  ),
  'Monolog\\Formatter\\JsonFormatter' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\JsonFormatter.php',
  ),
  'Monolog\\Formatter\\LineFormatter' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\LineFormatter.php',
  ),
  'Monolog\\Formatter\\LogglyFormatter' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\LogglyFormatter.php',
  ),
  'Monolog\\Formatter\\LogstashFormatter' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\LogstashFormatter.php',
  ),
  'Monolog\\Formatter\\MongoDBFormatter' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\MongoDBFormatter.php',
  ),
  'Monolog\\Formatter\\NormalizerFormatter' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\NormalizerFormatter.php',
  ),
  'Monolog\\Formatter\\ScalarFormatter' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\ScalarFormatter.php',
  ),
  'Monolog\\Formatter\\WildfireFormatter' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Formatter\\WildfireFormatter.php',
  ),
  'Monolog\\Handler\\AbstractHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\AbstractHandler.php',
  ),
  'Monolog\\Handler\\AbstractProcessingHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\AbstractProcessingHandler.php',
  ),
  'Monolog\\Handler\\AbstractSyslogHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\AbstractSyslogHandler.php',
  ),
  'Monolog\\Handler\\AmqpHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\AmqpHandler.php',
  ),
  'Monolog\\Handler\\BrowserConsoleHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\BrowserConsoleHandler.php',
  ),
  'Monolog\\Handler\\BufferHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\BufferHandler.php',
  ),
  'Monolog\\Handler\\ChromePHPHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\ChromePHPHandler.php',
  ),
  'Monolog\\Handler\\CouchDBHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\CouchDBHandler.php',
  ),
  'Monolog\\Handler\\CubeHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\CubeHandler.php',
  ),
  'Monolog\\Handler\\Curl\\Util' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\Curl\\Util.php',
  ),
  'Monolog\\Handler\\DoctrineCouchDBHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\DoctrineCouchDBHandler.php',
  ),
  'Monolog\\Handler\\DynamoDbHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\DynamoDbHandler.php',
  ),
  'Monolog\\Handler\\ElasticSearchHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\ElasticSearchHandler.php',
  ),
  'Monolog\\Handler\\ErrorLogHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\ErrorLogHandler.php',
  ),
  'Monolog\\Handler\\FilterHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\FilterHandler.php',
  ),
  'Monolog\\Handler\\FingersCrossed\\ActivationStrategyInterface' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\FingersCrossed\\ActivationStrategyInterface.php',
  ),
  'Monolog\\Handler\\FingersCrossed\\ChannelLevelActivationStrategy' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\FingersCrossed\\ChannelLevelActivationStrategy.php',
  ),
  'Monolog\\Handler\\FingersCrossed\\ErrorLevelActivationStrategy' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\FingersCrossed\\ErrorLevelActivationStrategy.php',
  ),
  'Monolog\\Handler\\FingersCrossedHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\FingersCrossedHandler.php',
  ),
  'Monolog\\Handler\\FirePHPHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\FirePHPHandler.php',
  ),
  'Monolog\\Handler\\FleepHookHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\FleepHookHandler.php',
  ),
  'Monolog\\Handler\\FlowdockHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\FlowdockHandler.php',
  ),
  'Monolog\\Handler\\GelfHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\GelfHandler.php',
  ),
  'Monolog\\Handler\\GroupHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\GroupHandler.php',
  ),
  'Monolog\\Handler\\HandlerInterface' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\HandlerInterface.php',
  ),
  'Monolog\\Handler\\HipChatHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\HipChatHandler.php',
  ),
  'Monolog\\Handler\\IFTTTHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\IFTTTHandler.php',
  ),
  'Monolog\\Handler\\LogEntriesHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\LogEntriesHandler.php',
  ),
  'Monolog\\Handler\\LogglyHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\LogglyHandler.php',
  ),
  'Monolog\\Handler\\MailHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\MailHandler.php',
  ),
  'Monolog\\Handler\\MandrillHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\MandrillHandler.php',
  ),
  'Monolog\\Handler\\MissingExtensionException' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\MissingExtensionException.php',
  ),
  'Monolog\\Handler\\MongoDBHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\MongoDBHandler.php',
  ),
  'Monolog\\Handler\\NativeMailerHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\NativeMailerHandler.php',
  ),
  'Monolog\\Handler\\NewRelicHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\NewRelicHandler.php',
  ),
  'Monolog\\Handler\\NullHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\NullHandler.php',
  ),
  'Monolog\\Handler\\PHPConsoleHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\PHPConsoleHandler.php',
  ),
  'Monolog\\Handler\\PsrHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\PsrHandler.php',
  ),
  'Monolog\\Handler\\PushoverHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\PushoverHandler.php',
  ),
  'Monolog\\Handler\\RavenHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\RavenHandler.php',
  ),
  'Monolog\\Handler\\RedisHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\RedisHandler.php',
  ),
  'Monolog\\Handler\\RollbarHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\RollbarHandler.php',
  ),
  'Monolog\\Handler\\RotatingFileHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\RotatingFileHandler.php',
  ),
  'Monolog\\Handler\\SamplingHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\SamplingHandler.php',
  ),
  'Monolog\\Handler\\SlackHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\SlackHandler.php',
  ),
  'Monolog\\Handler\\SocketHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\SocketHandler.php',
  ),
  'Monolog\\Handler\\StreamHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\StreamHandler.php',
  ),
  'Monolog\\Handler\\SwiftMailerHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\SwiftMailerHandler.php',
  ),
  'Monolog\\Handler\\SyslogHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\SyslogHandler.php',
  ),
  'Monolog\\Handler\\SyslogUdp\\UdpSocket' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\SyslogUdp\\UdpSocket.php',
  ),
  'Monolog\\Handler\\SyslogUdpHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\SyslogUdpHandler.php',
  ),
  'Monolog\\Handler\\TestHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\TestHandler.php',
  ),
  'Monolog\\Handler\\WhatFailureGroupHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\WhatFailureGroupHandler.php',
  ),
  'Monolog\\Handler\\ZendMonitorHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Handler\\ZendMonitorHandler.php',
  ),
  'Monolog\\Logger' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Logger.php',
  ),
  'Monolog\\Processor\\GitProcessor' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Processor\\GitProcessor.php',
  ),
  'Monolog\\Processor\\IntrospectionProcessor' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Processor\\IntrospectionProcessor.php',
  ),
  'Monolog\\Processor\\MemoryPeakUsageProcessor' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Processor\\MemoryPeakUsageProcessor.php',
  ),
  'Monolog\\Processor\\MemoryProcessor' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Processor\\MemoryProcessor.php',
  ),
  'Monolog\\Processor\\MemoryUsageProcessor' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Processor\\MemoryUsageProcessor.php',
  ),
  'Monolog\\Processor\\ProcessIdProcessor' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Processor\\ProcessIdProcessor.php',
  ),
  'Monolog\\Processor\\PsrLogMessageProcessor' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Processor\\PsrLogMessageProcessor.php',
  ),
  'Monolog\\Processor\\TagProcessor' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Processor\\TagProcessor.php',
  ),
  'Monolog\\Processor\\UidProcessor' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Processor\\UidProcessor.php',
  ),
  'Monolog\\Processor\\WebProcessor' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Processor\\WebProcessor.php',
  ),
  'Monolog\\Registry' => 
  array (
    0 => '//vendor\\monolog\\monolog\\src\\Monolog\\Registry.php',
  ),
  'Monolog\\ErrorHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\ErrorHandlerTest.php',
  ),
  'Monolog\\Formatter\\ChromePHPFormatterTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\ChromePHPFormatterTest.php',
  ),
  'Monolog\\Formatter\\ElasticaFormatterTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\ElasticaFormatterTest.php',
  ),
  'Monolog\\Formatter\\FlowdockFormatterTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\FlowdockFormatterTest.php',
  ),
  'Monolog\\Formatter\\GelfMessageFormatterTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\GelfMessageFormatterTest.php',
  ),
  'Monolog\\Formatter\\JsonFormatterTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\JsonFormatterTest.php',
  ),
  'Monolog\\Formatter\\LineFormatterTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\LineFormatterTest.php',
  ),
  'Monolog\\Formatter\\TestFoo' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\LineFormatterTest.php',
  ),
  'Monolog\\Formatter\\TestBar' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\LineFormatterTest.php',
  ),
  'Monolog\\Formatter\\LogglyFormatterTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\LogglyFormatterTest.php',
  ),
  'Monolog\\Formatter\\LogstashFormatterTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\LogstashFormatterTest.php',
  ),
  'Monolog\\Formatter\\MongoDBFormatterTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\MongoDBFormatterTest.php',
  ),
  'Monolog\\Formatter\\NormalizerFormatterTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\NormalizerFormatterTest.php',
  ),
  'Monolog\\Formatter\\TestFooNorm' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\NormalizerFormatterTest.php',
  ),
  'Monolog\\Formatter\\TestBarNorm' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\NormalizerFormatterTest.php',
  ),
  'Monolog\\Formatter\\TestStreamFoo' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\NormalizerFormatterTest.php',
  ),
  'Monolog\\Formatter\\ScalarFormatterTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\ScalarFormatterTest.php',
  ),
  'Monolog\\Formatter\\WildfireFormatterTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Formatter\\WildfireFormatterTest.php',
  ),
  'Monolog\\Handler\\AbstractHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\AbstractHandlerTest.php',
  ),
  'Monolog\\Handler\\AbstractProcessingHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\AbstractProcessingHandlerTest.php',
  ),
  'Monolog\\Handler\\AmqpHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\AmqpHandlerTest.php',
  ),
  'Monolog\\Handler\\BrowserConsoleHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\BrowserConsoleHandlerTest.php',
  ),
  'Monolog\\Handler\\BufferHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\BufferHandlerTest.php',
  ),
  'Monolog\\Handler\\ChromePHPHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\ChromePHPHandlerTest.php',
  ),
  'Monolog\\Handler\\TestChromePHPHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\ChromePHPHandlerTest.php',
  ),
  'Monolog\\Handler\\CouchDBHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\CouchDBHandlerTest.php',
  ),
  'Monolog\\Handler\\DoctrineCouchDBHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\DoctrineCouchDBHandlerTest.php',
  ),
  'Monolog\\Handler\\DynamoDbHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\DynamoDbHandlerTest.php',
  ),
  'Monolog\\Handler\\ElasticSearchHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\ElasticSearchHandlerTest.php',
  ),
  'Monolog\\Handler\\ErrorLogHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\ErrorLogHandlerTest.php',
  ),
  'Monolog\\Handler\\FilterHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\FilterHandlerTest.php',
  ),
  'Monolog\\Handler\\FingersCrossedHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\FingersCrossedHandlerTest.php',
  ),
  'Monolog\\Handler\\FirePHPHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\FirePHPHandlerTest.php',
  ),
  'Monolog\\Handler\\TestFirePHPHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\FirePHPHandlerTest.php',
  ),
  'Monolog\\Handler\\FleepHookHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\FleepHookHandlerTest.php',
  ),
  'Monolog\\Handler\\FlowdockHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\FlowdockHandlerTest.php',
  ),
  'Monolog\\Handler\\GelfHandlerLegacyTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\GelfHandlerLegacyTest.php',
  ),
  'Monolog\\Handler\\GelfHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\GelfHandlerTest.php',
  ),
  'Monolog\\Handler\\GelfMockMessagePublisher' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\GelfMockMessagePublisher.php',
  ),
  'Monolog\\Handler\\GroupHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\GroupHandlerTest.php',
  ),
  'Monolog\\Handler\\HipChatHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\HipChatHandlerTest.php',
  ),
  'Monolog\\Handler\\LogEntriesHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\LogEntriesHandlerTest.php',
  ),
  'Monolog\\Handler\\MailHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\MailHandlerTest.php',
  ),
  'Monolog\\Handler\\MockRavenClient' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\MockRavenClient.php',
  ),
  'Monolog\\Handler\\MongoDBHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\MongoDBHandlerTest.php',
  ),
  'Monolog\\Handler\\NativeMailerHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\NativeMailerHandlerTest.php',
  ),
  'Monolog\\Handler\\NewRelicHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\NewRelicHandlerTest.php',
  ),
  'Monolog\\Handler\\StubNewRelicHandlerWithoutExtension' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\NewRelicHandlerTest.php',
  ),
  'Monolog\\Handler\\StubNewRelicHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\NewRelicHandlerTest.php',
  ),
  'Monolog\\Handler\\NullHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\NullHandlerTest.php',
  ),
  'Monolog\\Handler\\PHPConsoleHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\PHPConsoleHandlerTest.php',
  ),
  'Monolog\\Handler\\PsrHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\PsrHandlerTest.php',
  ),
  'Monolog\\Handler\\PushoverHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\PushoverHandlerTest.php',
  ),
  'Monolog\\Handler\\RavenHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\RavenHandlerTest.php',
  ),
  'Monolog\\Handler\\RedisHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\RedisHandlerTest.php',
  ),
  'Monolog\\Handler\\RotatingFileHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\RotatingFileHandlerTest.php',
  ),
  'Monolog\\Handler\\SamplingHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\SamplingHandlerTest.php',
  ),
  'Monolog\\Handler\\SlackHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\SlackHandlerTest.php',
  ),
  'Monolog\\Handler\\SocketHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\SocketHandlerTest.php',
  ),
  'Monolog\\Handler\\StreamHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\StreamHandlerTest.php',
  ),
  'Monolog\\Handler\\SwiftMailerHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\SwiftMailerHandlerTest.php',
  ),
  'Monolog\\Handler\\SyslogHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\SyslogHandlerTest.php',
  ),
  'Monolog\\Handler\\SyslogUdpHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\SyslogUdpHandlerTest.php',
  ),
  'Monolog\\Handler\\TestHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\TestHandlerTest.php',
  ),
  'Monolog\\Handler\\UdpSocketTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\UdpSocketTest.php',
  ),
  'Monolog\\Handler\\WhatFailureGroupHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\WhatFailureGroupHandlerTest.php',
  ),
  'Monolog\\Handler\\ExceptionTestHandler' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\WhatFailureGroupHandlerTest.php',
  ),
  'Monolog\\Handler\\ZendMonitorHandlerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Handler\\ZendMonitorHandlerTest.php',
  ),
  'Monolog\\LoggerTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\LoggerTest.php',
  ),
  'Monolog\\Processor\\GitProcessorTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Processor\\GitProcessorTest.php',
  ),
  'Acme\\Tester' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Processor\\IntrospectionProcessorTest.php',
  ),
  'Acme\\IntrospectionProcessorTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Processor\\IntrospectionProcessorTest.php',
  ),
  'Monolog\\Processor\\MemoryPeakUsageProcessorTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Processor\\MemoryPeakUsageProcessorTest.php',
  ),
  'Monolog\\Processor\\MemoryUsageProcessorTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Processor\\MemoryUsageProcessorTest.php',
  ),
  'Monolog\\Processor\\ProcessIdProcessorTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Processor\\ProcessIdProcessorTest.php',
  ),
  'Monolog\\Processor\\PsrLogMessageProcessorTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Processor\\PsrLogMessageProcessorTest.php',
  ),
  'Monolog\\Processor\\TagProcessorTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Processor\\TagProcessorTest.php',
  ),
  'Monolog\\Processor\\UidProcessorTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Processor\\UidProcessorTest.php',
  ),
  'Monolog\\Processor\\WebProcessorTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\Processor\\WebProcessorTest.php',
  ),
  'Monolog\\PsrLogCompatTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\PsrLogCompatTest.php',
  ),
  'Monolog\\RegistryTest' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\RegistryTest.php',
  ),
  'Monolog\\TestCase' => 
  array (
    0 => '//vendor\\monolog\\monolog\\tests\\Monolog\\TestCase.php',
  ),
  'DeepCopy\\DeepCopy' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\DeepCopy.php',
  ),
  'DeepCopy\\Exception\\CloneException' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\Exception\\CloneException.php',
  ),
  'DeepCopy\\Filter\\Doctrine\\DoctrineCollectionFilter' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\Filter\\Doctrine\\DoctrineCollectionFilter.php',
  ),
  'DeepCopy\\Filter\\Doctrine\\DoctrineEmptyCollectionFilter' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\Filter\\Doctrine\\DoctrineEmptyCollectionFilter.php',
  ),
  'DeepCopy\\Filter\\Filter' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\Filter\\Filter.php',
  ),
  'DeepCopy\\Filter\\KeepFilter' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\Filter\\KeepFilter.php',
  ),
  'DeepCopy\\Filter\\ReplaceFilter' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\Filter\\ReplaceFilter.php',
  ),
  'DeepCopy\\Filter\\SetNullFilter' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\Filter\\SetNullFilter.php',
  ),
  'DeepCopy\\Matcher\\Matcher' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\Matcher\\Matcher.php',
  ),
  'DeepCopy\\Matcher\\PropertyMatcher' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\Matcher\\PropertyMatcher.php',
  ),
  'DeepCopy\\Matcher\\PropertyNameMatcher' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\Matcher\\PropertyNameMatcher.php',
  ),
  'DeepCopy\\Matcher\\PropertyTypeMatcher' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\Matcher\\PropertyTypeMatcher.php',
  ),
  'DeepCopy\\Reflection\\ReflectionHelper' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\Reflection\\ReflectionHelper.php',
  ),
  'DeepCopy\\TypeFilter\\ReplaceFilter' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\TypeFilter\\ReplaceFilter.php',
  ),
  'DeepCopy\\TypeFilter\\ShallowCopyFilter' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\TypeFilter\\ShallowCopyFilter.php',
  ),
  'DeepCopy\\TypeFilter\\TypeFilter' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\TypeFilter\\TypeFilter.php',
  ),
  'DeepCopy\\TypeMatcher\\TypeMatcher' => 
  array (
    0 => '//vendor\\myclabs\\deep-copy\\src\\DeepCopy\\TypeMatcher\\TypeMatcher.php',
  ),
  'Phly\\Mustache\\Exception\\BadMethodCallException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\BadMethodCallException.php',
  ),
  'Phly\\Mustache\\Exception\\DuplicatePragmaException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\DuplicatePragmaException.php',
  ),
  'Phly\\Mustache\\Exception\\ExceptionInterface' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\ExceptionInterface.php',
  ),
  'Phly\\Mustache\\Exception\\InvalidDelimiterException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\InvalidDelimiterException.php',
  ),
  'Phly\\Mustache\\Exception\\InvalidEscaperException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\InvalidEscaperException.php',
  ),
  'Phly\\Mustache\\Exception\\InvalidNamespaceException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\InvalidNamespaceException.php',
  ),
  'Phly\\Mustache\\Exception\\InvalidPartialsException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\InvalidPartialsException.php',
  ),
  'Phly\\Mustache\\Exception\\InvalidPragmaNameException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\InvalidPragmaNameException.php',
  ),
  'Phly\\Mustache\\Exception\\InvalidStateException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\InvalidStateException.php',
  ),
  'Phly\\Mustache\\Exception\\InvalidTemplateException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\InvalidTemplateException.php',
  ),
  'Phly\\Mustache\\Exception\\InvalidTemplatePathException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\InvalidTemplatePathException.php',
  ),
  'Phly\\Mustache\\Exception\\InvalidTokenException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\InvalidTokenException.php',
  ),
  'Phly\\Mustache\\Exception\\InvalidTokensException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\InvalidTokensException.php',
  ),
  'Phly\\Mustache\\Exception\\InvalidVariableNameException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\InvalidVariableNameException.php',
  ),
  'Phly\\Mustache\\Exception\\MissingPragmaNameException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\MissingPragmaNameException.php',
  ),
  'Phly\\Mustache\\Exception\\PragmaNotFoundException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\PragmaNotFoundException.php',
  ),
  'Phly\\Mustache\\Exception\\ResolverTypeNotFoundException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\ResolverTypeNotFoundException.php',
  ),
  'Phly\\Mustache\\Exception\\TemplateNotFoundException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\TemplateNotFoundException.php',
  ),
  'Phly\\Mustache\\Exception\\UnbalancedSectionException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\UnbalancedSectionException.php',
  ),
  'Phly\\Mustache\\Exception\\UnbalancedTagException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\UnbalancedTagException.php',
  ),
  'Phly\\Mustache\\Exception\\UnregisteredPragmaException' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Exception\\UnregisteredPragmaException.php',
  ),
  'Phly\\Mustache\\Lexer' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Lexer.php',
  ),
  'Phly\\Mustache\\Mustache' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Mustache.php',
  ),
  'Phly\\Mustache\\Pragma\\ContextualEscape' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Pragma\\ContextualEscape.php',
  ),
  'Phly\\Mustache\\Pragma\\ImplicitIterator' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Pragma\\ImplicitIterator.php',
  ),
  'Phly\\Mustache\\Pragma\\PragmaCollection' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Pragma\\PragmaCollection.php',
  ),
  'Phly\\Mustache\\Pragma\\PragmaInterface' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Pragma\\PragmaInterface.php',
  ),
  'Phly\\Mustache\\Pragma\\PragmaNameAndTokensTrait' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Pragma\\PragmaNameAndTokensTrait.php',
  ),
  'Phly\\Mustache\\Pragma\\SubView' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Pragma\\SubView.php',
  ),
  'Phly\\Mustache\\Pragma\\SubViews' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Pragma\\SubViews.php',
  ),
  'Phly\\Mustache\\Renderer' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Renderer.php',
  ),
  'Phly\\Mustache\\Resolver\\AggregateResolver' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Resolver\\AggregateResolver.php',
  ),
  'Phly\\Mustache\\Resolver\\DefaultResolver' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Resolver\\DefaultResolver.php',
  ),
  'Phly\\Mustache\\Resolver\\ResolverInterface' => 
  array (
    0 => '//vendor\\phly\\phly-mustache\\src\\Resolver\\ResolverInterface.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Context' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Context.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Description' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Description.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Location' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Location.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Serializer' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Serializer.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\AuthorTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\AuthorTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\CoversTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\CoversTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\DeprecatedTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\DeprecatedTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\ExampleTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\ExampleTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\LinkTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\LinkTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\MethodTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\MethodTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\ParamTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\ParamTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\PropertyReadTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\PropertyReadTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\PropertyTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\PropertyTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\PropertyWriteTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\PropertyWriteTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\ReturnTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\ReturnTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\SeeTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\SeeTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\SinceTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\SinceTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\SourceTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\SourceTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\ThrowsTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\ThrowsTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\UsesTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\UsesTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\VarTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\VarTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\VersionTag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag\\VersionTag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Tag.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Type\\Collection' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock\\Type\\Collection.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\src\\phpDocumentor\\Reflection\\DocBlock.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\DescriptionTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\DescriptionTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\CoversTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\CoversTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\DeprecatedTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\DeprecatedTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\ExampleTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\ExampleTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\LinkTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\LinkTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\MethodTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\MethodTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\ParamTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\ParamTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\ReturnTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\ReturnTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\SeeTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\SeeTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\SinceTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\SinceTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\SourceTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\SourceTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\ThrowsTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\ThrowsTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\UsesTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\UsesTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\VarTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\VarTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Tag\\VersionTagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Tag\\VersionTagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\TagTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\TagTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlock\\Type\\CollectionTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlock\\Type\\CollectionTest.php',
  ),
  'phpDocumentor\\Reflection\\DocBlockTest' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlockTest.php',
  ),
  'phpDocumentor\\Reflection\\MyReflectionDocBlock' => 
  array (
    0 => '//vendor\\phpdocumentor\\reflection-docblock\\tests\\phpDocumentor\\Reflection\\DocBlockTest.php',
  ),
  'spec\\Prophecy\\Argument\\ArgumentsWildcardSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\ArgumentsWildcardSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\AnyValuesTokenSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\AnyValuesTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\AnyValueTokenSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\AnyValueTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\ArrayCountTokenSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\ArrayCountTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\ArrayEntryTokenSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\ArrayEntryTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\ArrayEveryEntryTokenSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\ArrayEveryEntryTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\CallbackTokenSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\CallbackTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\ExactValueTokenSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\ExactValueTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\ExactValueTokenFixtureA' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\ExactValueTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\ExactValueTokenFixtureB' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\ExactValueTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\IdenticalValueTokenSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\IdenticalValueTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\LogicalAndTokenSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\LogicalAndTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\LogicalNotTokenSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\LogicalNotTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\ObjectStateTokenSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\ObjectStateTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\ObjectStateTokenFixtureA' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\ObjectStateTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\ObjectStateTokenFixtureB' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\ObjectStateTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\StringContainsTokenSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\StringContainsTokenSpec.php',
  ),
  'spec\\Prophecy\\Argument\\Token\\TypeTokenSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Argument\\Token\\TypeTokenSpec.php',
  ),
  'spec\\Prophecy\\ArgumentSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\ArgumentSpec.php',
  ),
  'spec\\Prophecy\\Call\\CallCenterSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Call\\CallCenterSpec.php',
  ),
  'spec\\Prophecy\\Call\\CallSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Call\\CallSpec.php',
  ),
  'spec\\Prophecy\\Comparator\\ClosureComparatorSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Comparator\\ClosureComparatorSpec.php',
  ),
  'spec\\Prophecy\\Comparator\\FactorySpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Comparator\\FactorySpec.php',
  ),
  'spec\\Prophecy\\Doubler\\ClassPatch\\DisableConstructorPatchSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\ClassPatch\\DisableConstructorPatchSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\ClassPatch\\HhvmExceptionPatchSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\ClassPatch\\HhvmExceptionPatchSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\ClassPatch\\KeywordPatchSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\ClassPatch\\KeywordPatchSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\ClassPatch\\MagicCallPatchSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\ClassPatch\\MagicCallPatchSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\ClassPatch\\MagicalApi' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\ClassPatch\\MagicCallPatchSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\ClassPatch\\MagicalApiExtended' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\ClassPatch\\MagicCallPatchSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\ClassPatch\\ProphecySubjectPatchSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\ClassPatch\\ProphecySubjectPatchSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\ClassPatch\\ReflectionClassNewInstancePatchSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\ClassPatch\\ReflectionClassNewInstancePatchSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\ClassPatch\\SplFileInfoPatchSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\ClassPatch\\SplFileInfoPatchSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\ClassPatch\\TraversablePatchSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\ClassPatch\\TraversablePatchSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\DoublerSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\DoublerSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\WithFinalConstructor' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\DoublerSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\Generator\\ClassCodeGeneratorSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\Generator\\ClassCodeGeneratorSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\Generator\\CustomClass' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\Generator\\ClassCodeGeneratorSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\Generator\\ClassCreatorSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\Generator\\ClassCreatorSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\Generator\\ClassMirrorSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\Generator\\ClassMirrorSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\Generator\\OptionalDepsClass' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\Generator\\ClassMirrorSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\Generator\\Node\\ArgumentNodeSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\Generator\\Node\\ArgumentNodeSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\Generator\\Node\\ClassNodeSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\Generator\\Node\\ClassNodeSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\Generator\\Node\\MethodNodeSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\Generator\\Node\\MethodNodeSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\LazyDoubleSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\LazyDoubleSpec.php',
  ),
  'spec\\Prophecy\\Doubler\\NameGeneratorSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Doubler\\NameGeneratorSpec.php',
  ),
  'spec\\Prophecy\\Exception\\Call\\UnexpectedCallExceptionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Exception\\Call\\UnexpectedCallExceptionSpec.php',
  ),
  'spec\\Prophecy\\Exception\\Doubler\\ClassCreatorExceptionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Exception\\Doubler\\ClassCreatorExceptionSpec.php',
  ),
  'spec\\Prophecy\\Exception\\Doubler\\ClassMirrorExceptionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Exception\\Doubler\\ClassMirrorExceptionSpec.php',
  ),
  'spec\\Prophecy\\Exception\\Doubler\\ClassNotFoundExceptionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Exception\\Doubler\\ClassNotFoundExceptionSpec.php',
  ),
  'spec\\Prophecy\\Exception\\Doubler\\DoubleExceptionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Exception\\Doubler\\DoubleExceptionSpec.php',
  ),
  'spec\\Prophecy\\Exception\\Doubler\\InterfaceNotFoundExceptionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Exception\\Doubler\\InterfaceNotFoundExceptionSpec.php',
  ),
  'spec\\Prophecy\\Exception\\Doubler\\MethodNotFoundExceptionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Exception\\Doubler\\MethodNotFoundExceptionSpec.php',
  ),
  'spec\\Prophecy\\Exception\\Prediction\\AggregateExceptionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Exception\\Prediction\\AggregateExceptionSpec.php',
  ),
  'spec\\Prophecy\\Exception\\Prediction\\NoCallsExceptionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Exception\\Prediction\\NoCallsExceptionSpec.php',
  ),
  'spec\\Prophecy\\Exception\\Prediction\\UnexpectedCallsCountExceptionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Exception\\Prediction\\UnexpectedCallsCountExceptionSpec.php',
  ),
  'spec\\Prophecy\\Exception\\Prediction\\UnexpectedCallsExceptionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Exception\\Prediction\\UnexpectedCallsExceptionSpec.php',
  ),
  'spec\\Prophecy\\Exception\\Prophecy\\MethodProphecyExceptionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Exception\\Prophecy\\MethodProphecyExceptionSpec.php',
  ),
  'spec\\Prophecy\\Exception\\Prophecy\\ObjectProphecyExceptionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Exception\\Prophecy\\ObjectProphecyExceptionSpec.php',
  ),
  'spec\\Prophecy\\Prediction\\CallbackPredictionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Prediction\\CallbackPredictionSpec.php',
  ),
  'spec\\Prophecy\\Prediction\\CallPredictionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Prediction\\CallPredictionSpec.php',
  ),
  'spec\\Prophecy\\Prediction\\CallTimesPredictionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Prediction\\CallTimesPredictionSpec.php',
  ),
  'spec\\Prophecy\\Prediction\\NoCallsPredictionSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Prediction\\NoCallsPredictionSpec.php',
  ),
  'spec\\Prophecy\\Promise\\CallbackPromiseSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Promise\\CallbackPromiseSpec.php',
  ),
  'spec\\Prophecy\\Promise\\ClassCallback' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Promise\\CallbackPromiseSpec.php',
  ),
  'spec\\Prophecy\\Promise\\ReturnArgumentPromiseSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Promise\\ReturnArgumentPromiseSpec.php',
  ),
  'spec\\Prophecy\\Promise\\ReturnPromiseSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Promise\\ReturnPromiseSpec.php',
  ),
  'spec\\Prophecy\\Promise\\ThrowPromiseSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Promise\\ThrowPromiseSpec.php',
  ),
  'spec\\Prophecy\\Promise\\RequiredArgumentException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Promise\\ThrowPromiseSpec.php',
  ),
  'spec\\Prophecy\\Prophecy\\ClassWithFinalMethod' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Prophecy\\MethodProphecySpec.php',
  ),
  'spec\\Prophecy\\Prophecy\\MethodProphecySpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Prophecy\\MethodProphecySpec.php',
  ),
  'spec\\Prophecy\\Prophecy\\ObjectProphecySpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Prophecy\\ObjectProphecySpec.php',
  ),
  'spec\\Prophecy\\Prophecy\\ObjectProphecySpecFixtureA' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Prophecy\\ObjectProphecySpec.php',
  ),
  'spec\\Prophecy\\Prophecy\\ObjectProphecySpecFixtureB' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Prophecy\\ObjectProphecySpec.php',
  ),
  'spec\\Prophecy\\Prophecy\\RevealerSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Prophecy\\RevealerSpec.php',
  ),
  'spec\\Prophecy\\ProphetSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\ProphetSpec.php',
  ),
  'spec\\Prophecy\\Util\\StringUtilSpec' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\spec\\Prophecy\\Util\\StringUtilSpec.php',
  ),
  'Prophecy\\Argument\\ArgumentsWildcard' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\ArgumentsWildcard.php',
  ),
  'Prophecy\\Argument\\Token\\AnyValuesToken' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\AnyValuesToken.php',
  ),
  'Prophecy\\Argument\\Token\\AnyValueToken' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\AnyValueToken.php',
  ),
  'Prophecy\\Argument\\Token\\ArrayCountToken' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\ArrayCountToken.php',
  ),
  'Prophecy\\Argument\\Token\\ArrayEntryToken' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\ArrayEntryToken.php',
  ),
  'Prophecy\\Argument\\Token\\ArrayEveryEntryToken' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\ArrayEveryEntryToken.php',
  ),
  'Prophecy\\Argument\\Token\\CallbackToken' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\CallbackToken.php',
  ),
  'Prophecy\\Argument\\Token\\ExactValueToken' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\ExactValueToken.php',
  ),
  'Prophecy\\Argument\\Token\\IdenticalValueToken' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\IdenticalValueToken.php',
  ),
  'Prophecy\\Argument\\Token\\LogicalAndToken' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\LogicalAndToken.php',
  ),
  'Prophecy\\Argument\\Token\\LogicalNotToken' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\LogicalNotToken.php',
  ),
  'Prophecy\\Argument\\Token\\ObjectStateToken' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\ObjectStateToken.php',
  ),
  'Prophecy\\Argument\\Token\\StringContainsToken' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\StringContainsToken.php',
  ),
  'Prophecy\\Argument\\Token\\TokenInterface' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\TokenInterface.php',
  ),
  'Prophecy\\Argument\\Token\\TypeToken' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument\\Token\\TypeToken.php',
  ),
  'Prophecy\\Argument' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Argument.php',
  ),
  'Prophecy\\Call\\Call' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Call\\Call.php',
  ),
  'Prophecy\\Call\\CallCenter' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Call\\CallCenter.php',
  ),
  'Prophecy\\Comparator\\ClosureComparator' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Comparator\\ClosureComparator.php',
  ),
  'Prophecy\\Comparator\\Factory' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Comparator\\Factory.php',
  ),
  'Prophecy\\Doubler\\CachedDoubler' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\CachedDoubler.php',
  ),
  'Prophecy\\Doubler\\ClassPatch\\ClassPatchInterface' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\ClassPatch\\ClassPatchInterface.php',
  ),
  'Prophecy\\Doubler\\ClassPatch\\DisableConstructorPatch' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\ClassPatch\\DisableConstructorPatch.php',
  ),
  'Prophecy\\Doubler\\ClassPatch\\HhvmExceptionPatch' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\ClassPatch\\HhvmExceptionPatch.php',
  ),
  'Prophecy\\Doubler\\ClassPatch\\KeywordPatch' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\ClassPatch\\KeywordPatch.php',
  ),
  'Prophecy\\Doubler\\ClassPatch\\MagicCallPatch' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\ClassPatch\\MagicCallPatch.php',
  ),
  'Prophecy\\Doubler\\ClassPatch\\ProphecySubjectPatch' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\ClassPatch\\ProphecySubjectPatch.php',
  ),
  'Prophecy\\Doubler\\ClassPatch\\ReflectionClassNewInstancePatch' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\ClassPatch\\ReflectionClassNewInstancePatch.php',
  ),
  'Prophecy\\Doubler\\ClassPatch\\SplFileInfoPatch' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\ClassPatch\\SplFileInfoPatch.php',
  ),
  'Prophecy\\Doubler\\ClassPatch\\TraversablePatch' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\ClassPatch\\TraversablePatch.php',
  ),
  'Prophecy\\Doubler\\DoubleInterface' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\DoubleInterface.php',
  ),
  'Prophecy\\Doubler\\Doubler' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\Doubler.php',
  ),
  'Prophecy\\Doubler\\Generator\\ClassCodeGenerator' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\Generator\\ClassCodeGenerator.php',
  ),
  'Prophecy\\Doubler\\Generator\\ClassCreator' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\Generator\\ClassCreator.php',
  ),
  'Prophecy\\Doubler\\Generator\\ClassMirror' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\Generator\\ClassMirror.php',
  ),
  'Prophecy\\Doubler\\Generator\\Node\\ArgumentNode' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\Generator\\Node\\ArgumentNode.php',
  ),
  'Prophecy\\Doubler\\Generator\\Node\\ClassNode' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\Generator\\Node\\ClassNode.php',
  ),
  'Prophecy\\Doubler\\Generator\\Node\\MethodNode' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\Generator\\Node\\MethodNode.php',
  ),
  'Prophecy\\Doubler\\Generator\\ReflectionInterface' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\Generator\\ReflectionInterface.php',
  ),
  'Prophecy\\Doubler\\LazyDouble' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\LazyDouble.php',
  ),
  'Prophecy\\Doubler\\NameGenerator' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Doubler\\NameGenerator.php',
  ),
  'Prophecy\\Exception\\Call\\UnexpectedCallException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Call\\UnexpectedCallException.php',
  ),
  'Prophecy\\Exception\\Doubler\\ClassCreatorException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Doubler\\ClassCreatorException.php',
  ),
  'Prophecy\\Exception\\Doubler\\ClassMirrorException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Doubler\\ClassMirrorException.php',
  ),
  'Prophecy\\Exception\\Doubler\\ClassNotFoundException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Doubler\\ClassNotFoundException.php',
  ),
  'Prophecy\\Exception\\Doubler\\DoubleException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Doubler\\DoubleException.php',
  ),
  'Prophecy\\Exception\\Doubler\\DoublerException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Doubler\\DoublerException.php',
  ),
  'Prophecy\\Exception\\Doubler\\InterfaceNotFoundException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Doubler\\InterfaceNotFoundException.php',
  ),
  'Prophecy\\Exception\\Doubler\\MethodNotFoundException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Doubler\\MethodNotFoundException.php',
  ),
  'Prophecy\\Exception\\Doubler\\ReturnByReferenceException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Doubler\\ReturnByReferenceException.php',
  ),
  'Prophecy\\Exception\\Exception' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Exception.php',
  ),
  'Prophecy\\Exception\\InvalidArgumentException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\InvalidArgumentException.php',
  ),
  'Prophecy\\Exception\\Prediction\\AggregateException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Prediction\\AggregateException.php',
  ),
  'Prophecy\\Exception\\Prediction\\FailedPredictionException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Prediction\\FailedPredictionException.php',
  ),
  'Prophecy\\Exception\\Prediction\\NoCallsException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Prediction\\NoCallsException.php',
  ),
  'Prophecy\\Exception\\Prediction\\PredictionException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Prediction\\PredictionException.php',
  ),
  'Prophecy\\Exception\\Prediction\\UnexpectedCallsCountException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Prediction\\UnexpectedCallsCountException.php',
  ),
  'Prophecy\\Exception\\Prediction\\UnexpectedCallsException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Prediction\\UnexpectedCallsException.php',
  ),
  'Prophecy\\Exception\\Prophecy\\MethodProphecyException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Prophecy\\MethodProphecyException.php',
  ),
  'Prophecy\\Exception\\Prophecy\\ObjectProphecyException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Prophecy\\ObjectProphecyException.php',
  ),
  'Prophecy\\Exception\\Prophecy\\ProphecyException' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Exception\\Prophecy\\ProphecyException.php',
  ),
  'Prophecy\\Prediction\\CallbackPrediction' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Prediction\\CallbackPrediction.php',
  ),
  'Prophecy\\Prediction\\CallPrediction' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Prediction\\CallPrediction.php',
  ),
  'Prophecy\\Prediction\\CallTimesPrediction' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Prediction\\CallTimesPrediction.php',
  ),
  'Prophecy\\Prediction\\NoCallsPrediction' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Prediction\\NoCallsPrediction.php',
  ),
  'Prophecy\\Prediction\\PredictionInterface' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Prediction\\PredictionInterface.php',
  ),
  'Prophecy\\Promise\\CallbackPromise' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Promise\\CallbackPromise.php',
  ),
  'Prophecy\\Promise\\PromiseInterface' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Promise\\PromiseInterface.php',
  ),
  'Prophecy\\Promise\\ReturnArgumentPromise' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Promise\\ReturnArgumentPromise.php',
  ),
  'Prophecy\\Promise\\ReturnPromise' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Promise\\ReturnPromise.php',
  ),
  'Prophecy\\Promise\\ThrowPromise' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Promise\\ThrowPromise.php',
  ),
  'Prophecy\\Prophecy\\MethodProphecy' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Prophecy\\MethodProphecy.php',
  ),
  'Prophecy\\Prophecy\\ObjectProphecy' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Prophecy\\ObjectProphecy.php',
  ),
  'Prophecy\\Prophecy\\ProphecyInterface' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Prophecy\\ProphecyInterface.php',
  ),
  'Prophecy\\Prophecy\\ProphecySubjectInterface' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Prophecy\\ProphecySubjectInterface.php',
  ),
  'Prophecy\\Prophecy\\Revealer' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Prophecy\\Revealer.php',
  ),
  'Prophecy\\Prophecy\\RevealerInterface' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Prophecy\\RevealerInterface.php',
  ),
  'Prophecy\\Prophet' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Prophet.php',
  ),
  'Prophecy\\Util\\ExportUtil' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Util\\ExportUtil.php',
  ),
  'Prophecy\\Util\\StringUtil' => 
  array (
    0 => '//vendor\\phpspec\\prophecy\\src\\Prophecy\\Util\\StringUtil.php',
  ),
  'PHP_CodeCoverage_Driver_HHVM' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Driver\\HHVM.php',
  ),
  'PHP_CodeCoverage_Driver_PHPDBG' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Driver\\PHPDBG.php',
  ),
  'PHP_CodeCoverage_Driver_Xdebug' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Driver\\Xdebug.php',
  ),
  'PHP_CodeCoverage_Driver' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Driver.php',
  ),
  'PHP_CodeCoverage_Exception' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Exception\\Exception.php',
  ),
  'PHP_CodeCoverage_InvalidArgumentException' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Exception\\InvalidArgumentException.php',
  ),
  'PHP_CodeCoverage_RuntimeException' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Exception\\RuntimeException.php',
  ),
  'PHP_CodeCoverage_UnintentionallyCoveredCodeException' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Exception\\UnintentionallyCoveredCodeException.php',
  ),
  'PHP_CodeCoverage_Filter' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Filter.php',
  ),
  'PHP_CodeCoverage_Report_Clover' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\Clover.php',
  ),
  'PHP_CodeCoverage_Report_Crap4j' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\Crap4j.php',
  ),
  'PHP_CodeCoverage_Report_Factory' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\Factory.php',
  ),
  'PHP_CodeCoverage_Report_HTML_Renderer_Dashboard' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\HTML\\Renderer\\Dashboard.php',
  ),
  'PHP_CodeCoverage_Report_HTML_Renderer_Directory' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\HTML\\Renderer\\Directory.php',
  ),
  'PHP_CodeCoverage_Report_HTML_Renderer_File' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\HTML\\Renderer\\File.php',
  ),
  'PHP_CodeCoverage_Report_HTML_Renderer' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\HTML\\Renderer.php',
  ),
  'PHP_CodeCoverage_Report_HTML' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\HTML.php',
  ),
  'PHP_CodeCoverage_Report_Node_Directory' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\Node\\Directory.php',
  ),
  'PHP_CodeCoverage_Report_Node_File' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\Node\\File.php',
  ),
  'PHP_CodeCoverage_Report_Node_Iterator' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\Node\\Iterator.php',
  ),
  'PHP_CodeCoverage_Report_Node' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\Node.php',
  ),
  'PHP_CodeCoverage_Report_PHP' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\PHP.php',
  ),
  'PHP_CodeCoverage_Report_Text' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\Text.php',
  ),
  'PHP_CodeCoverage_Report_XML_Directory' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\XML\\Directory.php',
  ),
  'PHP_CodeCoverage_Report_XML_File_Coverage' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\XML\\File\\Coverage.php',
  ),
  'PHP_CodeCoverage_Report_XML_File_Method' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\XML\\File\\Method.php',
  ),
  'PHP_CodeCoverage_Report_XML_File_Report' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\XML\\File\\Report.php',
  ),
  'PHP_CodeCoverage_Report_XML_File_Unit' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\XML\\File\\Unit.php',
  ),
  'PHP_CodeCoverage_Report_XML_File' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\XML\\File.php',
  ),
  'PHP_CodeCoverage_Report_XML_Node' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\XML\\Node.php',
  ),
  'PHP_CodeCoverage_Report_XML_Project' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\XML\\Project.php',
  ),
  'PHP_CodeCoverage_Report_XML_Tests' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\XML\\Tests.php',
  ),
  'PHP_CodeCoverage_Report_XML_Totals' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\XML\\Totals.php',
  ),
  'PHP_CodeCoverage_Report_XML' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Report\\XML.php',
  ),
  'PHP_CodeCoverage_Util' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage\\Util.php',
  ),
  'PHP_CodeCoverage' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\src\\CodeCoverage.php',
  ),
  'PHP_CodeCoverage_FilterTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\PHP\\CodeCoverage\\FilterTest.php',
  ),
  'PHP_CodeCoverage_Report_CloverTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\PHP\\CodeCoverage\\Report\\CloverTest.php',
  ),
  'PHP_CodeCoverage_Report_FactoryTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\PHP\\CodeCoverage\\Report\\FactoryTest.php',
  ),
  'PHP_CodeCoverage_UtilTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\PHP\\CodeCoverage\\UtilTest.php',
  ),
  'PHP_CodeCoverageTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\PHP\\CodeCoverageTest.php',
  ),
  'PHP_CodeCoverage_TestCase' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\TestCase.php',
  ),
  'BankAccount' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\BankAccount.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\BankAccount.php',
  ),
  'BankAccountTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\BankAccountTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\BankAccountTest.php',
  ),
  'CoverageClassExtendedTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageClassExtendedTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageClassExtendedTest.php',
  ),
  'CoverageClassTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageClassTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageClassTest.php',
  ),
  'CoverageFunctionParenthesesTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageFunctionParenthesesTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageFunctionParenthesesTest.php',
  ),
  'CoverageFunctionParenthesesWhitespaceTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageFunctionParenthesesWhitespaceTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageFunctionParenthesesWhitespaceTest.php',
  ),
  'CoverageFunctionTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageFunctionTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageFunctionTest.php',
  ),
  'CoverageMethodOneLineAnnotationTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageMethodOneLineAnnotationTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageMethodOneLineAnnotationTest.php',
  ),
  'CoverageMethodParenthesesTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageMethodParenthesesTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageMethodParenthesesTest.php',
  ),
  'CoverageMethodParenthesesWhitespaceTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageMethodParenthesesWhitespaceTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageMethodParenthesesWhitespaceTest.php',
  ),
  'CoverageMethodTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageMethodTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageMethodTest.php',
  ),
  'CoverageNoneTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageNoneTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageNoneTest.php',
  ),
  'CoverageNothingTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageNothingTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageNothingTest.php',
  ),
  'CoverageNotPrivateTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageNotPrivateTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageNotPrivateTest.php',
  ),
  'CoverageNotProtectedTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageNotProtectedTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageNotProtectedTest.php',
  ),
  'CoverageNotPublicTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageNotPublicTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageNotPublicTest.php',
  ),
  'CoveragePrivateTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoveragePrivateTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoveragePrivateTest.php',
  ),
  'CoverageProtectedTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageProtectedTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageProtectedTest.php',
  ),
  'CoveragePublicTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoveragePublicTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoveragePublicTest.php',
  ),
  'CoverageTwoDefaultClassAnnotations' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoverageTwoDefaultClassAnnotations.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageTwoDefaultClassAnnotations.php',
  ),
  'CoveredParentClass' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoveredClass.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoveredClass.php',
  ),
  'CoveredClass' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\CoveredClass.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoveredClass.php',
  ),
  'NamespaceCoverageClassExtendedTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NamespaceCoverageClassExtendedTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NamespaceCoverageClassExtendedTest.php',
  ),
  'NamespaceCoverageClassTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NamespaceCoverageClassTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NamespaceCoverageClassTest.php',
  ),
  'NamespaceCoverageCoversClassPublicTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NamespaceCoverageCoversClassPublicTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NamespaceCoverageCoversClassPublicTest.php',
  ),
  'NamespaceCoverageCoversClassTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NamespaceCoverageCoversClassTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NamespaceCoverageCoversClassTest.php',
  ),
  'NamespaceCoverageMethodTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NamespaceCoverageMethodTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NamespaceCoverageMethodTest.php',
  ),
  'NamespaceCoverageNotPrivateTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NamespaceCoverageNotPrivateTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NamespaceCoverageNotPrivateTest.php',
  ),
  'NamespaceCoverageNotProtectedTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NamespaceCoverageNotProtectedTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NamespaceCoverageNotProtectedTest.php',
  ),
  'NamespaceCoverageNotPublicTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NamespaceCoverageNotPublicTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NamespaceCoverageNotPublicTest.php',
  ),
  'NamespaceCoveragePrivateTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NamespaceCoveragePrivateTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NamespaceCoveragePrivateTest.php',
  ),
  'NamespaceCoverageProtectedTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NamespaceCoverageProtectedTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NamespaceCoverageProtectedTest.php',
  ),
  'NamespaceCoveragePublicTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NamespaceCoveragePublicTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NamespaceCoveragePublicTest.php',
  ),
  'Foo\\CoveredParentClass' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NamespaceCoveredClass.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NamespaceCoveredClass.php',
  ),
  'Foo\\CoveredClass' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NamespaceCoveredClass.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NamespaceCoveredClass.php',
  ),
  'NotExistingCoveredElementTest' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\NotExistingCoveredElementTest.php',
    1 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NotExistingCoveredElementTest.php',
  ),
  'Foo' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\source_without_namespace.php',
    1 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\source_with_ignore.php',
    2 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\source_with_oneline_annotations.php',
    3 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\issue30.php',
    4 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\source.php',
    5 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\Foo.php',
  ),
  'CoveredClassWithAnonymousFunctionInStaticMethod' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\source_with_class_and_anonymous_function.php',
  ),
  'Bor' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\source_with_ignore.php',
  ),
  'Bar' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\source_with_ignore.php',
    1 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\Bar.php',
  ),
  'bar\\baz\\source_with_namespace' => 
  array (
    0 => '//vendor\\phpunit\\php-code-coverage\\tests\\_files\\source_with_namespace.php',
  ),
  'File_Iterator_Facade' => 
  array (
    0 => '//vendor\\phpunit\\php-file-iterator\\src\\Facade.php',
  ),
  'File_Iterator_Factory' => 
  array (
    0 => '//vendor\\phpunit\\php-file-iterator\\src\\Factory.php',
  ),
  'File_Iterator' => 
  array (
    0 => '//vendor\\phpunit\\php-file-iterator\\src\\Iterator.php',
  ),
  'Text_Template' => 
  array (
    0 => '//vendor\\phpunit\\php-text-template\\src\\Template.php',
  ),
  'PHP_Timer' => 
  array (
    0 => '//vendor\\phpunit\\php-timer\\src\\Timer.php',
  ),
  'PHP_TimerTest' => 
  array (
    0 => '//vendor\\phpunit\\php-timer\\tests\\TimerTest.php',
  ),
  'PHP_Token_Stream_CachingFactory' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token\\Stream\\CachingFactory.php',
  ),
  'PHP_Token_Stream' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token\\Stream.php',
  ),
  'PHP_Token' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_TokenWithScope' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_TokenWithScopeAndVisibility' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_Includes' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_FUNCTION' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_INTERFACE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ABSTRACT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_AMPERSAND' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_AND_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ARRAY' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ARRAY_CAST' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_AS' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_AT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_BACKTICK' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_BAD_CHARACTER' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_BOOLEAN_AND' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_BOOLEAN_OR' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_BOOL_CAST' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_BREAK' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CARET' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CASE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CATCH' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CHARACTER' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CLASS' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CLASS_C' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CLASS_NAME_CONSTANT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CLONE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CLOSE_BRACKET' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CLOSE_CURLY' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CLOSE_SQUARE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CLOSE_TAG' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_COLON' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_COMMA' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_COMMENT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CONCAT_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CONST' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CONSTANT_ENCAPSED_STRING' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CONTINUE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CURLY_OPEN' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DEC' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DECLARE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DEFAULT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DIV' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DIV_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DNUMBER' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DO' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DOC_COMMENT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DOLLAR' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DOLLAR_OPEN_CURLY_BRACES' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DOT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DOUBLE_ARROW' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DOUBLE_CAST' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DOUBLE_COLON' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DOUBLE_QUOTES' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ECHO' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ELSE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ELSEIF' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_EMPTY' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ENCAPSED_AND_WHITESPACE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ENDDECLARE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ENDFOR' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ENDFOREACH' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ENDIF' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ENDSWITCH' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ENDWHILE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_END_HEREDOC' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_EVAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_EXCLAMATION_MARK' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_EXIT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_EXTENDS' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_FILE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_FINAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_FOR' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_FOREACH' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_FUNC_C' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_GLOBAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_GT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_IF' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_IMPLEMENTS' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_INC' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_INCLUDE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_INCLUDE_ONCE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_INLINE_HTML' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_INSTANCEOF' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_INT_CAST' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ISSET' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_IS_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_IS_GREATER_OR_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_IS_IDENTICAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_IS_NOT_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_IS_NOT_IDENTICAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_IS_SMALLER_OR_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_LINE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_LIST' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_LNUMBER' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_LOGICAL_AND' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_LOGICAL_OR' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_LOGICAL_XOR' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_LT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_METHOD_C' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_MINUS' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_MINUS_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_MOD_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_MULT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_MUL_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_NEW' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_NUM_STRING' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_OBJECT_CAST' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_OBJECT_OPERATOR' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_OPEN_BRACKET' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_OPEN_CURLY' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_OPEN_SQUARE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_OPEN_TAG' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_OPEN_TAG_WITH_ECHO' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_OR_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_PAAMAYIM_NEKUDOTAYIM' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_PERCENT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_PIPE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_PLUS' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_PLUS_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_PRINT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_PRIVATE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_PROTECTED' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_PUBLIC' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_QUESTION_MARK' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_REQUIRE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_REQUIRE_ONCE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_RETURN' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_SEMICOLON' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_SL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_SL_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_SR' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_SR_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_START_HEREDOC' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_STATIC' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_STRING' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_STRING_CAST' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_STRING_VARNAME' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_SWITCH' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_THROW' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_TILDE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_TRY' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_UNSET' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_UNSET_CAST' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_USE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_VAR' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_VARIABLE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_WHILE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_WHITESPACE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_XOR_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_HALT_COMPILER' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_DIR' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_GOTO' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_NAMESPACE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_NS_C' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_NS_SEPARATOR' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_CALLABLE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_INSTEADOF' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_TRAIT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_TRAIT_C' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_FINALLY' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_YIELD' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ELLIPSIS' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_POW' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_POW_EQUAL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_COALESCE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_SPACESHIP' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_YIELD_FROM' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ASYNC' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_AWAIT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_COMPILER_HALT_OFFSET' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ENUM' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_EQUALS' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_IN' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_JOIN' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_LAMBDA_ARROW' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_LAMBDA_CP' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_LAMBDA_OP' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ONUMBER' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_SHAPE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_TYPE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_TYPELIST_GT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_TYPELIST_LT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_WHERE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_XHP_ATTRIBUTE' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_XHP_CATEGORY' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_XHP_CATEGORY_LABEL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_XHP_CHILDREN' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_XHP_LABEL' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_XHP_REQUIRED' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_XHP_TAG_GT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_XHP_TAG_LT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_XHP_TEXT' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\src\\Token.php',
  ),
  'PHP_Token_ClassTest' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\Token\\ClassTest.php',
  ),
  'PHP_Token_ClosureTest' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\Token\\ClosureTest.php',
  ),
  'PHP_Token_FunctionTest' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\Token\\FunctionTest.php',
  ),
  'PHP_Token_IncludeTest' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\Token\\IncludeTest.php',
  ),
  'PHP_Token_InterfaceTest' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\Token\\InterfaceTest.php',
  ),
  'PHP_Token_NamespaceTest' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\Token\\NamespaceTest.php',
  ),
  'PHP_TokenTest' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\TokenTest.php',
  ),
  'Foo\\Bar\\Baz' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\classExtendsNamespacedClass.php',
  ),
  'Foo\\Bar\\Extender' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\classExtendsNamespacedClass.php',
  ),
  'Foo\\Bar\\TestClass' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\classInNamespace.php',
  ),
  'foo' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\class_with_method_that_declares_anonymous_class.php',
  ),
  'class_with_method_that_declares_anonymous_class' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\class_with_method_that_declares_anonymous_class.php',
  ),
  'Test' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\class_with_method_that_declares_anonymous_class2.php',
  ),
  'TestClass' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\issue19.php',
  ),
  'Foo\\Bar\\TestClassInBar' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\multipleNamespacesWithOneClassUsingBraces.php',
  ),
  'Foo\\Bar\\TestClassInBaz' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\multipleNamespacesWithOneClassUsingBraces.php',
  ),
  'iTemplate' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\source4.php',
  ),
  'a' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\source4.php',
    1 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\source5.php',
  ),
  'b' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\source4.php',
  ),
  'c' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\source4.php',
    1 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\source5.php',
  ),
  'i' => 
  array (
    0 => '//vendor\\phpunit\\php-token-stream\\tests\\_fixture\\source5.php',
  ),
  'PHPUnit_Exception' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Exception.php',
  ),
  'PHPUnit_Extensions_GroupTestSuite' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Extensions\\GroupTestSuite.php',
  ),
  'PHPUnit_Extensions_PhptTestCase' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Extensions\\PhptTestCase.php',
  ),
  'PHPUnit_Extensions_PhptTestSuite' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Extensions\\PhptTestSuite.php',
  ),
  'PHPUnit_Extensions_RepeatedTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Extensions\\RepeatedTest.php',
  ),
  'PHPUnit_Extensions_TestDecorator' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Extensions\\TestDecorator.php',
  ),
  'PHPUnit_Extensions_TicketListener' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Extensions\\TicketListener.php',
  ),
  'PHPUnit_Framework_Assert' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Assert.php',
  ),
  'PHPUnit_Framework_AssertionFailedError' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\AssertionFailedError.php',
  ),
  'PHPUnit_Framework_BaseTestListener' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\BaseTestListener.php',
  ),
  'PHPUnit_Framework_CodeCoverageException' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\CodeCoverageException.php',
  ),
  'PHPUnit_Framework_Constraint_And' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\And.php',
  ),
  'PHPUnit_Framework_Constraint_ArrayHasKey' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\ArrayHasKey.php',
  ),
  'PHPUnit_Framework_Constraint_ArraySubset' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\ArraySubset.php',
  ),
  'PHPUnit_Framework_Constraint_Attribute' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\Attribute.php',
  ),
  'PHPUnit_Framework_Constraint_Callback' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\Callback.php',
  ),
  'PHPUnit_Framework_Constraint_ClassHasAttribute' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\ClassHasAttribute.php',
  ),
  'PHPUnit_Framework_Constraint_ClassHasStaticAttribute' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\ClassHasStaticAttribute.php',
  ),
  'PHPUnit_Framework_Constraint_Composite' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\Composite.php',
  ),
  'PHPUnit_Framework_Constraint_Count' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\Count.php',
  ),
  'PHPUnit_Framework_Constraint_Exception' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\Exception.php',
  ),
  'PHPUnit_Framework_Constraint_ExceptionCode' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\ExceptionCode.php',
  ),
  'PHPUnit_Framework_Constraint_ExceptionMessage' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\ExceptionMessage.php',
  ),
  'PHPUnit_Framework_Constraint_ExceptionMessageRegExp' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\ExceptionMessageRegExp.php',
  ),
  'PHPUnit_Framework_Constraint_FileExists' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\FileExists.php',
  ),
  'PHPUnit_Framework_Constraint_GreaterThan' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\GreaterThan.php',
  ),
  'PHPUnit_Framework_Constraint_IsAnything' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\IsAnything.php',
  ),
  'PHPUnit_Framework_Constraint_IsEmpty' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\IsEmpty.php',
  ),
  'PHPUnit_Framework_Constraint_IsEqual' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\IsEqual.php',
  ),
  'PHPUnit_Framework_Constraint_IsFalse' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\IsFalse.php',
  ),
  'PHPUnit_Framework_Constraint_IsFinite' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\IsFinite.php',
  ),
  'PHPUnit_Framework_Constraint_IsIdentical' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\IsIdentical.php',
  ),
  'PHPUnit_Framework_Constraint_IsInfinite' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\IsInfinite.php',
  ),
  'PHPUnit_Framework_Constraint_IsInstanceOf' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\IsInstanceOf.php',
  ),
  'PHPUnit_Framework_Constraint_IsJson' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\IsJson.php',
  ),
  'PHPUnit_Framework_Constraint_IsNan' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\IsNan.php',
  ),
  'PHPUnit_Framework_Constraint_IsNull' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\IsNull.php',
  ),
  'PHPUnit_Framework_Constraint_IsTrue' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\IsTrue.php',
  ),
  'PHPUnit_Framework_Constraint_IsType' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\IsType.php',
  ),
  'PHPUnit_Framework_Constraint_JsonMatches_ErrorMessageProvider' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\JsonMatches\\ErrorMessageProvider.php',
  ),
  'PHPUnit_Framework_Constraint_JsonMatches' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\JsonMatches.php',
  ),
  'PHPUnit_Framework_Constraint_LessThan' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\LessThan.php',
  ),
  'PHPUnit_Framework_Constraint_Not' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\Not.php',
  ),
  'PHPUnit_Framework_Constraint_ObjectHasAttribute' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\ObjectHasAttribute.php',
  ),
  'PHPUnit_Framework_Constraint_Or' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\Or.php',
  ),
  'PHPUnit_Framework_Constraint_PCREMatch' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\PCREMatch.php',
  ),
  'PHPUnit_Framework_Constraint_SameSize' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\SameSize.php',
  ),
  'PHPUnit_Framework_Constraint_StringContains' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\StringContains.php',
  ),
  'PHPUnit_Framework_Constraint_StringEndsWith' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\StringEndsWith.php',
  ),
  'PHPUnit_Framework_Constraint_StringMatches' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\StringMatches.php',
  ),
  'PHPUnit_Framework_Constraint_StringStartsWith' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\StringStartsWith.php',
  ),
  'PHPUnit_Framework_Constraint_TraversableContains' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\TraversableContains.php',
  ),
  'PHPUnit_Framework_Constraint_TraversableContainsOnly' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\TraversableContainsOnly.php',
  ),
  'PHPUnit_Framework_Constraint_Xor' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint\\Xor.php',
  ),
  'PHPUnit_Framework_Constraint' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Constraint.php',
  ),
  'PHPUnit_Framework_Error_Deprecated' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Error\\Deprecated.php',
  ),
  'PHPUnit_Framework_Error_Notice' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Error\\Notice.php',
  ),
  'PHPUnit_Framework_Error_Warning' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Error\\Warning.php',
  ),
  'PHPUnit_Framework_Error' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Error.php',
  ),
  'PHPUnit_Framework_Exception' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Exception.php',
  ),
  'PHPUnit_Framework_ExceptionWrapper' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\ExceptionWrapper.php',
  ),
  'PHPUnit_Framework_ExpectationFailedException' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\ExpectationFailedException.php',
  ),
  'PHPUnit_Framework_IncompleteTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\IncompleteTest.php',
  ),
  'PHPUnit_Framework_IncompleteTestCase' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\IncompleteTestCase.php',
  ),
  'PHPUnit_Framework_IncompleteTestError' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\IncompleteTestError.php',
  ),
  'PHPUnit_Framework_InvalidCoversTargetError' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\InvalidCoversTargetError.php',
  ),
  'PHPUnit_Framework_InvalidCoversTargetException' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\InvalidCoversTargetException.php',
  ),
  'PHPUnit_Framework_OutputError' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\OutputError.php',
  ),
  'PHPUnit_Framework_RiskyTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\RiskyTest.php',
  ),
  'PHPUnit_Framework_RiskyTestError' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\RiskyTestError.php',
  ),
  'PHPUnit_Framework_SelfDescribing' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\SelfDescribing.php',
  ),
  'PHPUnit_Framework_SkippedTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\SkippedTest.php',
  ),
  'PHPUnit_Framework_SkippedTestCase' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\SkippedTestCase.php',
  ),
  'PHPUnit_Framework_SkippedTestError' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\SkippedTestError.php',
  ),
  'PHPUnit_Framework_SkippedTestSuiteError' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\SkippedTestSuiteError.php',
  ),
  'PHPUnit_Framework_SyntheticError' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\SyntheticError.php',
  ),
  'PHPUnit_Framework_Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Test.php',
  ),
  'PHPUnit_Framework_TestCase' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\TestCase.php',
  ),
  'PHPUnit_Framework_TestFailure' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\TestFailure.php',
  ),
  'PHPUnit_Framework_TestListener' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\TestListener.php',
  ),
  'PHPUnit_Framework_TestResult' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\TestResult.php',
  ),
  'PHPUnit_Framework_TestSuite_DataProvider' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\TestSuite\\DataProvider.php',
  ),
  'PHPUnit_Framework_TestSuite' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\TestSuite.php',
  ),
  'PHPUnit_Framework_UnintentionallyCoveredCodeError' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\UnintentionallyCoveredCodeError.php',
  ),
  'PHPUnit_Framework_Warning' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Framework\\Warning.php',
  ),
  'PHPUnit_Runner_BaseTestRunner' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Runner\\BaseTestRunner.php',
  ),
  'PHPUnit_Runner_Exception' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Runner\\Exception.php',
  ),
  'PHPUnit_Runner_Filter_Factory' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Runner\\Filter\\Factory.php',
  ),
  'PHPUnit_Runner_Filter_Group_Exclude' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Runner\\Filter\\Group\\Exclude.php',
  ),
  'PHPUnit_Runner_Filter_Group_Include' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Runner\\Filter\\Group\\Include.php',
  ),
  'PHPUnit_Runner_Filter_GroupFilterIterator' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Runner\\Filter\\Group.php',
  ),
  'PHPUnit_Runner_Filter_Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Runner\\Filter\\Test.php',
  ),
  'PHPUnit_Runner_StandardTestSuiteLoader' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Runner\\StandardTestSuiteLoader.php',
  ),
  'PHPUnit_Runner_TestSuiteLoader' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Runner\\TestSuiteLoader.php',
  ),
  'PHPUnit_Runner_Version' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Runner\\Version.php',
  ),
  'PHPUnit_TextUI_Command' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\TextUI\\Command.php',
  ),
  'PHPUnit_TextUI_ResultPrinter' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\TextUI\\ResultPrinter.php',
  ),
  'PHPUnit_TextUI_TestRunner' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\TextUI\\TestRunner.php',
  ),
  'PHPUnit_Util_Blacklist' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Blacklist.php',
  ),
  'PHPUnit_Util_Configuration' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Configuration.php',
  ),
  'PHPUnit_Util_ErrorHandler' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\ErrorHandler.php',
  ),
  'PHPUnit_Util_Fileloader' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Fileloader.php',
  ),
  'PHPUnit_Util_Filesystem' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Filesystem.php',
  ),
  'PHPUnit_Util_Filter' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Filter.php',
  ),
  'PHPUnit_Util_Getopt' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Getopt.php',
  ),
  'PHPUnit_Util_GlobalState' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\GlobalState.php',
  ),
  'PHPUnit_Util_InvalidArgumentHelper' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\InvalidArgumentHelper.php',
  ),
  'PHPUnit_Util_Log_JSON' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Log\\JSON.php',
  ),
  'PHPUnit_Util_Log_JUnit' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Log\\JUnit.php',
  ),
  'PHPUnit_Util_Log_TAP' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Log\\TAP.php',
  ),
  'PHPUnit_Util_Log_TeamCity' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Log\\TeamCity.php',
  ),
  'PHPUnit_Util_PHP_Default' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\PHP\\Default.php',
  ),
  'PHPUnit_Util_PHP_Windows' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\PHP\\Windows.php',
  ),
  'PHPUnit_Util_PHP' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\PHP.php',
  ),
  'PHPUnit_Util_Printer' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Printer.php',
  ),
  'PHPUnit_Util_Regex' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Regex.php',
  ),
  'PHPUnit_Util_String' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\String.php',
  ),
  'PHPUnit_Util_Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Test.php',
  ),
  'PHPUnit_Util_TestDox_NamePrettifier' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\TestDox\\NamePrettifier.php',
  ),
  'PHPUnit_Util_TestDox_ResultPrinter_HTML' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\TestDox\\ResultPrinter\\HTML.php',
  ),
  'PHPUnit_Util_TestDox_ResultPrinter_Text' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\TestDox\\ResultPrinter\\Text.php',
  ),
  'PHPUnit_Util_TestDox_ResultPrinter' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\TestDox\\ResultPrinter.php',
  ),
  'PHPUnit_Util_TestSuiteIterator' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\TestSuiteIterator.php',
  ),
  'PHPUnit_Util_Type' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\Type.php',
  ),
  'PHPUnit_Util_XML' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\src\\Util\\XML.php',
  ),
  'Extensions_PhptTestCaseTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Extensions\\PhptTestCaseTest.php',
  ),
  'PhpTestCaseProxy' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Extensions\\PhptTestCaseTest.php',
  ),
  'Extensions_RepeatedTestTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Extensions\\RepeatedTestTest.php',
  ),
  'Framework_AssertTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\AssertTest.php',
  ),
  'Framework_BaseTestListenerTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\BaseTestListenerTest.php',
  ),
  'CountTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\Constraint\\CountTest.php',
  ),
  'ExceptionMessageRegExpTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\Constraint\\ExceptionMessageRegExpTest.php',
  ),
  'ExceptionMessageTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\Constraint\\ExceptionMessageTest.php',
  ),
  'Framework_Constraint_IsJsonTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\Constraint\\IsJsonTest.php',
  ),
  'Framework_Constraint_JsonMatches_ErrorMessageProviderTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\Constraint\\JsonMatches\\ErrorMessageProviderTest.php',
  ),
  'Framework_Constraint_JsonMatchesTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\Constraint\\JsonMatchesTest.php',
  ),
  'Framework_ConstraintTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\ConstraintTest.php',
  ),
  'Framework_SuiteTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\SuiteTest.php',
  ),
  'Framework_TestCaseTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\TestCaseTest.php',
  ),
  'Framework_TestFailureTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\TestFailureTest.php',
  ),
  'Framework_TestImplementorTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\TestImplementorTest.php',
  ),
  'Framework_TestListenerTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Framework\\TestListenerTest.php',
  ),
  'Issue1021Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\1021\\Issue1021Test.php',
  ),
  'Issue523Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\523\\Issue523Test.php',
  ),
  'Issue523' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\523\\Issue523Test.php',
  ),
  'Issue578Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\578\\Issue578Test.php',
  ),
  'Foo_Bar_Issue684Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\684\\Issue684Test.php',
  ),
  'ChildSuite' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\783\\ChildSuite.php',
  ),
  'OneTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\783\\OneTest.php',
  ),
  'ParentSuite' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\783\\ParentSuite.php',
  ),
  'TwoTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\783\\TwoTest.php',
  ),
  'Issue1149Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1149\\Issue1149Test.php',
  ),
  'Issue1216Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1216\\Issue1216Test.php',
  ),
  'Issue1265Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1265\\Issue1265Test.php',
  ),
  'Issue1330Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1330\\Issue1330Test.php',
  ),
  'Issue1335Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1335\\Issue1335Test.php',
  ),
  'Issue1337Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1337\\Issue1337Test.php',
  ),
  'Issue1348Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1348\\Issue1348Test.php',
  ),
  'ChildProcessClass1351' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1351\\ChildProcessClass1351.php',
  ),
  'Issue1351Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1351\\Issue1351Test.php',
  ),
  'Issue1374Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1374\\Issue1374Test.php',
  ),
  'Issue1437Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1437\\Issue1437Test.php',
  ),
  'Issue1468Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1468\\Issue1468Test.php',
  ),
  'Issue1471Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1471\\Issue1471Test.php',
  ),
  'Issue1472Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1472\\Issue1472Test.php',
  ),
  'Issue1570Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\1570\\Issue1570Test.php',
  ),
  'Issue244Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\244\\Issue244Test.php',
  ),
  'Issue244Exception' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\244\\Issue244Test.php',
  ),
  'Issue244ExceptionIntCode' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\244\\Issue244Test.php',
  ),
  'Issue322Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\322\\Issue322Test.php',
  ),
  'Issue433Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\433\\Issue433Test.php',
  ),
  'Issue445Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\445\\Issue445Test.php',
  ),
  'Issue498Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\498\\Issue498Test.php',
  ),
  'Issue503Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\503\\Issue503Test.php',
  ),
  'Issue581Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\581\\Issue581Test.php',
  ),
  'Issue74Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\74\\Issue74Test.php',
  ),
  'NewException' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\74\\NewException.php',
  ),
  'Issue765Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\765\\Issue765Test.php',
  ),
  'Issue797Test' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Regression\\GitHub\\797\\Issue797Test.php',
  ),
  'Runner_BaseTestRunnerTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Runner\\BaseTestRunnerTest.php',
  ),
  'Util_ConfigurationTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Util\\ConfigurationTest.php',
  ),
  'Util_GetoptTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Util\\GetoptTest.php',
  ),
  'Util_GlobalStateTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Util\\GlobalStateTest.php',
  ),
  'Util_RegexTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Util\\RegexTest.php',
  ),
  'Util_TestDox_NamePrettifierTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Util\\TestDox\\NamePrettifierTest.php',
  ),
  'Util_TestTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Util\\TestTest.php',
  ),
  'Util_XMLTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\Util\\XMLTest.php',
  ),
  'AbstractTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\AbstractTest.php',
  ),
  'Author' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\Author.php',
  ),
  'BankAccountException' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\BankAccount.php',
  ),
  'BankAccountWithCustomExtensionTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\BankAccountTest.test.php',
  ),
  'BaseTestListenerSample' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\BaseTestListenerSample.php',
  ),
  'BeforeAndAfterTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\BeforeAndAfterTest.php',
  ),
  'BeforeClassAndAfterClassTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\BeforeClassAndAfterClassTest.php',
  ),
  'Book' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\Book.php',
  ),
  'Calculator' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\Calculator.php',
  ),
  'ChangeCurrentWorkingDirectoryTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ChangeCurrentWorkingDirectoryTest.php',
  ),
  'ParentClassWithPrivateAttributes' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ClassWithNonPublicAttributes.php',
  ),
  'ParentClassWithProtectedAttributes' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ClassWithNonPublicAttributes.php',
  ),
  'ClassWithNonPublicAttributes' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ClassWithNonPublicAttributes.php',
  ),
  'ClassWithScalarTypeDeclarations' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ClassWithScalarTypeDeclarations.php',
  ),
  'ClassWithToString' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ClassWithToString.php',
  ),
  'ClonedDependencyTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ClonedDependencyTest.php',
  ),
  'ConcreteWithMyCustomExtensionTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ConcreteTest.my.php',
  ),
  'ConcreteTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ConcreteTest.php',
  ),
  'CoverageNamespacedFunctionTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CoverageNamespacedFunctionTest.php',
  ),
  'CustomPrinter' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\CustomPrinter.php',
  ),
  'DataProviderDebugTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\DataProviderDebugTest.php',
  ),
  'DataProviderFilterTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\DataProviderFilterTest.php',
  ),
  'DataProviderIncompleteTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\DataProviderIncompleteTest.php',
  ),
  'DataProviderSkippedTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\DataProviderSkippedTest.php',
  ),
  'DataProviderTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\DataProviderTest.php',
  ),
  'DependencyFailureTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\DependencyFailureTest.php',
  ),
  'DependencySuccessTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\DependencySuccessTest.php',
  ),
  'DependencyTestSuite' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\DependencyTestSuite.php',
  ),
  'DoubleTestCase' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\DoubleTestCase.php',
  ),
  'DummyException' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\DummyException.php',
  ),
  'EmptyTestCaseTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\EmptyTestCaseTest.php',
  ),
  'ExceptionInAssertPostConditionsTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ExceptionInAssertPostConditionsTest.php',
  ),
  'ExceptionInAssertPreConditionsTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ExceptionInAssertPreConditionsTest.php',
  ),
  'ExceptionInSetUpTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ExceptionInSetUpTest.php',
  ),
  'ExceptionInTearDownTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ExceptionInTearDownTest.php',
  ),
  'ExceptionInTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ExceptionInTest.php',
  ),
  'My\\Space\\ExceptionNamespaceTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ExceptionNamespaceTest.php',
  ),
  'ExceptionStackTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ExceptionStackTest.php',
  ),
  'ExceptionTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ExceptionTest.php',
  ),
  'Failure' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\Failure.php',
  ),
  'FailureTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\FailureTest.php',
  ),
  'FatalTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\FatalTest.php',
  ),
  'IncompleteTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\IncompleteTest.php',
  ),
  'InheritanceA' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\Inheritance\\InheritanceA.php',
  ),
  'InheritanceB' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\Inheritance\\InheritanceB.php',
  ),
  'InheritedTestCase' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\InheritedTestCase.php',
  ),
  'IniTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\IniTest.php',
  ),
  'IsolationTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\IsolationTest.php',
  ),
  'MockRunner' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\MockRunner.php',
  ),
  'MultiDependencyTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\MultiDependencyTest.php',
  ),
  'NoArgTestCaseTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NoArgTestCaseTest.php',
  ),
  'NonStatic' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NonStatic.php',
  ),
  'NoTestCaseClass' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NoTestCaseClass.php',
  ),
  'NoTestCases' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NoTestCases.php',
  ),
  'NothingTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NothingTest.php',
  ),
  'NotPublicTestCase' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NotPublicTestCase.php',
  ),
  'NotVoidTestCase' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\NotVoidTestCase.php',
  ),
  'OneTestCase' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\OneTestCase.php',
  ),
  'OutputTestCase' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\OutputTestCase.php',
  ),
  'OverrideTestCase' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\OverrideTestCase.php',
  ),
  'RequirementsClassBeforeClassHookTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\RequirementsClassBeforeClassHookTest.php',
  ),
  'RequirementsClassDocBlockTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\RequirementsClassDocBlockTest.php',
  ),
  'RequirementsTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\RequirementsTest.php',
  ),
  'SampleArrayAccess' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\SampleArrayAccess.php',
  ),
  'SampleClass' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\SampleClass.php',
  ),
  'Singleton' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\Singleton.php',
  ),
  'StackTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\StackTest.php',
  ),
  'Struct' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\Struct.php',
  ),
  'Success' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\Success.php',
  ),
  'TemplateMethodsTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\TemplateMethodsTest.php',
  ),
  'TestIncomplete' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\TestIncomplete.php',
  ),
  'TestIterator' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\TestIterator.php',
  ),
  'TestIterator2' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\TestIterator2.php',
  ),
  'TestSkipped' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\TestSkipped.php',
  ),
  'TestError' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\TestTestError.php',
  ),
  'TestWithTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\TestWithTest.php',
  ),
  'ThrowExceptionTestCase' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ThrowExceptionTestCase.php',
  ),
  'ThrowNoExceptionTestCase' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\ThrowNoExceptionTestCase.php',
  ),
  'WasRun' => 
  array (
    0 => '//vendor\\phpunit\\phpunit\\tests\\_files\\WasRun.php',
  ),
  'PHPUnit_Framework_MockObject_Builder_Identity' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Builder\\Identity.php',
  ),
  'PHPUnit_Framework_MockObject_Builder_InvocationMocker' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Builder\\InvocationMocker.php',
  ),
  'PHPUnit_Framework_MockObject_Builder_Match' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Builder\\Match.php',
  ),
  'PHPUnit_Framework_MockObject_Builder_MethodNameMatch' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Builder\\MethodNameMatch.php',
  ),
  'PHPUnit_Framework_MockObject_Builder_Namespace' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Builder\\Namespace.php',
  ),
  'PHPUnit_Framework_MockObject_Builder_ParametersMatch' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Builder\\ParametersMatch.php',
  ),
  'PHPUnit_Framework_MockObject_Builder_Stub' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Builder\\Stub.php',
  ),
  'PHPUnit_Framework_MockObject_BadMethodCallException' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Exception\\BadMethodCallException.php',
  ),
  'PHPUnit_Framework_MockObject_Exception' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Exception\\Exception.php',
  ),
  'PHPUnit_Framework_MockObject_RuntimeException' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Exception\\RuntimeException.php',
  ),
  'PHPUnit_Framework_MockObject_Generator' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Generator.php',
  ),
  'PHPUnit_Framework_MockObject_Invocation_Object' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Invocation\\Object.php',
  ),
  'PHPUnit_Framework_MockObject_Invocation_Static' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Invocation\\Static.php',
  ),
  'PHPUnit_Framework_MockObject_Invocation' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Invocation.php',
  ),
  'PHPUnit_Framework_MockObject_InvocationMocker' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\InvocationMocker.php',
  ),
  'PHPUnit_Framework_MockObject_Invokable' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Invokable.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher\\AnyInvokedCount.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher_AnyParameters' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher\\AnyParameters.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher_ConsecutiveParameters' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher\\ConsecutiveParameters.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher_Invocation' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher\\Invocation.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher_InvokedAtIndex' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher\\InvokedAtIndex.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher_InvokedAtLeastCount' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher\\InvokedAtLeastCount.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher_InvokedAtLeastOnce' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher\\InvokedAtLeastOnce.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher_InvokedAtMostCount' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher\\InvokedAtMostCount.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher_InvokedCount' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher\\InvokedCount.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher_InvokedRecorder' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher\\InvokedRecorder.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher_MethodName' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher\\MethodName.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher_Parameters' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher\\Parameters.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher_StatelessInvocation' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher\\StatelessInvocation.php',
  ),
  'PHPUnit_Framework_MockObject_Matcher' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Matcher.php',
  ),
  'PHPUnit_Framework_MockObject_MockBuilder' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\MockBuilder.php',
  ),
  'PHPUnit_Framework_MockObject_MockObject' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\MockObject.php',
  ),
  'PHPUnit_Framework_MockObject_Stub_ConsecutiveCalls' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Stub\\ConsecutiveCalls.php',
  ),
  'PHPUnit_Framework_MockObject_Stub_Exception' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Stub\\Exception.php',
  ),
  'PHPUnit_Framework_MockObject_Stub_MatcherCollection' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Stub\\MatcherCollection.php',
  ),
  'PHPUnit_Framework_MockObject_Stub_Return' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Stub\\Return.php',
  ),
  'PHPUnit_Framework_MockObject_Stub_ReturnArgument' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Stub\\ReturnArgument.php',
  ),
  'PHPUnit_Framework_MockObject_Stub_ReturnCallback' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Stub\\ReturnCallback.php',
  ),
  'PHPUnit_Framework_MockObject_Stub_ReturnSelf' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Stub\\ReturnSelf.php',
  ),
  'PHPUnit_Framework_MockObject_Stub_ReturnValueMap' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Stub\\ReturnValueMap.php',
  ),
  'PHPUnit_Framework_MockObject_Stub' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Stub.php',
  ),
  'PHPUnit_Framework_MockObject_Verifiable' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\src\\Framework\\MockObject\\Verifiable.php',
  ),
  'Framework_MockObject_GeneratorTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\GeneratorTest.php',
  ),
  'Framework_MockBuilderTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\MockBuilderTest.php',
  ),
  'Framework_MockObject_Builder_InvocationMockerTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\MockObject\\Builder\\InvocationMockerTest.php',
  ),
  'Framework_MockObject_Invocation_ObjectTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\MockObject\\Invocation\\ObjectTest.php',
  ),
  'Framework_MockObject_Invocation_StaticTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\MockObject\\Invocation\\StaticTest.php',
  ),
  'Framework_MockObject_Matcher_ConsecutiveParametersTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\MockObject\\Matcher\\ConsecutiveParametersTest.php',
  ),
  'Framework_MockObjectTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\MockObjectTest.php',
  ),
  'Framework_ProxyObjectTest' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\ProxyObjectTest.php',
  ),
  'AbstractMockTestClass' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\AbstractMockTestClass.php',
  ),
  'AbstractTrait' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\AbstractTrait.php',
  ),
  'AnInterface' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\AnInterface.php',
  ),
  'AnotherInterface' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\AnotherInterface.php',
  ),
  'ClassThatImplementsSerializable' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\ClassThatImplementsSerializable.php',
  ),
  'ClassWithStaticMethod' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\ClassWithStaticMethod.php',
  ),
  'InterfaceWithSemiReservedMethodName' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\InterfaceWithSemiReservedMethodName.php',
  ),
  'InterfaceWithStaticMethod' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\InterfaceWithStaticMethod.php',
  ),
  'MethodCallback' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\MethodCallback.php',
  ),
  'MethodCallbackByReference' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\MethodCallbackByReference.php',
  ),
  'Mockable' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\Mockable.php',
  ),
  'MockTestInterface' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\MockTestInterface.php',
  ),
  'PartialMockTestClass' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\PartialMockTestClass.php',
  ),
  'SingletonClass' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\SingletonClass.php',
  ),
  'SomeClass' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\SomeClass.php',
  ),
  'StaticMockTestClass' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\StaticMockTestClass.php',
  ),
  'StringableClass' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\StringableClass.php',
  ),
  'TraversableMockTestInterface' => 
  array (
    0 => '//vendor\\phpunit\\phpunit-mock-objects\\tests\\_fixture\\TraversableMockTestInterface.php',
  ),
  'Psr\\Http\\Message\\MessageInterface' => 
  array (
    0 => '//vendor\\psr\\http-message\\src\\MessageInterface.php',
  ),
  'Psr\\Http\\Message\\RequestInterface' => 
  array (
    0 => '//vendor\\psr\\http-message\\src\\RequestInterface.php',
  ),
  'Psr\\Http\\Message\\ResponseInterface' => 
  array (
    0 => '//vendor\\psr\\http-message\\src\\ResponseInterface.php',
  ),
  'Psr\\Http\\Message\\ServerRequestInterface' => 
  array (
    0 => '//vendor\\psr\\http-message\\src\\ServerRequestInterface.php',
  ),
  'Psr\\Http\\Message\\StreamInterface' => 
  array (
    0 => '//vendor\\psr\\http-message\\src\\StreamInterface.php',
  ),
  'Psr\\Http\\Message\\UploadedFileInterface' => 
  array (
    0 => '//vendor\\psr\\http-message\\src\\UploadedFileInterface.php',
  ),
  'Psr\\Http\\Message\\UriInterface' => 
  array (
    0 => '//vendor\\psr\\http-message\\src\\UriInterface.php',
  ),
  'Psr\\Log\\AbstractLogger' => 
  array (
    0 => '//vendor\\psr\\log\\Psr\\Log\\AbstractLogger.php',
  ),
  'Psr\\Log\\InvalidArgumentException' => 
  array (
    0 => '//vendor\\psr\\log\\Psr\\Log\\InvalidArgumentException.php',
  ),
  'Psr\\Log\\LoggerAwareInterface' => 
  array (
    0 => '//vendor\\psr\\log\\Psr\\Log\\LoggerAwareInterface.php',
  ),
  'Psr\\Log\\LoggerAwareTrait' => 
  array (
    0 => '//vendor\\psr\\log\\Psr\\Log\\LoggerAwareTrait.php',
  ),
  'Psr\\Log\\LoggerInterface' => 
  array (
    0 => '//vendor\\psr\\log\\Psr\\Log\\LoggerInterface.php',
  ),
  'Psr\\Log\\LoggerTrait' => 
  array (
    0 => '//vendor\\psr\\log\\Psr\\Log\\LoggerTrait.php',
  ),
  'Psr\\Log\\LogLevel' => 
  array (
    0 => '//vendor\\psr\\log\\Psr\\Log\\LogLevel.php',
  ),
  'Psr\\Log\\NullLogger' => 
  array (
    0 => '//vendor\\psr\\log\\Psr\\Log\\NullLogger.php',
  ),
  'Psr\\Log\\Test\\LoggerInterfaceTest' => 
  array (
    0 => '//vendor\\psr\\log\\Psr\\Log\\Test\\LoggerInterfaceTest.php',
  ),
  'Psr\\Log\\Test\\DummyTest' => 
  array (
    0 => '//vendor\\psr\\log\\Psr\\Log\\Test\\LoggerInterfaceTest.php',
  ),
  'SebastianBergmann\\Comparator\\ArrayComparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\ArrayComparator.php',
  ),
  'SebastianBergmann\\Comparator\\Comparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\Comparator.php',
  ),
  'SebastianBergmann\\Comparator\\ComparisonFailure' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\ComparisonFailure.php',
  ),
  'SebastianBergmann\\Comparator\\DateTimeComparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\DateTimeComparator.php',
  ),
  'SebastianBergmann\\Comparator\\DOMNodeComparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\DOMNodeComparator.php',
  ),
  'SebastianBergmann\\Comparator\\DoubleComparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\DoubleComparator.php',
  ),
  'SebastianBergmann\\Comparator\\ExceptionComparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\ExceptionComparator.php',
  ),
  'SebastianBergmann\\Comparator\\Factory' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\Factory.php',
  ),
  'SebastianBergmann\\Comparator\\MockObjectComparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\MockObjectComparator.php',
  ),
  'SebastianBergmann\\Comparator\\NumericComparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\NumericComparator.php',
  ),
  'SebastianBergmann\\Comparator\\ObjectComparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\ObjectComparator.php',
  ),
  'SebastianBergmann\\Comparator\\ResourceComparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\ResourceComparator.php',
  ),
  'SebastianBergmann\\Comparator\\ScalarComparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\ScalarComparator.php',
  ),
  'SebastianBergmann\\Comparator\\SplObjectStorageComparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\SplObjectStorageComparator.php',
  ),
  'SebastianBergmann\\Comparator\\TypeComparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\src\\TypeComparator.php',
  ),
  'SebastianBergmann\\Comparator\\ArrayComparatorTest' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\ArrayComparatorTest.php',
  ),
  'SebastianBergmann\\Comparator\\DateTimeComparatorTest' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\DateTimeComparatorTest.php',
  ),
  'SebastianBergmann\\Comparator\\DOMNodeComparatorTest' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\DOMNodeComparatorTest.php',
  ),
  'SebastianBergmann\\Comparator\\DoubleComparatorTest' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\DoubleComparatorTest.php',
  ),
  'SebastianBergmann\\Comparator\\ExceptionComparatorTest' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\ExceptionComparatorTest.php',
  ),
  'SebastianBergmann\\Comparator\\FactoryTest' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\FactoryTest.php',
  ),
  'SebastianBergmann\\Comparator\\MockObjectComparatorTest' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\MockObjectComparatorTest.php',
  ),
  'SebastianBergmann\\Comparator\\NumericComparatorTest' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\NumericComparatorTest.php',
  ),
  'SebastianBergmann\\Comparator\\ObjectComparatorTest' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\ObjectComparatorTest.php',
  ),
  'SebastianBergmann\\Comparator\\ResourceComparatorTest' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\ResourceComparatorTest.php',
  ),
  'SebastianBergmann\\Comparator\\ScalarComparatorTest' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\ScalarComparatorTest.php',
  ),
  'SebastianBergmann\\Comparator\\SplObjectStorageComparatorTest' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\SplObjectStorageComparatorTest.php',
  ),
  'SebastianBergmann\\Comparator\\TypeComparatorTest' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\TypeComparatorTest.php',
  ),
  'SebastianBergmann\\Comparator\\Author' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\_files\\Author.php',
  ),
  'SebastianBergmann\\Comparator\\Book' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\_files\\Book.php',
  ),
  'SebastianBergmann\\Comparator\\ClassWithToString' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\_files\\ClassWithToString.php',
  ),
  'SebastianBergmann\\Comparator\\SampleClass' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\_files\\SampleClass.php',
  ),
  'SebastianBergmann\\Comparator\\Struct' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\_files\\Struct.php',
  ),
  'SebastianBergmann\\Comparator\\TestClass' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\_files\\TestClass.php',
  ),
  'SebastianBergmann\\Comparator\\TestClassComparator' => 
  array (
    0 => '//vendor\\sebastian\\comparator\\tests\\_files\\TestClassComparator.php',
  ),
  'SebastianBergmann\\Diff\\Chunk' => 
  array (
    0 => '//vendor\\sebastian\\diff\\src\\Chunk.php',
  ),
  'SebastianBergmann\\Diff\\Diff' => 
  array (
    0 => '//vendor\\sebastian\\diff\\src\\Diff.php',
  ),
  'SebastianBergmann\\Diff\\Differ' => 
  array (
    0 => '//vendor\\sebastian\\diff\\src\\Differ.php',
  ),
  'SebastianBergmann\\Diff\\LCS\\LongestCommonSubsequence' => 
  array (
    0 => '//vendor\\sebastian\\diff\\src\\LCS\\LongestCommonSubsequence.php',
  ),
  'SebastianBergmann\\Diff\\LCS\\MemoryEfficientImplementation' => 
  array (
    0 => '//vendor\\sebastian\\diff\\src\\LCS\\MemoryEfficientLongestCommonSubsequenceImplementation.php',
  ),
  'SebastianBergmann\\Diff\\LCS\\TimeEfficientImplementation' => 
  array (
    0 => '//vendor\\sebastian\\diff\\src\\LCS\\TimeEfficientLongestCommonSubsequenceImplementation.php',
  ),
  'SebastianBergmann\\Diff\\Line' => 
  array (
    0 => '//vendor\\sebastian\\diff\\src\\Line.php',
  ),
  'SebastianBergmann\\Diff\\Parser' => 
  array (
    0 => '//vendor\\sebastian\\diff\\src\\Parser.php',
  ),
  'SebastianBergmann\\Diff\\DifferTest' => 
  array (
    0 => '//vendor\\sebastian\\diff\\tests\\DifferTest.php',
  ),
  'SebastianBergmann\\Diff\\LCS\\TimeEfficientImplementationTest' => 
  array (
    0 => '//vendor\\sebastian\\diff\\tests\\LCS\\TimeEfficientImplementationTest.php',
  ),
  'SebastianBergmann\\Diff\\ParserTest' => 
  array (
    0 => '//vendor\\sebastian\\diff\\tests\\ParserTest.php',
  ),
  'SebastianBergmann\\Environment\\Console' => 
  array (
    0 => '//vendor\\sebastian\\environment\\src\\Console.php',
  ),
  'SebastianBergmann\\Environment\\Runtime' => 
  array (
    0 => '//vendor\\sebastian\\environment\\src\\Runtime.php',
  ),
  'SebastianBergmann\\Environment\\ConsoleTest' => 
  array (
    0 => '//vendor\\sebastian\\environment\\tests\\ConsoleTest.php',
  ),
  'SebastianBergmann\\Environment\\RuntimeTest' => 
  array (
    0 => '//vendor\\sebastian\\environment\\tests\\RuntimeTest.php',
  ),
  'SebastianBergmann\\Exporter\\Exporter' => 
  array (
    0 => '//vendor\\sebastian\\exporter\\src\\Exporter.php',
  ),
  'SebastianBergmann\\Exporter\\ExporterTest' => 
  array (
    0 => '//vendor\\sebastian\\exporter\\tests\\ExporterTest.php',
  ),
  'SebastianBergmann\\GlobalState\\Blacklist' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\src\\Blacklist.php',
  ),
  'SebastianBergmann\\GlobalState\\CodeExporter' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\src\\CodeExporter.php',
  ),
  'SebastianBergmann\\GlobalState\\Exception' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\src\\Exception.php',
  ),
  'SebastianBergmann\\GlobalState\\Restorer' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\src\\Restorer.php',
  ),
  'SebastianBergmann\\GlobalState\\RuntimeException' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\src\\RuntimeException.php',
  ),
  'SebastianBergmann\\GlobalState\\Snapshot' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\src\\Snapshot.php',
  ),
  'SebastianBergmann\\GlobalState\\BlacklistTest' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\tests\\BlacklistTest.php',
  ),
  'SebastianBergmann\\GlobalState\\SnapshotTest' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\tests\\SnapshotTest.php',
  ),
  'SebastianBergmann\\GlobalState\\TestFixture\\BlacklistedChildClass' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\tests\\_fixture\\BlacklistedChildClass.php',
  ),
  'SebastianBergmann\\GlobalState\\TestFixture\\BlacklistedClass' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\tests\\_fixture\\BlacklistedClass.php',
  ),
  'SebastianBergmann\\GlobalState\\TestFixture\\BlacklistedImplementor' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\tests\\_fixture\\BlacklistedImplementor.php',
  ),
  'SebastianBergmann\\GlobalState\\TestFixture\\BlacklistedInterface' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\tests\\_fixture\\BlacklistedInterface.php',
  ),
  'SebastianBergmann\\GlobalState\\TestFixture\\SnapshotClass' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\tests\\_fixture\\SnapshotClass.php',
  ),
  'SebastianBergmann\\GlobalState\\TestFixture\\SnapshotDomDocument' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\tests\\_fixture\\SnapshotDomDocument.php',
  ),
  'SebastianBergmann\\GlobalState\\TestFixture\\SnapshotTrait' => 
  array (
    0 => '//vendor\\sebastian\\global-state\\tests\\_fixture\\SnapshotTrait.php',
  ),
  'SebastianBergmann\\RecursionContext\\Context' => 
  array (
    0 => '//vendor\\sebastian\\recursion-context\\src\\Context.php',
  ),
  'SebastianBergmann\\RecursionContext\\Exception' => 
  array (
    0 => '//vendor\\sebastian\\recursion-context\\src\\Exception.php',
  ),
  'SebastianBergmann\\RecursionContext\\InvalidArgumentException' => 
  array (
    0 => '//vendor\\sebastian\\recursion-context\\src\\InvalidArgumentException.php',
  ),
  'SebastianBergmann\\RecursionContext\\ContextTest' => 
  array (
    0 => '//vendor\\sebastian\\recursion-context\\tests\\ContextTest.php',
  ),
  'SebastianBergmann\\ResourceOperations\\ResourceOperations' => 
  array (
    0 => '//vendor\\sebastian\\resource-operations\\build\\generate.php',
    1 => '//vendor\\sebastian\\resource-operations\\src\\ResourceOperations.php',
  ),
  'SebastianBergmann\\Version' => 
  array (
    0 => '//vendor\\sebastian\\version\\src\\Version.php',
  ),
  'Swift_Attachment' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Attachment.php',
  ),
  'Swift_ByteStream_AbstractFilterableInputStream' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\ByteStream\\AbstractFilterableInputStream.php',
  ),
  'Swift_ByteStream_ArrayByteStream' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\ByteStream\\ArrayByteStream.php',
  ),
  'Swift_ByteStream_FileByteStream' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\ByteStream\\FileByteStream.php',
  ),
  'Swift_ByteStream_TemporaryFileByteStream' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\ByteStream\\TemporaryFileByteStream.php',
  ),
  'Swift_CharacterReader_GenericFixedWidthReader' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\CharacterReader\\GenericFixedWidthReader.php',
  ),
  'Swift_CharacterReader_UsAsciiReader' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\CharacterReader\\UsAsciiReader.php',
  ),
  'Swift_CharacterReader_Utf8Reader' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\CharacterReader\\Utf8Reader.php',
  ),
  'Swift_CharacterReader' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\CharacterReader.php',
  ),
  'Swift_CharacterReaderFactory_SimpleCharacterReaderFactory' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\CharacterReaderFactory\\SimpleCharacterReaderFactory.php',
  ),
  'Swift_CharacterReaderFactory' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\CharacterReaderFactory.php',
  ),
  'Swift_CharacterStream_ArrayCharacterStream' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\CharacterStream\\ArrayCharacterStream.php',
  ),
  'Swift_CharacterStream_NgCharacterStream' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\CharacterStream\\NgCharacterStream.php',
  ),
  'Swift_CharacterStream' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\CharacterStream.php',
  ),
  'Swift_ConfigurableSpool' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\ConfigurableSpool.php',
  ),
  'Swift_DependencyContainer' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\DependencyContainer.php',
  ),
  'Swift_DependencyException' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\DependencyException.php',
  ),
  'Swift_EmbeddedFile' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\EmbeddedFile.php',
  ),
  'Swift_Encoder_Base64Encoder' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Encoder\\Base64Encoder.php',
  ),
  'Swift_Encoder_QpEncoder' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Encoder\\QpEncoder.php',
  ),
  'Swift_Encoder_Rfc2231Encoder' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Encoder\\Rfc2231Encoder.php',
  ),
  'Swift_Encoder' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Encoder.php',
  ),
  'Swift_Encoding' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Encoding.php',
  ),
  'Swift_Events_CommandEvent' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\CommandEvent.php',
  ),
  'Swift_Events_CommandListener' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\CommandListener.php',
  ),
  'Swift_Events_Event' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\Event.php',
  ),
  'Swift_Events_EventDispatcher' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\EventDispatcher.php',
  ),
  'Swift_Events_EventListener' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\EventListener.php',
  ),
  'Swift_Events_EventObject' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\EventObject.php',
  ),
  'Swift_Events_ResponseEvent' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\ResponseEvent.php',
  ),
  'Swift_Events_ResponseListener' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\ResponseListener.php',
  ),
  'Swift_Events_SendEvent' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\SendEvent.php',
  ),
  'Swift_Events_SendListener' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\SendListener.php',
  ),
  'Swift_Events_SimpleEventDispatcher' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\SimpleEventDispatcher.php',
  ),
  'Swift_Events_TransportChangeEvent' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\TransportChangeEvent.php',
  ),
  'Swift_Events_TransportChangeListener' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\TransportChangeListener.php',
  ),
  'Swift_Events_TransportExceptionEvent' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\TransportExceptionEvent.php',
  ),
  'Swift_Events_TransportExceptionListener' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Events\\TransportExceptionListener.php',
  ),
  'Swift_FailoverTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\FailoverTransport.php',
  ),
  'Swift_FileSpool' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\FileSpool.php',
  ),
  'Swift_FileStream' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\FileStream.php',
  ),
  'Swift_Filterable' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Filterable.php',
  ),
  'Swift_Image' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Image.php',
  ),
  'Swift_InputByteStream' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\InputByteStream.php',
  ),
  'Swift_IoException' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\IoException.php',
  ),
  'Swift_KeyCache_ArrayKeyCache' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\KeyCache\\ArrayKeyCache.php',
  ),
  'Swift_KeyCache_DiskKeyCache' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\KeyCache\\DiskKeyCache.php',
  ),
  'Swift_KeyCache_KeyCacheInputStream' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\KeyCache\\KeyCacheInputStream.php',
  ),
  'Swift_KeyCache_NullKeyCache' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\KeyCache\\NullKeyCache.php',
  ),
  'Swift_KeyCache_SimpleKeyCacheInputStream' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\KeyCache\\SimpleKeyCacheInputStream.php',
  ),
  'Swift_KeyCache' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\KeyCache.php',
  ),
  'Swift_LoadBalancedTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\LoadBalancedTransport.php',
  ),
  'Swift_Mailer_ArrayRecipientIterator' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mailer\\ArrayRecipientIterator.php',
  ),
  'Swift_Mailer_RecipientIterator' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mailer\\RecipientIterator.php',
  ),
  'Swift_Mailer' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mailer.php',
  ),
  'Swift_MailTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\MailTransport.php',
  ),
  'Swift_MemorySpool' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\MemorySpool.php',
  ),
  'Swift_Message' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Message.php',
  ),
  'Swift_Mime_Attachment' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\Attachment.php',
  ),
  'Swift_Mime_CharsetObserver' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\CharsetObserver.php',
  ),
  'Swift_Mime_ContentEncoder_Base64ContentEncoder' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\ContentEncoder\\Base64ContentEncoder.php',
  ),
  'Swift_Mime_ContentEncoder_NativeQpContentEncoder' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\ContentEncoder\\NativeQpContentEncoder.php',
  ),
  'Swift_Mime_ContentEncoder_PlainContentEncoder' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\ContentEncoder\\PlainContentEncoder.php',
  ),
  'Swift_Mime_ContentEncoder_QpContentEncoder' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\ContentEncoder\\QpContentEncoder.php',
  ),
  'Swift_Mime_ContentEncoder_QpContentEncoderProxy' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\ContentEncoder\\QpContentEncoderProxy.php',
  ),
  'Swift_Mime_ContentEncoder_RawContentEncoder' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\ContentEncoder\\RawContentEncoder.php',
  ),
  'Swift_Mime_ContentEncoder' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\ContentEncoder.php',
  ),
  'Swift_Mime_EmbeddedFile' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\EmbeddedFile.php',
  ),
  'Swift_Mime_EncodingObserver' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\EncodingObserver.php',
  ),
  'Swift_Mime_Grammar' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\Grammar.php',
  ),
  'Swift_Mime_Header' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\Header.php',
  ),
  'Swift_Mime_HeaderEncoder_Base64HeaderEncoder' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\HeaderEncoder\\Base64HeaderEncoder.php',
  ),
  'Swift_Mime_HeaderEncoder_QpHeaderEncoder' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\HeaderEncoder\\QpHeaderEncoder.php',
  ),
  'Swift_Mime_HeaderEncoder' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\HeaderEncoder.php',
  ),
  'Swift_Mime_HeaderFactory' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\HeaderFactory.php',
  ),
  'Swift_Mime_Headers_AbstractHeader' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\Headers\\AbstractHeader.php',
  ),
  'Swift_Mime_Headers_DateHeader' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\Headers\\DateHeader.php',
  ),
  'Swift_Mime_Headers_IdentificationHeader' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\Headers\\IdentificationHeader.php',
  ),
  'Swift_Mime_Headers_MailboxHeader' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\Headers\\MailboxHeader.php',
  ),
  'Swift_Mime_Headers_OpenDKIMHeader' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\Headers\\OpenDKIMHeader.php',
  ),
  'Swift_Mime_Headers_ParameterizedHeader' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\Headers\\ParameterizedHeader.php',
  ),
  'Swift_Mime_Headers_PathHeader' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\Headers\\PathHeader.php',
  ),
  'Swift_Mime_Headers_UnstructuredHeader' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\Headers\\UnstructuredHeader.php',
  ),
  'Swift_Mime_HeaderSet' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\HeaderSet.php',
  ),
  'Swift_Mime_Message' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\Message.php',
  ),
  'Swift_Mime_MimeEntity' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\MimeEntity.php',
  ),
  'Swift_Mime_MimePart' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\MimePart.php',
  ),
  'Swift_Mime_ParameterizedHeader' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\ParameterizedHeader.php',
  ),
  'Swift_Mime_SimpleHeaderFactory' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\SimpleHeaderFactory.php',
  ),
  'Swift_Mime_SimpleHeaderSet' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\SimpleHeaderSet.php',
  ),
  'Swift_Mime_SimpleMessage' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\SimpleMessage.php',
  ),
  'Swift_Mime_SimpleMimeEntity' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Mime\\SimpleMimeEntity.php',
  ),
  'Swift_MimePart' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\MimePart.php',
  ),
  'Swift_NullTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\NullTransport.php',
  ),
  'Swift_OutputByteStream' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\OutputByteStream.php',
  ),
  'Swift_Plugins_AntiFloodPlugin' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\AntiFloodPlugin.php',
  ),
  'Swift_Plugins_BandwidthMonitorPlugin' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\BandwidthMonitorPlugin.php',
  ),
  'Swift_Plugins_Decorator_Replacements' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\Decorator\\Replacements.php',
  ),
  'Swift_Plugins_DecoratorPlugin' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\DecoratorPlugin.php',
  ),
  'Swift_Plugins_ImpersonatePlugin' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\ImpersonatePlugin.php',
  ),
  'Swift_Plugins_Logger' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\Logger.php',
  ),
  'Swift_Plugins_LoggerPlugin' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\LoggerPlugin.php',
  ),
  'Swift_Plugins_Loggers_ArrayLogger' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\Loggers\\ArrayLogger.php',
  ),
  'Swift_Plugins_Loggers_EchoLogger' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\Loggers\\EchoLogger.php',
  ),
  'Swift_Plugins_MessageLogger' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\MessageLogger.php',
  ),
  'Swift_Plugins_Pop_Pop3Connection' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\Pop\\Pop3Connection.php',
  ),
  'Swift_Plugins_Pop_Pop3Exception' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\Pop\\Pop3Exception.php',
  ),
  'Swift_Plugins_PopBeforeSmtpPlugin' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\PopBeforeSmtpPlugin.php',
  ),
  'Swift_Plugins_RedirectingPlugin' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\RedirectingPlugin.php',
  ),
  'Swift_Plugins_Reporter' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\Reporter.php',
  ),
  'Swift_Plugins_ReporterPlugin' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\ReporterPlugin.php',
  ),
  'Swift_Plugins_Reporters_HitReporter' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\Reporters\\HitReporter.php',
  ),
  'Swift_Plugins_Reporters_HtmlReporter' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\Reporters\\HtmlReporter.php',
  ),
  'Swift_Plugins_Sleeper' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\Sleeper.php',
  ),
  'Swift_Plugins_ThrottlerPlugin' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\ThrottlerPlugin.php',
  ),
  'Swift_Plugins_Timer' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Plugins\\Timer.php',
  ),
  'Swift_Preferences' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Preferences.php',
  ),
  'Swift_ReplacementFilterFactory' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\ReplacementFilterFactory.php',
  ),
  'Swift_RfcComplianceException' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\RfcComplianceException.php',
  ),
  'Swift_SendmailTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\SendmailTransport.php',
  ),
  'Swift_SignedMessage' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\SignedMessage.php',
  ),
  'Swift_Signer' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Signer.php',
  ),
  'Swift_Signers_BodySigner' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Signers\\BodySigner.php',
  ),
  'Swift_Signers_DKIMSigner' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Signers\\DKIMSigner.php',
  ),
  'Swift_Signers_DomainKeySigner' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Signers\\DomainKeySigner.php',
  ),
  'Swift_Signers_HeaderSigner' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Signers\\HeaderSigner.php',
  ),
  'Swift_Signers_OpenDKIMSigner' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Signers\\OpenDKIMSigner.php',
  ),
  'Swift_Signers_SMimeSigner' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Signers\\SMimeSigner.php',
  ),
  'Swift_SmtpTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\SmtpTransport.php',
  ),
  'Swift_Spool' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Spool.php',
  ),
  'Swift_SpoolTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\SpoolTransport.php',
  ),
  'Swift_StreamFilter' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\StreamFilter.php',
  ),
  'Swift_StreamFilters_ByteArrayReplacementFilter' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\StreamFilters\\ByteArrayReplacementFilter.php',
  ),
  'Swift_StreamFilters_StringReplacementFilter' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\StreamFilters\\StringReplacementFilter.php',
  ),
  'Swift_StreamFilters_StringReplacementFilterFactory' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\StreamFilters\\StringReplacementFilterFactory.php',
  ),
  'Swift_SwiftException' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\SwiftException.php',
  ),
  'Swift_Transport_AbstractSmtpTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\AbstractSmtpTransport.php',
  ),
  'Swift_Transport_Esmtp_Auth_CramMd5Authenticator' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\Esmtp\\Auth\\CramMd5Authenticator.php',
  ),
  'Swift_Transport_Esmtp_Auth_LoginAuthenticator' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\Esmtp\\Auth\\LoginAuthenticator.php',
  ),
  'Swift_Transport_Esmtp_Auth_NTLMAuthenticator' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\Esmtp\\Auth\\NTLMAuthenticator.php',
  ),
  'Swift_Transport_Esmtp_Auth_PlainAuthenticator' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\Esmtp\\Auth\\PlainAuthenticator.php',
  ),
  'Swift_Transport_Esmtp_Auth_XOAuth2Authenticator' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\Esmtp\\Auth\\XOAuth2Authenticator.php',
  ),
  'Swift_Transport_Esmtp_Authenticator' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\Esmtp\\Authenticator.php',
  ),
  'Swift_Transport_Esmtp_AuthHandler' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\Esmtp\\AuthHandler.php',
  ),
  'Swift_Transport_EsmtpHandler' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\EsmtpHandler.php',
  ),
  'Swift_Transport_EsmtpTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\EsmtpTransport.php',
  ),
  'Swift_Transport_FailoverTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\FailoverTransport.php',
  ),
  'Swift_Transport_IoBuffer' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\IoBuffer.php',
  ),
  'Swift_Transport_LoadBalancedTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\LoadBalancedTransport.php',
  ),
  'Swift_Transport_MailInvoker' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\MailInvoker.php',
  ),
  'Swift_Transport_MailTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\MailTransport.php',
  ),
  'Swift_Transport_NullTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\NullTransport.php',
  ),
  'Swift_Transport_SendmailTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\SendmailTransport.php',
  ),
  'Swift_Transport_SimpleMailInvoker' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\SimpleMailInvoker.php',
  ),
  'Swift_Transport_SmtpAgent' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\SmtpAgent.php',
  ),
  'Swift_Transport_SpoolTransport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\SpoolTransport.php',
  ),
  'Swift_Transport_StreamBuffer' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport\\StreamBuffer.php',
  ),
  'Swift_Transport' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Transport.php',
  ),
  'Swift_TransportException' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\TransportException.php',
  ),
  'Swift_Validate' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift\\Validate.php',
  ),
  'Swift' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\lib\\classes\\Swift.php',
  ),
  'Swift_AttachmentAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\AttachmentAcceptanceTest.php',
  ),
  'Swift_ByteStream_FileByteStreamAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\ByteStream\\FileByteStreamAcceptanceTest.php',
  ),
  'Swift_CharacterReaderFactory_SimpleCharacterReaderFactoryAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\CharacterReaderFactory\\SimpleCharacterReaderFactoryAcceptanceTest.php',
  ),
  'Swift_DependencyContainerAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\DependencyContainerAcceptanceTest.php',
  ),
  'Swift_EmbeddedFileAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\EmbeddedFileAcceptanceTest.php',
  ),
  'Swift_Encoder_Base64EncoderAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Encoder\\Base64EncoderAcceptanceTest.php',
  ),
  'Swift_Encoder_QpEncoderAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Encoder\\QpEncoderAcceptanceTest.php',
  ),
  'Swift_Encoder_Rfc2231EncoderAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Encoder\\Rfc2231EncoderAcceptanceTest.php',
  ),
  'Swift_EncodingAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\EncodingAcceptanceTest.php',
  ),
  'Swift_KeyCache_ArrayKeyCacheAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\KeyCache\\ArrayKeyCacheAcceptanceTest.php',
  ),
  'Swift_KeyCache_DiskKeyCacheAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\KeyCache\\DiskKeyCacheAcceptanceTest.php',
  ),
  'Swift_MessageAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\MessageAcceptanceTest.php',
  ),
  'Swift_Mime_AttachmentAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Mime\\AttachmentAcceptanceTest.php',
  ),
  'Swift_Mime_ContentEncoder_Base64ContentEncoderAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Mime\\ContentEncoder\\Base64ContentEncoderAcceptanceTest.php',
  ),
  'Swift_Mime_ContentEncoder_NativeQpContentEncoderAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Mime\\ContentEncoder\\NativeQpContentEncoderAcceptanceTest.php',
  ),
  'Swift_Mime_ContentEncoder_PlainContentEncoderAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Mime\\ContentEncoder\\PlainContentEncoderAcceptanceTest.php',
  ),
  'Swift_Mime_ContentEncoder_QpContentEncoderAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Mime\\ContentEncoder\\QpContentEncoderAcceptanceTest.php',
  ),
  'Swift_Mime_EmbeddedFileAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Mime\\EmbeddedFileAcceptanceTest.php',
  ),
  'Swift_Mime_HeaderEncoder_Base64HeaderEncoderAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Mime\\HeaderEncoder\\Base64HeaderEncoderAcceptanceTest.php',
  ),
  'Swift_Mime_MimePartAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Mime\\MimePartAcceptanceTest.php',
  ),
  'Swift_Mime_SimpleMessageAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Mime\\SimpleMessageAcceptanceTest.php',
  ),
  'Swift_MimePartAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\MimePartAcceptanceTest.php',
  ),
  'Swift_Transport_StreamBuffer_AbstractStreamBufferAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Transport\\StreamBuffer\\AbstractStreamBufferAcceptanceTest.php',
  ),
  'Swift_Transport_StreamBuffer_BasicSocketAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Transport\\StreamBuffer\\BasicSocketAcceptanceTest.php',
  ),
  'Swift_Transport_StreamBuffer_ProcessAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Transport\\StreamBuffer\\ProcessAcceptanceTest.php',
  ),
  'Swift_Transport_StreamBuffer_SocketTimeoutTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Transport\\StreamBuffer\\SocketTimeoutTest.php',
  ),
  'Swift_Transport_StreamBuffer_SslSocketAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Transport\\StreamBuffer\\SslSocketAcceptanceTest.php',
  ),
  'Swift_Transport_StreamBuffer_TlsSocketAcceptanceTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\acceptance\\Swift\\Transport\\StreamBuffer\\TlsSocketAcceptanceTest.php',
  ),
  'Swift_Bug111Test' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\bug\\Swift\\Bug111Test.php',
  ),
  'Swift_Bug118Test' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\bug\\Swift\\Bug118Test.php',
  ),
  'Swift_Bug206Test' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\bug\\Swift\\Bug206Test.php',
  ),
  'Swift_Bug274Test' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\bug\\Swift\\Bug274Test.php',
  ),
  'Swift_Bug34Test' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\bug\\Swift\\Bug34Test.php',
  ),
  'Swift_Bug35Test' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\bug\\Swift\\Bug35Test.php',
  ),
  'Swift_Bug38Test' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\bug\\Swift\\Bug38Test.php',
  ),
  'Swift_Bug518Test' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\bug\\Swift\\Bug518Test.php',
  ),
  'Swift_Bug51Test' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\bug\\Swift\\Bug51Test.php',
  ),
  'Swift_Bug534Test' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\bug\\Swift\\Bug534Test.php',
  ),
  'Swift_Bug71Test' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\bug\\Swift\\Bug71Test.php',
  ),
  'Swift_Bug76Test' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\bug\\Swift\\Bug76Test.php',
  ),
  'EsmtpTransportFixture' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\fixtures\\EsmtpTransportFixture.php',
  ),
  'MimeEntityFixture' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\fixtures\\MimeEntityFixture.php',
  ),
  'IdenticalBinaryConstraint' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\IdenticalBinaryConstraint.php',
  ),
  'Swift_Smoke_AttachmentSmokeTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\smoke\\Swift\\Smoke\\AttachmentSmokeTest.php',
  ),
  'Swift_Smoke_BasicSmokeTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\smoke\\Swift\\Smoke\\BasicSmokeTest.php',
  ),
  'Swift_Smoke_HtmlWithAttachmentSmokeTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\smoke\\Swift\\Smoke\\HtmlWithAttachmentSmokeTest.php',
  ),
  'Swift_Smoke_InternationalSmokeTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\smoke\\Swift\\Smoke\\InternationalSmokeTest.php',
  ),
  'Swift_StreamCollector' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\StreamCollector.php',
  ),
  'SwiftMailerSmokeTestCase' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\SwiftMailerSmokeTestCase.php',
  ),
  'SwiftMailerTestCase' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\SwiftMailerTestCase.php',
  ),
  'Swift_ByteStream_ArrayByteStreamTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\ByteStream\\ArrayByteStreamTest.php',
  ),
  'Swift_CharacterReader_GenericFixedWidthReaderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\CharacterReader\\GenericFixedWidthReaderTest.php',
  ),
  'Swift_CharacterReader_UsAsciiReaderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\CharacterReader\\UsAsciiReaderTest.php',
  ),
  'Swift_CharacterReader_Utf8ReaderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\CharacterReader\\Utf8ReaderTest.php',
  ),
  'Swift_CharacterStream_ArrayCharacterStreamTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\CharacterStream\\ArrayCharacterStreamTest.php',
  ),
  'One' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\DependencyContainerTest.php',
  ),
  'Swift_DependencyContainerTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\DependencyContainerTest.php',
  ),
  'Swift_Encoder_Base64EncoderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Encoder\\Base64EncoderTest.php',
  ),
  'Swift_Encoder_QpEncoderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Encoder\\QpEncoderTest.php',
  ),
  'Swift_Encoder_Rfc2231EncoderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Encoder\\Rfc2231EncoderTest.php',
  ),
  'Swift_Events_CommandEventTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Events\\CommandEventTest.php',
  ),
  'Swift_Events_EventObjectTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Events\\EventObjectTest.php',
  ),
  'Swift_Events_ResponseEventTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Events\\ResponseEventTest.php',
  ),
  'Swift_Events_SendEventTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Events\\SendEventTest.php',
  ),
  'Swift_Events_SimpleEventDispatcherTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Events\\SimpleEventDispatcherTest.php',
  ),
  'Swift_Events_TransportChangeEventTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Events\\TransportChangeEventTest.php',
  ),
  'Swift_Events_TransportExceptionEventTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Events\\TransportExceptionEventTest.php',
  ),
  'Swift_KeyCache_ArrayKeyCacheTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\KeyCache\\ArrayKeyCacheTest.php',
  ),
  'Swift_KeyCache_SimpleKeyCacheInputStreamTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\KeyCache\\SimpleKeyCacheInputStreamTest.php',
  ),
  'Swift_Mailer_ArrayRecipientIteratorTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mailer\\ArrayRecipientIteratorTest.php',
  ),
  'Swift_MailerTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\MailerTest.php',
  ),
  'Swift_MessageTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\MessageTest.php',
  ),
  'Swift_Mime_AbstractMimeEntityTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\AbstractMimeEntityTest.php',
  ),
  'Swift_Mime_AttachmentTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\AttachmentTest.php',
  ),
  'Swift_Mime_ContentEncoder_Base64ContentEncoderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\ContentEncoder\\Base64ContentEncoderTest.php',
  ),
  'Swift_Mime_ContentEncoder_PlainContentEncoderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\ContentEncoder\\PlainContentEncoderTest.php',
  ),
  'Swift_Mime_ContentEncoder_QpContentEncoderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\ContentEncoder\\QpContentEncoderTest.php',
  ),
  'Swift_Mime_EmbeddedFileTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\EmbeddedFileTest.php',
  ),
  'Swift_Mime_HeaderEncoder_Base64HeaderEncoderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\HeaderEncoder\\Base64HeaderEncoderTest.php',
  ),
  'Swift_Mime_HeaderEncoder_QpHeaderEncoderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\HeaderEncoder\\QpHeaderEncoderTest.php',
  ),
  'Swift_Mime_Headers_DateHeaderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\Headers\\DateHeaderTest.php',
  ),
  'Swift_Mime_Headers_IdentificationHeaderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\Headers\\IdentificationHeaderTest.php',
  ),
  'Swift_Mime_Headers_MailboxHeaderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\Headers\\MailboxHeaderTest.php',
  ),
  'Swift_Mime_Headers_ParameterizedHeaderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\Headers\\ParameterizedHeaderTest.php',
  ),
  'Swift_Mime_Headers_PathHeaderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\Headers\\PathHeaderTest.php',
  ),
  'Swift_Mime_Headers_UnstructuredHeaderTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\Headers\\UnstructuredHeaderTest.php',
  ),
  'Swift_Mime_MimePartTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\MimePartTest.php',
  ),
  'Swift_Mime_SimpleHeaderFactoryTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\SimpleHeaderFactoryTest.php',
  ),
  'Swift_Mime_SimpleHeaderSetTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\SimpleHeaderSetTest.php',
  ),
  'Swift_Mime_SimpleMessageTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\SimpleMessageTest.php',
  ),
  'Swift_Mime_SimpleMimeEntityTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Mime\\SimpleMimeEntityTest.php',
  ),
  'Swift_Plugins_AntiFloodPluginTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Plugins\\AntiFloodPluginTest.php',
  ),
  'Swift_Plugins_BandwidthMonitorPluginTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Plugins\\BandwidthMonitorPluginTest.php',
  ),
  'Swift_Plugins_DecoratorPluginTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Plugins\\DecoratorPluginTest.php',
  ),
  'Swift_Plugins_LoggerPluginTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Plugins\\LoggerPluginTest.php',
  ),
  'Swift_Plugins_Loggers_ArrayLoggerTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Plugins\\Loggers\\ArrayLoggerTest.php',
  ),
  'Swift_Plugins_Loggers_EchoLoggerTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Plugins\\Loggers\\EchoLoggerTest.php',
  ),
  'Swift_Plugins_PopBeforeSmtpPluginTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Plugins\\PopBeforeSmtpPluginTest.php',
  ),
  'Swift_Plugins_RedirectingPluginTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Plugins\\RedirectingPluginTest.php',
  ),
  'Swift_Plugins_ReporterPluginTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Plugins\\ReporterPluginTest.php',
  ),
  'Swift_Plugins_Reporters_HitReporterTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Plugins\\Reporters\\HitReporterTest.php',
  ),
  'Swift_Plugins_Reporters_HtmlReporterTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Plugins\\Reporters\\HtmlReporterTest.php',
  ),
  'Swift_Plugins_ThrottlerPluginTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Plugins\\ThrottlerPluginTest.php',
  ),
  'Swift_Signers_DKIMSignerTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Signers\\DKIMSignerTest.php',
  ),
  'Swift_Signers_OpenDKIMSignerTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Signers\\OpenDKIMSignerTest.php',
  ),
  'Swift_Signers_SMimeSignerTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Signers\\SMimeSignerTest.php',
  ),
  'Swift_StreamFilters_ByteArrayReplacementFilterTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\StreamFilters\\ByteArrayReplacementFilterTest.php',
  ),
  'Swift_StreamFilters_StringReplacementFilterFactoryTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\StreamFilters\\StringReplacementFilterFactoryTest.php',
  ),
  'Swift_StreamFilters_StringReplacementFilterTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\StreamFilters\\StringReplacementFilterTest.php',
  ),
  'Swift_Transport_AbstractSmtpEventSupportTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\AbstractSmtpEventSupportTest.php',
  ),
  'Swift_Transport_AbstractSmtpTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\AbstractSmtpTest.php',
  ),
  'Swift_Transport_Esmtp_Auth_CramMd5AuthenticatorTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\Esmtp\\Auth\\CramMd5AuthenticatorTest.php',
  ),
  'Swift_Transport_Esmtp_Auth_LoginAuthenticatorTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\Esmtp\\Auth\\LoginAuthenticatorTest.php',
  ),
  'Swift_Transport_Esmtp_Auth_NTLMAuthenticatorTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\Esmtp\\Auth\\NTLMAuthenticatorTest.php',
  ),
  'Swift_Transport_Esmtp_Auth_PlainAuthenticatorTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\Esmtp\\Auth\\PlainAuthenticatorTest.php',
  ),
  'Swift_Transport_Esmtp_AuthHandlerTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\Esmtp\\AuthHandlerTest.php',
  ),
  'Swift_Transport_EsmtpHandlerMixin' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\EsmtpTransport\\ExtensionSupportTest.php',
  ),
  'Swift_Transport_EsmtpTransport_ExtensionSupportTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\EsmtpTransport\\ExtensionSupportTest.php',
  ),
  'Swift_Transport_EsmtpTransportTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\EsmtpTransportTest.php',
  ),
  'Swift_Transport_FailoverTransportTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\FailoverTransportTest.php',
  ),
  'Swift_Transport_LoadBalancedTransportTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\LoadBalancedTransportTest.php',
  ),
  'Swift_Transport_MailTransportTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\MailTransportTest.php',
  ),
  'Swift_Transport_SendmailTransportTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\SendmailTransportTest.php',
  ),
  'Swift_Transport_StreamBufferTest' => 
  array (
    0 => '//vendor\\swiftmailer\\swiftmailer\\tests\\unit\\Swift\\Transport\\StreamBufferTest.php',
  ),
  'Symfony\\Component\\Yaml\\Dumper' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Dumper.php',
  ),
  'Symfony\\Component\\Yaml\\Escaper' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Escaper.php',
  ),
  'Symfony\\Component\\Yaml\\Exception\\DumpException' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Exception\\DumpException.php',
  ),
  'Symfony\\Component\\Yaml\\Exception\\ExceptionInterface' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Exception\\ExceptionInterface.php',
  ),
  'Symfony\\Component\\Yaml\\Exception\\ParseException' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Exception\\ParseException.php',
  ),
  'Symfony\\Component\\Yaml\\Exception\\RuntimeException' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Exception\\RuntimeException.php',
  ),
  'Symfony\\Component\\Yaml\\Inline' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Inline.php',
  ),
  'Symfony\\Component\\Yaml\\Parser' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Parser.php',
  ),
  'Symfony\\Component\\Yaml\\Tests\\DumperTest' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Tests\\DumperTest.php',
  ),
  'Symfony\\Component\\Yaml\\Tests\\A' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Tests\\DumperTest.php',
  ),
  'Symfony\\Component\\Yaml\\Tests\\InlineTest' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Tests\\InlineTest.php',
  ),
  'Symfony\\Component\\Yaml\\Tests\\ParseExceptionTest' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Tests\\ParseExceptionTest.php',
  ),
  'Symfony\\Component\\Yaml\\Tests\\ParserTest' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Tests\\ParserTest.php',
  ),
  'Symfony\\Component\\Yaml\\Tests\\B' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Tests\\ParserTest.php',
  ),
  'Symfony\\Component\\Yaml\\Tests\\YamlTest' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Tests\\YamlTest.php',
  ),
  'Symfony\\Component\\Yaml\\Unescaper' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Unescaper.php',
  ),
  'Symfony\\Component\\Yaml\\Yaml' => 
  array (
    0 => '//vendor\\symfony\\yaml\\Yaml.php',
  ),
  'Zend\\Escaper\\Escaper' => 
  array (
    0 => '//vendor\\zendframework\\zend-escaper\\src\\Escaper.php',
  ),
  'Zend\\Escaper\\Exception\\ExceptionInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-escaper\\src\\Exception\\ExceptionInterface.php',
  ),
  'Zend\\Escaper\\Exception\\InvalidArgumentException' => 
  array (
    0 => '//vendor\\zendframework\\zend-escaper\\src\\Exception\\InvalidArgumentException.php',
  ),
  'Zend\\Escaper\\Exception\\RuntimeException' => 
  array (
    0 => '//vendor\\zendframework\\zend-escaper\\src\\Exception\\RuntimeException.php',
  ),
  'Zend\\Hydrator\\AbstractHydrator' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\AbstractHydrator.php',
  ),
  'Zend\\Hydrator\\Aggregate\\AggregateHydrator' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Aggregate\\AggregateHydrator.php',
  ),
  'Zend\\Hydrator\\Aggregate\\ExtractEvent' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Aggregate\\ExtractEvent.php',
  ),
  'Zend\\Hydrator\\Aggregate\\HydrateEvent' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Aggregate\\HydrateEvent.php',
  ),
  'Zend\\Hydrator\\Aggregate\\HydratorListener' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Aggregate\\HydratorListener.php',
  ),
  'Zend\\Hydrator\\ArraySerializable' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\ArraySerializable.php',
  ),
  'Zend\\Hydrator\\ClassMethods' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\ClassMethods.php',
  ),
  'Zend\\Hydrator\\DelegatingHydrator' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\DelegatingHydrator.php',
  ),
  'Zend\\Hydrator\\DelegatingHydratorFactory' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\DelegatingHydratorFactory.php',
  ),
  'Zend\\Hydrator\\Exception\\BadMethodCallException' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Exception\\BadMethodCallException.php',
  ),
  'Zend\\Hydrator\\Exception\\DomainException' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Exception\\DomainException.php',
  ),
  'Zend\\Hydrator\\Exception\\ExceptionInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Exception\\ExceptionInterface.php',
  ),
  'Zend\\Hydrator\\Exception\\ExtensionNotLoadedException' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Exception\\ExtensionNotLoadedException.php',
  ),
  'Zend\\Hydrator\\Exception\\InvalidArgumentException' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Exception\\InvalidArgumentException.php',
  ),
  'Zend\\Hydrator\\Exception\\InvalidCallbackException' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Exception\\InvalidCallbackException.php',
  ),
  'Zend\\Hydrator\\Exception\\LogicException' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Exception\\LogicException.php',
  ),
  'Zend\\Hydrator\\Exception\\RuntimeException' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Exception\\RuntimeException.php',
  ),
  'Zend\\Hydrator\\ExtractionInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\ExtractionInterface.php',
  ),
  'Zend\\Hydrator\\Filter\\FilterComposite' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Filter\\FilterComposite.php',
  ),
  'Zend\\Hydrator\\Filter\\FilterInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Filter\\FilterInterface.php',
  ),
  'Zend\\Hydrator\\Filter\\FilterProviderInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Filter\\FilterProviderInterface.php',
  ),
  'Zend\\Hydrator\\Filter\\GetFilter' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Filter\\GetFilter.php',
  ),
  'Zend\\Hydrator\\Filter\\HasFilter' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Filter\\HasFilter.php',
  ),
  'Zend\\Hydrator\\Filter\\IsFilter' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Filter\\IsFilter.php',
  ),
  'Zend\\Hydrator\\Filter\\MethodMatchFilter' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Filter\\MethodMatchFilter.php',
  ),
  'Zend\\Hydrator\\Filter\\NumberOfParameterFilter' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Filter\\NumberOfParameterFilter.php',
  ),
  'Zend\\Hydrator\\Filter\\OptionalParametersFilter' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Filter\\OptionalParametersFilter.php',
  ),
  'Zend\\Hydrator\\FilterEnabledInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\FilterEnabledInterface.php',
  ),
  'Zend\\Hydrator\\HydrationInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\HydrationInterface.php',
  ),
  'Zend\\Hydrator\\HydratorAwareInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\HydratorAwareInterface.php',
  ),
  'Zend\\Hydrator\\HydratorAwareTrait' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\HydratorAwareTrait.php',
  ),
  'Zend\\Hydrator\\HydratorInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\HydratorInterface.php',
  ),
  'Zend\\Hydrator\\HydratorOptionsInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\HydratorOptionsInterface.php',
  ),
  'Zend\\Hydrator\\HydratorPluginManager' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\HydratorPluginManager.php',
  ),
  'Zend\\Hydrator\\Iterator\\HydratingArrayIterator' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Iterator\\HydratingArrayIterator.php',
  ),
  'Zend\\Hydrator\\Iterator\\HydratingIteratorInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Iterator\\HydratingIteratorInterface.php',
  ),
  'Zend\\Hydrator\\Iterator\\HydratingIteratorIterator' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Iterator\\HydratingIteratorIterator.php',
  ),
  'Zend\\Hydrator\\NamingStrategy\\ArrayMapNamingStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\NamingStrategy\\ArrayMapNamingStrategy.php',
  ),
  'Zend\\Hydrator\\NamingStrategy\\CompositeNamingStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\NamingStrategy\\CompositeNamingStrategy.php',
  ),
  'Zend\\Hydrator\\NamingStrategy\\IdentityNamingStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\NamingStrategy\\IdentityNamingStrategy.php',
  ),
  'Zend\\Hydrator\\NamingStrategy\\MapNamingStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\NamingStrategy\\MapNamingStrategy.php',
  ),
  'Zend\\Hydrator\\NamingStrategy\\NamingStrategyInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\NamingStrategy\\NamingStrategyInterface.php',
  ),
  'Zend\\Hydrator\\NamingStrategy\\UnderscoreNamingStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\NamingStrategy\\UnderscoreNamingStrategy.php',
  ),
  'Zend\\Hydrator\\NamingStrategyEnabledInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\NamingStrategyEnabledInterface.php',
  ),
  'Zend\\Hydrator\\ObjectProperty' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\ObjectProperty.php',
  ),
  'Zend\\Hydrator\\Reflection' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Reflection.php',
  ),
  'Zend\\Hydrator\\Strategy\\BooleanStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Strategy\\BooleanStrategy.php',
  ),
  'Zend\\Hydrator\\Strategy\\ClosureStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Strategy\\ClosureStrategy.php',
  ),
  'Zend\\Hydrator\\Strategy\\DateTimeFormatterStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Strategy\\DateTimeFormatterStrategy.php',
  ),
  'Zend\\Hydrator\\Strategy\\DefaultStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Strategy\\DefaultStrategy.php',
  ),
  'Zend\\Hydrator\\Strategy\\Exception\\ExceptionInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Strategy\\Exception\\ExceptionInterface.php',
  ),
  'Zend\\Hydrator\\Strategy\\Exception\\InvalidArgumentException' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Strategy\\Exception\\InvalidArgumentException.php',
  ),
  'Zend\\Hydrator\\Strategy\\ExplodeStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Strategy\\ExplodeStrategy.php',
  ),
  'Zend\\Hydrator\\Strategy\\SerializableStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Strategy\\SerializableStrategy.php',
  ),
  'Zend\\Hydrator\\Strategy\\StrategyChain' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Strategy\\StrategyChain.php',
  ),
  'Zend\\Hydrator\\Strategy\\StrategyInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\Strategy\\StrategyInterface.php',
  ),
  'Zend\\Hydrator\\StrategyEnabledInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-hydrator\\src\\StrategyEnabledInterface.php',
  ),
  'ZendBench\\Stdlib\\ExtractPriorityQueue' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\benchmark\\ExtractPriorityQueue.php',
  ),
  'ZendBench\\Stdlib\\InsertPriorityQueue' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\benchmark\\InsertPriorityQueue.php',
  ),
  'ZendBench\\Stdlib\\RemovePriorityQueue' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\benchmark\\RemovePriorityQueue.php',
  ),
  'Zend\\Stdlib\\AbstractOptions' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\AbstractOptions.php',
  ),
  'Zend\\Stdlib\\ArrayObject' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\ArrayObject.php',
  ),
  'Zend\\Stdlib\\ArraySerializableInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\ArraySerializableInterface.php',
  ),
  'Zend\\Stdlib\\ArrayStack' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\ArrayStack.php',
  ),
  'Zend\\Stdlib\\ArrayUtils\\MergeRemoveKey' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\ArrayUtils\\MergeRemoveKey.php',
  ),
  'Zend\\Stdlib\\ArrayUtils\\MergeReplaceKey' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\ArrayUtils\\MergeReplaceKey.php',
  ),
  'Zend\\Stdlib\\ArrayUtils\\MergeReplaceKeyInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\ArrayUtils\\MergeReplaceKeyInterface.php',
  ),
  'Zend\\Stdlib\\ArrayUtils' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\ArrayUtils.php',
  ),
  'Zend\\Stdlib\\CallbackHandler' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\CallbackHandler.php',
  ),
  'Zend\\Stdlib\\DateTime' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\DateTime.php',
  ),
  'Zend\\Stdlib\\DispatchableInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\DispatchableInterface.php',
  ),
  'Zend\\Stdlib\\ErrorHandler' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\ErrorHandler.php',
  ),
  'Zend\\Stdlib\\Exception\\BadMethodCallException' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Exception\\BadMethodCallException.php',
  ),
  'Zend\\Stdlib\\Exception\\DomainException' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Exception\\DomainException.php',
  ),
  'Zend\\Stdlib\\Exception\\ExceptionInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Exception\\ExceptionInterface.php',
  ),
  'Zend\\Stdlib\\Exception\\ExtensionNotLoadedException' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Exception\\ExtensionNotLoadedException.php',
  ),
  'Zend\\Stdlib\\Exception\\InvalidArgumentException' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Exception\\InvalidArgumentException.php',
  ),
  'Zend\\Stdlib\\Exception\\InvalidCallbackException' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Exception\\InvalidCallbackException.php',
  ),
  'Zend\\Stdlib\\Exception\\LogicException' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Exception\\LogicException.php',
  ),
  'Zend\\Stdlib\\Exception\\RuntimeException' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Exception\\RuntimeException.php',
  ),
  'Zend\\Stdlib\\Extractor\\ExtractionInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Extractor\\ExtractionInterface.php',
  ),
  'Zend\\Stdlib\\FastPriorityQueue' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\FastPriorityQueue.php',
  ),
  'Zend\\Stdlib\\Glob' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Glob.php',
  ),
  'Zend\\Stdlib\\Guard\\AllGuardsTrait' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Guard\\AllGuardsTrait.php',
  ),
  'Zend\\Stdlib\\Guard\\ArrayOrTraversableGuardTrait' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Guard\\ArrayOrTraversableGuardTrait.php',
  ),
  'Zend\\Stdlib\\Guard\\EmptyGuardTrait' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Guard\\EmptyGuardTrait.php',
  ),
  'Zend\\Stdlib\\Guard\\GuardUtils' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Guard\\GuardUtils.php',
  ),
  'Zend\\Stdlib\\Guard\\NullGuardTrait' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Guard\\NullGuardTrait.php',
  ),
  'Zend\\Stdlib\\Hydrator\\AbstractHydrator' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\AbstractHydrator.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Aggregate\\AggregateHydrator' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Aggregate\\AggregateHydrator.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Aggregate\\ExtractEvent' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Aggregate\\ExtractEvent.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Aggregate\\HydrateEvent' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Aggregate\\HydrateEvent.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Aggregate\\HydratorListener' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Aggregate\\HydratorListener.php',
  ),
  'Zend\\Stdlib\\Hydrator\\ArraySerializable' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\ArraySerializable.php',
  ),
  'Zend\\Stdlib\\Hydrator\\ClassMethods' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\ClassMethods.php',
  ),
  'Zend\\Stdlib\\Hydrator\\DelegatingHydrator' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\DelegatingHydrator.php',
  ),
  'Zend\\Stdlib\\Hydrator\\DelegatingHydratorFactory' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\DelegatingHydratorFactory.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Filter\\FilterComposite' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Filter\\FilterComposite.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Filter\\FilterInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Filter\\FilterInterface.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Filter\\FilterProviderInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Filter\\FilterProviderInterface.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Filter\\GetFilter' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Filter\\GetFilter.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Filter\\HasFilter' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Filter\\HasFilter.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Filter\\IsFilter' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Filter\\IsFilter.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Filter\\MethodMatchFilter' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Filter\\MethodMatchFilter.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Filter\\NumberOfParameterFilter' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Filter\\NumberOfParameterFilter.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Filter\\OptionalParametersFilter' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Filter\\OptionalParametersFilter.php',
  ),
  'Zend\\Stdlib\\Hydrator\\FilterEnabledInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\FilterEnabledInterface.php',
  ),
  'Zend\\Stdlib\\Hydrator\\HydrationInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\HydrationInterface.php',
  ),
  'Zend\\Stdlib\\Hydrator\\HydratorAwareInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\HydratorAwareInterface.php',
  ),
  'Zend\\Stdlib\\Hydrator\\HydratorAwareTrait' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\HydratorAwareTrait.php',
  ),
  'Zend\\Stdlib\\Hydrator\\HydratorInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\HydratorInterface.php',
  ),
  'Zend\\Stdlib\\Hydrator\\HydratorOptionsInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\HydratorOptionsInterface.php',
  ),
  'Zend\\Stdlib\\Hydrator\\HydratorPluginManager' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\HydratorPluginManager.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Iterator\\HydratingArrayIterator' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Iterator\\HydratingArrayIterator.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Iterator\\HydratingIteratorInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Iterator\\HydratingIteratorInterface.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Iterator\\HydratingIteratorIterator' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Iterator\\HydratingIteratorIterator.php',
  ),
  'Zend\\Stdlib\\Hydrator\\NamingStrategy\\ArrayMapNamingStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\NamingStrategy\\ArrayMapNamingStrategy.php',
  ),
  'Zend\\Stdlib\\Hydrator\\NamingStrategy\\CompositeNamingStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\NamingStrategy\\CompositeNamingStrategy.php',
  ),
  'Zend\\Stdlib\\Hydrator\\NamingStrategy\\IdentityNamingStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\NamingStrategy\\IdentityNamingStrategy.php',
  ),
  'Zend\\Stdlib\\Hydrator\\NamingStrategy\\MapNamingStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\NamingStrategy\\MapNamingStrategy.php',
  ),
  'Zend\\Stdlib\\Hydrator\\NamingStrategy\\NamingStrategyInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\NamingStrategy\\NamingStrategyInterface.php',
  ),
  'Zend\\Stdlib\\Hydrator\\NamingStrategy\\UnderscoreNamingStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\NamingStrategy\\UnderscoreNamingStrategy.php',
  ),
  'Zend\\Stdlib\\Hydrator\\NamingStrategyEnabledInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\NamingStrategyEnabledInterface.php',
  ),
  'Zend\\Stdlib\\Hydrator\\ObjectProperty' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\ObjectProperty.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Reflection' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Reflection.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Strategy\\BooleanStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Strategy\\BooleanStrategy.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Strategy\\ClosureStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Strategy\\ClosureStrategy.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Strategy\\DateTimeFormatterStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Strategy\\DateTimeFormatterStrategy.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Strategy\\DefaultStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Strategy\\DefaultStrategy.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Strategy\\Exception\\ExceptionInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Strategy\\Exception\\ExceptionInterface.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Strategy\\Exception\\InvalidArgumentException' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Strategy\\Exception\\InvalidArgumentException.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Strategy\\ExplodeStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Strategy\\ExplodeStrategy.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Strategy\\SerializableStrategy' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Strategy\\SerializableStrategy.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Strategy\\StrategyChain' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Strategy\\StrategyChain.php',
  ),
  'Zend\\Stdlib\\Hydrator\\Strategy\\StrategyInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\Strategy\\StrategyInterface.php',
  ),
  'Zend\\Stdlib\\Hydrator\\StrategyEnabledInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Hydrator\\StrategyEnabledInterface.php',
  ),
  'Zend\\Stdlib\\InitializableInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\InitializableInterface.php',
  ),
  'Zend\\Stdlib\\JsonSerializable' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\JsonSerializable.php',
  ),
  'Zend\\Stdlib\\Message' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Message.php',
  ),
  'Zend\\Stdlib\\MessageInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\MessageInterface.php',
  ),
  'Zend\\Stdlib\\ParameterObjectInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\ParameterObjectInterface.php',
  ),
  'Zend\\Stdlib\\Parameters' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Parameters.php',
  ),
  'Zend\\Stdlib\\ParametersInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\ParametersInterface.php',
  ),
  'Zend\\Stdlib\\PriorityList' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\PriorityList.php',
  ),
  'Zend\\Stdlib\\PriorityQueue' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\PriorityQueue.php',
  ),
  'Zend\\Stdlib\\Request' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Request.php',
  ),
  'Zend\\Stdlib\\RequestInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\RequestInterface.php',
  ),
  'Zend\\Stdlib\\Response' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\Response.php',
  ),
  'Zend\\Stdlib\\ResponseInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\ResponseInterface.php',
  ),
  'Zend\\Stdlib\\SplPriorityQueue' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\SplPriorityQueue.php',
  ),
  'Zend\\Stdlib\\SplQueue' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\SplQueue.php',
  ),
  'Zend\\Stdlib\\SplStack' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\SplStack.php',
  ),
  'Zend\\Stdlib\\StringUtils' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\StringUtils.php',
  ),
  'Zend\\Stdlib\\StringWrapper\\AbstractStringWrapper' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\StringWrapper\\AbstractStringWrapper.php',
  ),
  'Zend\\Stdlib\\StringWrapper\\Iconv' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\StringWrapper\\Iconv.php',
  ),
  'Zend\\Stdlib\\StringWrapper\\Intl' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\StringWrapper\\Intl.php',
  ),
  'Zend\\Stdlib\\StringWrapper\\MbString' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\StringWrapper\\MbString.php',
  ),
  'Zend\\Stdlib\\StringWrapper\\Native' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\StringWrapper\\Native.php',
  ),
  'Zend\\Stdlib\\StringWrapper\\StringWrapperInterface' => 
  array (
    0 => '//vendor\\zendframework\\zend-stdlib\\src\\StringWrapper\\StringWrapperInterface.php',
  ),
        'WebshopItemOrderabilityService' =>
            array (
                0 => '//module/dcshop\\common\\interfaces\\WebshopItemOrderabilityService.php',
            ),
        'GenericWebshopItemOrderabilityService' =>
            array (
                0 => '//module/dcshop\\common\\classes\\GenericWebshopItemOrderabilityService.php',
            ),
);

    public function register()
    {
        spl_autoload_register([__NAMESPACE__ . '\\' . __CLASS__,'resolve']);
    }

    public static function resolve($class)
    {
        if (isset(static::$map[$class])) {
            foreach (static::$map[$class] as $includePath) {
              $includePath = rtrim(rtrim($_SERVER['DOCUMENT_ROOT'],'/'),'/\\') . '/' .  ltrim($includePath,'/\\');
              $includePath = str_replace('\\', '/',$includePath);
                include($includePath);
            }
        }
    }

}