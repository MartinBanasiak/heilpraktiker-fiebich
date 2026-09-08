<?php

class ShopAutoloader
{
    private static $map = array(
        'RMAShipmentController' =>
            array(
                0 => '/module/dcshop/b2c/RMAShipmentController.php',
            ),
        'campaignConditionTrait' =>
            array(
                0 => '/module/dcshop/campaigns/campaignConditionTrait.php',
            ),
        'CampaignDiscountAction' =>
            array(
                0 => '/module/dcshop/campaigns/CampaignDiscountAction.php',
            ),
        'CampaignFreeItemAction' =>
            array(
                0 => '/module/dcshop/campaigns/CampaignFreeItemAction.php',
            ),
        'CampaignRuleBuilder' =>
            array(
                0 => '/module/dcshop/campaigns/CampaignRuleBuilder.php',
            ),
        'CampaignRuleFactory' =>
            array(
                0 => '/module/dcshop/campaigns/CampaignRuleFactory.php',
            ),
        'CampaignRuleHelper' =>
            array(
                0 => '/module/dcshop/campaigns/CampaignRuleHelper.php',
            ),
        'campaignRulePartTrait' =>
            array(
                0 => '/module/dcshop/campaigns/campaignRulePartTrait.php',
            ),
        'CampaignSpecialShippingAction' =>
            array(
                0 => '/module/dcshop/campaigns/CampaignSpecialShippingAction.php',
            ),
        'GenericCampaign' =>
            array(
                0 => '/module/dcshop/campaigns/GenericCampaign.php',
            ),
        'GenericCampaignElement' =>
            array(
                0 => '/module/dcshop/campaigns/GenericCampaignElement.php',
            ),
        'GenericCampaignElementCollection' =>
            array(
                0 => '/module/dcshop/campaigns/GenericCampaignElementCollection.php',
            ),
        'GenericCampaignElementConfig' =>
            array(
                0 => '/module/dcshop/campaigns/GenericCampaignElementConfig.php',
            ),
        'GenericCampaignElementRepository' =>
            array(
                0 => '/module/dcshop/campaigns/GenericCampaignElementRepository.php',
            ),
        'GenericCampaignHeaderCollection' =>
            array(
                0 => '/module/dcshop/campaigns/GenericCampaignHeaderCollection.php',
            ),
        'GenericCampaignHeaderConfig' =>
            array(
                0 => '/module/dcshop/campaigns/GenericCampaignHeaderConfig.php',
            ),
        'GenericCampaignHeaderRepository' =>
            array(
                0 => '/module/dcshop/campaigns/GenericCampaignHeaderRepository.php',
            ),
        'GenericCampaignRepository' =>
            array(
                0 => '/module/dcshop/campaigns/GenericCampaignRepository.php',
            ),
        'NoDiffItemsGTERuleCondition' =>
            array(
                0 => '/module/dcshop/campaigns/NoDiffItemsGTERuleCondition.php',
            ),
        'SingleItemAmntGTERuleCondition' =>
            array(
                0 => '/module/dcshop/campaigns/SingleItemAmntGTERuleCondition.php',
            ),
        'SingleItemQtyGTERuleCondition' =>
            array(
                0 => '/module/dcshop/campaigns/SingleItemQtyGTERuleCondition.php',
            ),
        'SumAmntAllItemsGTERuleCondition' =>
            array(
                0 => '/module/dcshop/campaigns/SumAmntAllItemsGTERuleCondition.php',
            ),
        'SumBasketAmntGTERuleCondition' =>
            array(
                0 => '/module/dcshop/campaigns/SumBasketAmntGTERuleCondition.php',
            ),
        'SumQtyItemRuleCondition' =>
            array(
                0 => '/module/dcshop/campaigns/SumQtyItemRuleCondition.php',
            ),
        'CustomerDecorator' =>
            array(
                0 => '/module/dcshop/common/abstracts/CustomerDecorator.php',
            ),
        'DiscountBase' =>
            array(
                0 => '/module/dcshop/common/abstracts/DiscountBase.php',
            ),
        'ItemPriceDataDecorator' =>
            array(
                0 => '/module/dcshop/common/abstracts/ItemPriceDataDecorator.php',
            ),
        'WebshopItemDecorator' =>
            array(
                0 => '/module/dcshop/common/abstracts/WebshopItemDecorator.php',
            ),
        'AddressFormBuilder' =>
            array(
                0 => '/module/dcshop/common/classes/AddressFormBuilder.php',
            ),
        'AdvancedPriceProvider' =>
            array(
                0 => '/module/dcshop/common/classes/AdvancedPriceProvider.php',
            ),
        'AppliedDiscount' =>
            array(
                0 => '/module/dcshop/common/classes/AppliedDiscount.php',
            ),
        'B2BStdPriceStrategy' =>
            array(
                0 => '/module/dcshop/common/classes/B2BStdPriceStrategy.php',
            ),
        'B2C4StepOrderHeaderTemplate' =>
            array(
                0 => '/module/dcshop/common/classes/B2C4StepOrderHeaderTemplate.php',
            ),
        'B2C4StepOrderHeaderViewModel' =>
            array(
                0 => '/module/dcshop/common/classes/B2C4StepOrderHeaderViewModel.php',
            ),
        'B2C4StepOrderStep1InfoContentLeftTemplate' =>
            array(
                0 => '/module/dcshop/common/classes/B2C4StepOrderStep1InfoContentLeftTemplate.php',
            ),
        'B2C4StepOrderStep1InfoContentLeftViewModel' =>
            array(
                0 => '/module/dcshop/common/classes/B2C4StepOrderStep1InfoContentLeftViewModel.php',
            ),
        'B2CStep1OrderTemplate' =>
            array(
                0 => '/module/dcshop/common/classes/B2CStep1OrderTemplate.php',
            ),
        'B2CUserOrderStep1ViewModel' =>
            array(
                0 => '/module/dcshop/common/classes/B2CUserOrderStep1ViewModel.php',
            ),
        'BasicPriceProvider' =>
            array(
                0 => '/module/dcshop/common/classes/BasicPriceProvider.php',
            ),
        'BasketEntity' =>
            array(
                0 => '/module/dcshop/common/classes/BasketEntity.php',
            ),
        'BasketValueSource' =>
            array(
                0 => '/module/dcshop/common/classes/BasketValueSource.php',
            ),
        'Category' =>
            array(
                0 => '/module/dcshop/common/classes/Category.php',
            ),
        'CategoryCollection' =>
            array(
                0 => '/module/dcshop/common/classes/CategoryCollection.php',
            ),
        'CategoryConfig' =>
            array(
                0 => '/module/dcshop/common/classes/CategoryConfig.php',
            ),
        'CategoryRepository' =>
            array(
                0 => '/module/dcshop/common/classes/CategoryRepository.php',
            ),
        'ComputopPaygateWrapper' =>
            array(
                0 => '/module/dcshop/common/classes/ComputopPaygateWrapper.php',
            ),
        'Country' =>
            array(
                0 => '/module/dcshop/common/classes/Country.php',
            ),
        'CountryCollection' =>
            array(
                0 => '/module/dcshop/common/classes/CountryCollection.php',
            ),
        'CountryConfig' =>
            array(
                0 => '/module/dcshop/common/classes/CountryConfig.php',
            ),
        'CountryRepository' =>
            array(
                0 => '/module/dcshop/common/classes/CountryRepository.php',
            ),
        'CouponApplicator' =>
            array(
                0 => '/module/dcshop/common/classes/CouponApplicator.php',
            ),
        'CouponHeader' =>
            array(
                0 => '/module/dcshop/common/classes/CouponHeader.php',
            ),
        'CouponHeaderCollection' =>
            array(
                0 => '/module/dcshop/common/classes/CouponHeaderCollection.php',
            ),
        'CouponHeaderConfig' =>
            array(
                0 => '/module/dcshop/common/classes/CouponHeaderConfig.php',
            ),
        'CouponHeaderRepository' =>
            array(
                0 => '/module/dcshop/common/classes/CouponHeaderRepository.php',
            ),
        'CouponLine' =>
            array(
                0 => '/module/dcshop/common/classes/CouponLine.php',
            ),
        'CouponLineCollection' =>
            array(
                0 => '/module/dcshop/common/classes/CouponLineCollection.php',
            ),
        'CouponLineConfig' =>
            array(
                0 => '/module/dcshop/common/classes/CouponLineConfig.php',
            ),
        'CouponLineRepository' =>
            array(
                0 => '/module/dcshop/common/classes/CouponLineRepository.php',
            ),
        'CurrShopConfiguration' =>
            array(
                0 => '/module/dcshop/common/classes/CurrShopConfiguration.php',
            ),
        'Customer' =>
            array(
                0 => '/module/dcshop/common/classes/Customer.php',
            ),
        'CustomerCollection' =>
            array(
                0 => '/module/dcshop/common/classes/CustomerCollection.php',
            ),
        'CustomerConfig' =>
            array(
                0 => '/module/dcshop/common/classes/CustomerConfig.php',
            ),
        'CustomerPermissionGroupDecorator' =>
            array(
                0 => '/module/dcshop/common/classes/CustomerPermissionGroupDecorator.php',
            ),
        'CustomerPseudoPayData' =>
            array(
                0 => '/module/dcshop/common/classes/CustomerPseudoPayData.php',
            ),
        'CustomerPseudoPayDataCollection' =>
            array(
                0 => '/module/dcshop/common/classes/CustomerPseudoPayDataCollection.php',
            ),
        'CustomerPseudoPayDataConfig' =>
            array(
                0 => '/module/dcshop/common/classes/CustomerPseudoPayDataConfig.php',
            ),
        'CustomerPseudoPayDataRepository' =>
            array(
                0 => '/module/dcshop/common/classes/CustomerPseudoPayDataRepository.php',
            ),
        'CustomerRepository' =>
            array(
                0 => '/module/dcshop/common/classes/CustomerRepository.php',
            ),
        'DefaultItemAvailabilityProvider' =>
            array(
                0 => '/module/dcshop/common/classes/DefaultItemAvailabilityProvider.php',
            ),
        'DefaultOrder' =>
            array(
                0 => '/module/dcshop/common/classes/DefaultOrder.php',
            ),
        'GenericInvoiceDiscount' =>
            array(
                0 => '/module/dcshop/common/classes/GenericInvoiceDiscount.php',
            ),
        'GenericLineDiscount' =>
            array(
                0 => '/module/dcshop/common/classes/GenericLineDiscount.php',
            ),
        'GenericUserBasket' =>
            array(
                0 => '/module/dcshop/common/classes/GenericUserBasket.php',
            ),
        'GenericUserItemPermissionProvider' =>
            array(
                0 => '/module/dcshop/common/classes/GenericUserItemPermissionProvider.php',
            ),
        'GenericViewModel' =>
            array(
                0 => '/module/dcshop/common/classes/GenericViewModel.php',
            ),
        'GraduatedItemPriceData' =>
            array(
                0 => '/module/dcshop/common/classes/GraduatedItemPriceData.php',
            ),
        'InventoryStrategyFactory' =>
            array(
                0 => '/module/dcshop/common/classes/InventoryStrategyFactory.php',
            ),
        'InvoiceCollection' =>
            array(
                0 => '/module/dcshop/common/classes/InvoiceCollection.php',
            ),
        'InvoiceConfig' =>
            array(
                0 => '/module/dcshop/common/classes/InvoiceConfig.php',
            ),
        'InvoiceDiscount' =>
            array(
                0 => '/module/dcshop/common/classes/InvoiceDiscount.php',
            ),
        'InvoiceDiscountCollection' =>
            array(
                0 => '/module/dcshop/common/classes/InvoiceDiscountCollection.php',
            ),
        'InvoiceDiscountConfig' =>
            array(
                0 => '/module/dcshop/common/classes/InvoiceDiscountConfig.php',
            ),
        'InvoiceDiscountRepository' =>
            array(
                0 => '/module/dcshop/common/classes/InvoiceDiscountRepository.php',
            ),
        'InvoiceDocument' =>
            array(
                0 => '/module/dcshop/common/classes/InvoiceDocument.php',
            ),
        'InvoiceLine' =>
            array(
                0 => '/module/dcshop/common/classes/InvoiceLine.php',
            ),
        'InvoiceLineCollection' =>
            array(
                0 => '/module/dcshop/common/classes/InvoiceLineCollection.php',
            ),
        'InvoiceLineConfig' =>
            array(
                0 => '/module/dcshop/common/classes/InvoiceLineConfig.php',
            ),
        'InvoiceLineRepository' =>
            array(
                0 => '/module/dcshop/common/classes/InvoiceLineRepository.php',
            ),
        'InvoiceRepository' =>
            array(
                0 => '/module/dcshop/common/classes/InvoiceRepository.php',
            ),
        'ItemOrderbuttonbuilder' =>
            array(
                0 => '/module/dcshop/common/classes/ItemOrderbuttonbuilder.php',
            ),
        'ItemInventoryProvider' =>
            array(
                0 => '/module/dcshop/common/classes/ItemInventoryProvider.php',
            ),
        'ItemPriceData' =>
            array(
                0 => '/module/dcshop/common/classes/ItemPriceData.php',
            ),
        'NavOrderCollection' =>
            array(
                0 => '/module/dcshop/common/classes/NavOrderCollection.php',
            ),
        'NavOrderConfig' =>
            array(
                0 => '/module/dcshop/common/classes/NavOrderConfig.php',
            ),
        'NavOrderDocument' =>
            array(
                0 => '/module/dcshop/common/classes/NavOrderDocument.php',
            ),
        'NavOrderLine' =>
            array(
                0 => '/module/dcshop/common/classes/NavOrderLine.php',
            ),
        'NavOrderLineCollection' =>
            array(
                0 => '/module/dcshop/common/classes/NavOrderLineCollection.php',
            ),
        'NavOrderLineConfig' =>
            array(
                0 => '/module/dcshop/common/classes/NavOrderLineConfig.php',
            ),
        'NavOrderLineRepository' =>
            array(
                0 => '/module/dcshop/common/classes/NavOrderLineRepository.php',
            ),
        'NavOrderRepository' =>
            array(
                0 => '/module/dcshop/common/classes/NavOrderRepository.php',
            ),
        'NAVVariantsInventoryStrategy' =>
            array(
                0 => '/module/dcshop/common/classes/NAVVariantsInventoryStrategy.php',
            ),
        'OrderCouponDTO' =>
            array(
                0 => '/module/dcshop/common/classes/OrderCouponDTO.php',
            ),
        'OrderItem' =>
            array(
                0 => '/module/dcshop/common/classes/OrderItem.php',
            ),
        'OrderItemCollection' =>
            array(
                0 => '/module/dcshop/common/classes/OrderItemCollection.php',
            ),
        'OrderPayData' =>
            array(
                0 => '/module/dcshop/common/classes/OrderPayData.php',
            ),
        'PaymentOption' =>
            array(
                0 => '/module/dcshop/common/classes/PaymentOption.php',
            ),
        'PaymentOptionCollection' =>
            array(
                0 => '/module/dcshop/common/classes/PaymentOptionCollection.php',
            ),
        'PaymentOptionConfig' =>
            array(
                0 => '/module/dcshop/common/classes/PaymentOptionConfig.php',
            ),
        'PaymentOptionRepository' =>
            array(
                0 => '/module/dcshop/common/classes/PaymentOptionRepository.php',
            ),
        'PriceProvider' =>
            array(
                0 => '/module/dcshop/common/classes/PriceProvider.php',
            ),
        'PriceStrategyBase' =>
            array(
                0 => '/module/dcshop/common/classes/PriceStrategyBase.php',
            ),
        'RelatedDocumentFinderService' =>
            array(
                0 => '/module/dcshop/common/classes/RelatedDocumentFinderService.php',
            ),
        'SalesPrice' =>
            array(
                0 => '/module/dcshop/common/classes/SalesPrice.php',
            ),
        'SalesPriceCollection' =>
            array(
                0 => '/module/dcshop/common/classes/SalesPriceCollection.php',
            ),
        'SalesPriceConfig' =>
            array(
                0 => '/module/dcshop/common/classes/SalesPriceConfig.php',
            ),
        'SalesPriceRepository' =>
            array(
                0 => '/module/dcshop/common/classes/SalesPriceRepository.php',
            ),
        'SelectionCriteriaValidationService' =>
            array(
                0 => '/module/dcshop/common/classes/SelectionCriteriaValidationService.php',
            ),
        'ShipmentAddress' =>
            array(
                0 => '/module/dcshop/common/classes/ShipmentAddress.php',
            ),
        'ShipmentAddressCollection' =>
            array(
                0 => '/module/dcshop/common/classes/ShipmentAddressCollection.php',
            ),
        'ShipmentAddressConfig' =>
            array(
                0 => '/module/dcshop/common/classes/ShipmentAddressConfig.php',
            ),
        'ShipmentAddressRepository' =>
            array(
                0 => '/module/dcshop/common/classes/ShipmentAddressRepository.php',
            ),
        'ShipmentCollection' =>
            array(
                0 => '/module/dcshop/common/classes/ShipmentCollection.php',
            ),
        'ShipmentConfig' =>
            array(
                0 => '/module/dcshop/common/classes/ShipmentConfig.php',
            ),
        'ShipmentDocument' =>
            array(
                0 => '/module/dcshop/common/classes/ShipmentDocument.php',
            ),
        'ShipmentLine' =>
            array(
                0 => '/module/dcshop/common/classes/ShipmentLine.php',
            ),
        'ShipmentLineCollection' =>
            array(
                0 => '/module/dcshop/common/classes/ShipmentLineCollection.php',
            ),
        'ShipmentLineConfig' =>
            array(
                0 => '/module/dcshop/common/classes/ShipmentLineConfig.php',
            ),
        'ShipmentLineRepository' =>
            array(
                0 => '/module/dcshop/common/classes/ShipmentLineRepository.php',
            ),
        'ShipmentRepository' =>
            array(
                0 => '/module/dcshop/common/classes/ShipmentRepository.php',
            ),
        'ShippingOption' =>
            array(
                0 => '/module/dcshop/common/classes/ShippingOption.php',
            ),
        'ShippingOptionCollection' =>
            array(
                0 => '/module/dcshop/common/classes/ShippingOptionCollection.php',
            ),
        'ShippingOptionConfig' =>
            array(
                0 => '/module/dcshop/common/classes/ShippingOptionConfig.php',
            ),
        'ShippingOptionRepository' =>
            array(
                0 => '/module/dcshop/common/classes/ShippingOptionRepository.php',
            ),
        'Shop' =>
            array(
                0 => '/module/dcshop/common/classes/Shop.php',
            ),
        'ShopCollection' =>
            array(
                0 => '/module/dcshop/common/classes/ShopCollection.php',
            ),
        'ShopConfig' =>
            array(
                0 => '/module/dcshop/common/classes/ShopConfig.php',
            ),
        'ShopLanguage' =>
            array(
                0 => '/module/dcshop/common/classes/ShopLanguage.php',
            ),
        'ShopLanguageCollection' =>
            array(
                0 => '/module/dcshop/common/classes/ShopLanguageCollection.php',
            ),
        'ShopLanguageConfig' =>
            array(
                0 => '/module/dcshop/common/classes/ShopLanguageConfig.php',
            ),
        'ShopLanguageRepository' =>
            array(
                0 => '/module/dcshop/common/classes/ShopLanguageRepository.php',
            ),
        'ShopRepository' =>
            array(
                0 => '/module/dcshop/common/classes/ShopRepository.php',
            ),
        'SimplePriceStrategy' =>
            array(
                0 => '/module/dcshop/common/classes/SimplePriceStrategy.php',
            ),
        'StdOrderFrontController' =>
            array(
                0 => '/module/dcshop/common/classes/StdOrderFrontController.php',
            ),
        'StdOrderHelper' =>
            array(
                0 => '/module/dcshop/common/classes/StdOrderHelper.php',
            ),
        'SubscriptionCustomerLink' =>
            array(
                0 => '/module/dcshop/common/classes/SubscriptionCustomerLink.php',
                1 => '/module/dcshop/subscriptions/classes/SubscriptionCustomerLink.php',
            ),
        'SubscriptionCustomerLinkConfig' =>
            array(
                0 => '/module/dcshop/common/classes/SubscriptionCustomerLinkConfig.php',
                1 => '/module/dcshop/subscriptions/classes/SubscriptionCustomerLinkConfig.php',
            ),
        'SubscriptionOrderChainManager' =>
            array(
                0 => '/module/dcshop/common/classes/SubscriptionOrderChainManager.php',
            ),
        'SubscriptionOrderStep1HTMLBuilder' =>
            array(
                0 => '/module/dcshop/common/classes/SubscriptionOrderStep1HTMLBuilder.php',
            ),
        'TextModule' =>
            array(
                0 => '/module/dcshop/common/classes/TextModule.php',
            ),
        'TextModuleCollection' =>
            array(
                0 => '/module/dcshop/common/classes/TextModuleCollection.php',
            ),
        'TextModuleConfig' =>
            array(
                0 => '/module/dcshop/common/classes/TextModuleConfig.php',
            ),
        'TextModuleRepository' =>
            array(
                0 => '/module/dcshop/common/classes/TextModuleRepository.php',
            ),
        'User' =>
            array(
                0 => '/module/dcshop/common/classes/User.php',
            ),
        'UserBasketListener' =>
            array(
                0 => '/module/dcshop/common/classes/UserBasketListener.php',
            ),
        'UserBasketLoginHandler' =>
            array(
                0 => '/module/dcshop/common/classes/UserBasketLoginHandler.php',
            ),
        'UserBasketPersistenceHandler' =>
            array(
                0 => '/module/dcshop/common/classes/UserBasketPersistenceHandler.php',
            ),
        'UserBasketRepository' =>
            array(
                0 => '/module/dcshop/common/classes/UserBasketRepository.php',
            ),
        'UserCollection' =>
            array(
                0 => '/module/dcshop/common/classes/UserCollection.php',
            ),
        'UserConfig' =>
            array(
                0 => '/module/dcshop/common/classes/UserConfig.php',
            ),
        'UserRepository' =>
            array(
                0 => '/module/dcshop/common/classes/UserRepository.php',
            ),
        'VATManager' =>
            array(
                0 => '/module/dcshop/common/classes/VATManager.php',
            ),
        'WebshopItem' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItem.php',
            ),
        'WebshopItemBuilder' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemBuilder.php',
            ),
        'WebshopItemCategoryDecorator' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemCategoryDecorator.php',
            ),
        'WebshopItemCollection' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemCollection.php',
            ),
        'WebshopItemConfig' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemConfig.php',
            ),
        'WebshopItemNAVVariantDecorator' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemNAVVariantDecorator.php',
            ),
        'WebshopItemOrderableEntityDecorator' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemOrderableEntityDecorator.php',
            ),
        'WebshopItemRepository' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemRepository.php',
            ),
        'WebshopItemVariant' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemVariant.php',
            ),
        'WebshopItemVariantCollection' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemVariantCollection.php',
            ),
        'WebshopItemVariantConfig' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemVariantConfig.php',
            ),
        'WebshopItemVariantDecorator' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemVariantDecorator.php',
            ),
        'WebshopItemVariantRepository' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemVariantRepository.php',
            ),
        'WebshopItemVariantService' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemVariantService.php',
            ),
        'WebshopItemWithImagesDecorator' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopItemWithImagesDecorator.php',
            ),
        'WebshopOrderCollection' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopOrderCollection.php',
            ),
        'WebshopOrderConfig' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopOrderConfig.php',
            ),
        'WebshopOrderDocument' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopOrderDocument.php',
            ),
        'WebshopOrderLine' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopOrderLine.php',
            ),
        'WebshopOrderLineCollection' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopOrderLineCollection.php',
            ),
        'WebshopOrderLineConfig' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopOrderLineConfig.php',
            ),
        'WebshopOrderLineRepository' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopOrderLineRepository.php',
            ),
        'WebshopOrderRepository' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopOrderRepository.php',
            ),
        'WebshopVariantsInventoryStrategy' =>
            array(
                0 => '/module/dcshop/common/classes/WebshopVariantsInventoryStrategy.php',
            ),
        'CustomerInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/CustomerInterface.php',
            ),
        'CustomerWithPermissionGroupsInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/CustomerWithPermissionGroupsInterface.php',
            ),
        'Discount' =>
            array(
                0 => '/module/dcshop/common/interfaces/Discount.php',
            ),
        'DocLineModelDBConfigInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/DocLineModelDBConfigInterface.php',
            ),
        'DocLineRepositoryInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/DocLineRepositoryInterface.php',
            ),
        'DocumentLineRepository' =>
            array(
                0 => '/module/dcshop/common/interfaces/DocumentLineRepository.php',
            ),
        'DocumentModelDBConfigInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/DocumentModelDBConfigInterface.php',
            ),
        'DocumentRepository' =>
            array(
                0 => '/module/dcshop/common/interfaces/DocumentRepository.php',
            ),
        'DocumentRepositoryInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/DocumentRepositoryInterface.php',
            ),
        'DynCom\dc\common\interfaces\GenericCollectionInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/DynCom\dc\common\interfaces\GenericCollectionInterface.php',
            ),
        'GenericDocLineInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/GenericDocLineInterface.php',
            ),
        'GenericDocumentInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/GenericDocumentInterface.php',
            ),
        'GenericRepositoryInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/GenericRepositoryInterface.php',
            ),
        'HasItemConfigInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/HasItemConfigInterface.php',
            ),
        'IHasVATProdPostingGroup' =>
            array(
                0 => '/module/dcshop/common/interfaces/IHasVATProdPostingGroup.php',
            ),
        'IPriceProvider' =>
            array(
                0 => '/module/dcshop/common/interfaces/IPriceProvider.php',
            ),
        'IPriceProviderStrategy' =>
            array(
                0 => '/module/dcshop/common/interfaces/IPriceProviderStrategy.php',
            ),
        'ItemAvailabilityProvider' =>
            array(
                0 => '/module/dcshop/common/interfaces/ItemAvailabilityProvider.php',
            ),
        'ItemAvailabilityStrategyInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/ItemAvailabilityStrategyInterface.php',
            ),
        'ItemInventoryStrategyInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/ItemInventoryStrategyInterface.php',
            ),
        'ItemPriceDataInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/ItemPriceDataInterface.php',
            ),
        'IVATManager' =>
            array(
                0 => '/module/dcshop/common/interfaces/IVATManager.php',
            ),
        'DynCom\dc\common\interfaces\ModelDBConfigInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/DynCom\dc\common\interfaces\ModelDBConfigInterface.php',
            ),
        'Order' =>
            array(
                0 => '/module/dcshop/common/interfaces/Order.php',
            ),
        'OrderableEntityInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/OrderableEntityInterface.php',
            ),
        'OrderItemInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/OrderItemInterface.php',
            ),
        'OrderStep' =>
            array(
                0 => '/module/dcshop/common/interfaces/OrderStep.php',
            ),
        'TemplatingInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/TemplatingInterface.php',
            ),
        'TextProviderInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/TextProviderInterface.php',
            ),
        'UserBasket' =>
            array(
                0 => '/module/dcshop/common/interfaces/UserBasket.php',
            ),
        'UserItemPermissionProvider' =>
            array(
                0 => '/module/dcshop/common/interfaces/UserItemPermissionProvider.php',
            ),
        'WebshopItemInterface' =>
            array(
                0 => '/module/dcshop/common/interfaces/WebshopItemInterface.php',
            ),
        'WebshopItemWithCategories' =>
            array(
                0 => '/module/dcshop/common/interfaces/WebshopItemWithCategories.php',
            ),
        'WebshopItemWithImages' =>
            array(
                0 => '/module/dcshop/common/interfaces/WebshopItemWithImages.php',
            ),
        'WebshopItemWithVariants' =>
            array(
                0 => '/module/dcshop/common/interfaces/WebshopItemWithVariants.php',
            ),
        'docLineRepositoryTrait' =>
            array(
                0 => '/module/dcshop/common/traits/docLineRepositoryTrait.php',
            ),
        'documentConfigTrait' =>
            array(
                0 => '/module/dcshop/common/traits/documentConfigTrait.php',
            ),
        'documentRepositoryTrait' =>
            array(
                0 => '/module/dcshop/common/traits/documentRepositoryTrait.php',
            ),
        'documentTrait' =>
            array(
                0 => '/module/dcshop/common/traits/documentTrait.php',
            ),
        'DecoratorTestInterface' =>
            array(
                0 => '/module/dcshop/common/traits/genericDecoratorTrait.php',
            ),
        'genericDecoratorTrait' =>
            array(
                0 => '/module/dcshop/common/traits/genericDecoratorTrait.php',
            ),
        'DecoratorTestDecorator' =>
            array(
                0 => '/module/dcshop/common/traits/genericDecoratorTrait.php',
            ),
        'DecoratorTestDecoratee' =>
            array(
                0 => '/module/dcshop/common/traits/genericDecoratorTrait.php',
            ),
        'Test3' =>
            array(
                0 => '/module/dcshop/common/traits/genericDecoratorTrait.php',
            ),
        'genericDocLineTrait' =>
            array(
                0 => '/module/dcshop/common/traits/genericDocLineTrait.php',
            ),
        'hasItemTrait' =>
            array(
                0 => '/module/dcshop/common/traits/hasItemTrait.php',
            ),
        'PomSoapClient' =>
            array(
                0 => '/module/dcshop/newsletter_copernica/soapclient.php',
            ),
        'RetShipmentCollection' =>
            array(
                0 => '/module/dcshop/rma/classes/RetShipmentCollection.php',
            ),
        'RetShipmentConfig' =>
            array(
                0 => '/module/dcshop/rma/classes/RetShipmentConfig.php',
            ),
        'RetShipmentDocument' =>
            array(
                0 => '/module/dcshop/rma/classes/RetShipmentDocument.php',
            ),
        'RetShipmentFinderService' =>
            array(
                0 => '/module/dcshop/rma/classes/RetShipmentFinderService.php',
            ),
        'RetShipmentLine' =>
            array(
                0 => '/module/dcshop/rma/classes/RetShipmentLine.php',
            ),
        'RetShipmentLineCollection' =>
            array(
                0 => '/module/dcshop/rma/classes/RetShipmentLineCollection.php',
            ),
        'RetShipmentLineConfig' =>
            array(
                0 => '/module/dcshop/rma/classes/RetShipmentLineConfig.php',
            ),
        'RetShipmentLineRepository' =>
            array(
                0 => '/module/dcshop/rma/classes/RetShipmentLineRepository.php',
            ),
        'RetShipmentRepository' =>
            array(
                0 => '/module/dcshop/rma/classes/RetShipmentRepository.php',
            ),
        'RetShipmentView' =>
            array(
                0 => '/module/dcshop/rma/classes/RetShipmentView.php',
            ),
        'ReturnReason' =>
            array(
                0 => '/module/dcshop/rma/classes/ReturnReason.php',
            ),
        'ReturnReasonCollection' =>
            array(
                0 => '/module/dcshop/rma/classes/ReturnReasonCollection.php',
            ),
        'ReturnReasonConfig' =>
            array(
                0 => '/module/dcshop/rma/classes/ReturnReasonConfig.php',
            ),
        'ReturnReasonRepository' =>
            array(
                0 => '/module/dcshop/rma/classes/ReturnReasonRepository.php',
            ),
        'RMAConfirmationMailPHTMLTemplate' =>
            array(
                0 => '/module/dcshop/rma/classes/RMAConfirmationMailPHTMLTemplate.php',
            ),
        'RMAConfirmationMailViewModel' =>
            array(
                0 => '/module/dcshop/rma/classes/RMAConfirmationMailViewModel.php',
            ),
        'RMAFrontController' =>
            array(
                0 => '/module/dcshop/rma/classes/RMAFrontController.php',
            ),
        'RMAGenericRMAPageViewModel' =>
            array(
                0 => '/module/dcshop/rma/classes/RMAGenericRMAPageViewModel.php',
            ),
        'RMAGenericViewModel' =>
            array(
                0 => '/module/dcshop/rma/classes/RMAGenericViewModel.php',
            ),
        'RMAGenericViewPHTMLTemplate' =>
            array(
                0 => '/module/dcshop/rma/classes/RMAGenericViewPHTMLTemplate.php',
            ),
        'RMAOrderHelper' =>
            array(
                0 => '/module/dcshop/rma/classes/RMAOrderHelper.php',
            ),
        'RMASearchController' =>
            array(
                0 => '/module/dcshop/rma/classes/RMASearchController.php',
            ),
        'RMASearchPHTMLTemplate' =>
            array(
                0 => '/module/dcshop/rma/classes/RMASearchPHTMLTemplate.php',
            ),
        'RMASearchViewModel' =>
            array(
                0 => '/module/dcshop/rma/classes/RMASearchViewModel.php',
            ),
        'RMAShipmentViewModel' =>
            array(
                0 => '/module/dcshop/rma/classes/RMAShipmentViewModel.php',
            ),
        'RMAShipmentViewPHTMLTemplate' =>
            array(
                0 => '/module/dcshop/rma/classes/RMAShipmentViewPHTMLTemplate.php',
            ),
        'ItemSubscriptionData' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/ItemSubscriptionData.php',
            ),
        'ItemSubscriptionDataBuilder' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/ItemSubscriptionDataBuilder.php',
            ),
        'SubscriptionCustomerLinkCollection' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionCustomerLinkCollection.php',
            ),
        'SubscriptionCustomerLinkRepository' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionCustomerLinkRepository.php',
            ),
        'SubscriptionDateCalculator' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionDateCalculator.php',
            ),
        'SubscriptionHeader' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionHeader.php',
            ),
        'SubscriptionHeaderCollection' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionHeaderCollection.php',
            ),
        'SubscriptionHeaderConfig' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionHeaderConfig.php',
            ),
        'SubscriptionHeaderRepository' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionHeaderRepository.php',
            ),
        'SubscriptionItemcardButtonViewModel' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionItemcardButtonViewModel.php',
            ),
        'SubscriptionItemcardButtonViewPHTMLTemplate' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionItemcardButtonViewTemplate.php',
            ),
        'SubscriptionItemDecorator' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionItemDecorator.php',
            ),
        'SubscriptionItemLink' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionItemLink.php',
            ),
        'SubscriptionItemLinkCollection' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionItemLinkCollection.php',
            ),
        'SubscriptionItemLinkConfig' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionItemLinkConfig.php',
            ),
        'SubscriptionItemLinkRepository' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionItemLinkRepository.php',
            ),
        'SubscriptionItemPriceDecorator' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionItemPriceDecorator.php',
            ),
        'SubscriptionOrderButtonFormFactory' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionOrderButtonFormFactory.php',
            ),
        'SubscriptionRepository' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionRepository.php',
            ),
        'SubscriptionRequest' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionRequest.php',
            ),
        'SubscriptionSequenceStep' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionSequenceStep.php',
            ),
        'SubscriptionSequenceStepCollection' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionSequenceStepCollection.php',
            ),
        'SubscriptionSequenceStepConfig' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionSequenceStepConfig.php',
            ),
        'SubscriptionSequenceStepRepository' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionSequenceStepRepository.php',
            ),
        'SubscriptionsFrontController' =>
            array(
                0 => '/module/dcshop/subscriptions/classes/SubscriptionsFrontController.php',
            ),
        'SubscriptionItemPriceDataInterface' =>
            array(
                0 => '/module/dcshop/subscriptions/interfaces/SubscriptionItemPriceDataInterface.php',
            ),
        'ActiveActionItemRuleDiscountRepositoryInterface' =>
            array (
                0 => '/module/RuleEngine/ActiveActionItemRuleDiscountRepositoryInterface.php',
            ),
        'ActiveActionItemRuleDiscountRepository' =>
            array(
                0 => '/module/RuleEngine/ActiveActionItemRuleDiscountRepository.php',
            ),
        'MockActiveActionItemRuleDiscountRepository' =>
            array(
                0 => '/module/RuleEngine/MockActiveActionItemRuleDiscountRepository.php',
            ),
        'AlwaysTrueRuleCondition' =>
            array(
                0 => '/module/RuleEngine/AlwaysTrueRuleCondition.php',
            ),
        'ClosureRuleCondition' =>
            array(
                0 => '/module/RuleEngine/ClosureRuleCondition.php',
            ),
        'closureRuleConditionTrait' =>
            array(
                0 => '/module/RuleEngine/closureRuleConditionTrait.php',
            ),
        'GenericBinaryAndRuleCondition' =>
            array(
                0 => '/module/RuleEngine/GenericBinaryAndRuleCondition.php',
            ),
        'GenericBinaryGTERuleCondition' =>
            array(
                0 => '/module/RuleEngine/GenericBinaryGTERuleCondition.php',
            ),
        'GenericBinaryGTRuleCondition' =>
            array(
                0 => '/module/RuleEngine/GenericBinaryGTRuleCondition.php',
            ),
        'GenericBinaryLTERuleCondition' =>
            array(
                0 => '/module/RuleEngine/GenericBinaryLTERuleCondition.php',
            ),
        'GenericBinaryLTRuleCondition' =>
            array(
                0 => '/module/RuleEngine/GenericBinaryLTRuleCondition.php',
            ),
        'GenericBinaryNAndRuleCondition' =>
            array(
                0 => '/module/RuleEngine/GenericBinaryNAndRuleCondition.php',
            ),
        'GenericBinaryNOrRuleCondition' =>
            array(
                0 => '/module/RuleEngine/GenericBinaryNOrRuleCondition.php',
            ),
        'GenericBinaryOrRuleCondition' =>
            array(
                0 => '/module/RuleEngine/GenericBinaryOrRuleCondition.php',
            ),
        'GenericClosureRuleCondition' =>
            array(
                0 => '/module/RuleEngine/GenericClosureRuleCondition.php',
            ),
        'GenericRule' =>
            array(
                0 => '/module/RuleEngine/GenericRule.php',
            ),
        'GenericRuleAction' =>
            array(
                0 => '/module/RuleEngine/GenericRuleAction.php',
            ),
        'GenericRuleConditionList' =>
            array(
                0 => '/module/RuleEngine/GenericRuleConditionList.php',
            ),
        'GenericRuleContext' =>
            array(
                0 => '/module/RuleEngine/GenericRuleContext.php',
            ),
        'GenericRuleContextVariable' =>
            array(
                0 => '/module/RuleEngine/GenericRuleContextVariable.php',
            ),
        'GenericRuleEngine' =>
            array(
                0 => '/module/RuleEngine/GenericRuleEngine.php',
            ),
        'GenericRuleList' =>
            array(
                0 => '/module/RuleEngine/GenericRuleList.php',
            ),
        'ReturnsValueRuleAction' =>
            array(
                0 => '/module/RuleEngine/ReturnsValueRuleAction.php',
            ),
        'Rule' =>
            array(
                0 => '/module/RuleEngine/Rule.php',
            ),
        'RuleAction' =>
            array(
                0 => '/module/RuleEngine/RuleAction.php',
            ),
        'RuleBuilder' =>
            array(
                0 => '/module/RuleEngine/RuleBuilder.php',
            ),
        'RuleCondition' =>
            array(
                0 => '/module/RuleEngine/RuleCondition.php',
            ),
        'RuleConditionList' =>
            array(
                0 => '/module/RuleEngine/RuleConditionList.php',
            ),
        'ruleConditionTrait' =>
            array(
                0 => '/module/RuleEngine/ruleConditionTrait.php',
            ),
        'RuleContext' =>
            array(
                0 => '/module/RuleEngine/RuleContext.php',
            ),
        'RuleContextVariable' =>
            array(
                0 => '/module/RuleEngine/RuleContextVariable.php',
            ),
        'RuleEngine' =>
            array(
                0 => '/module/RuleEngine/RuleEngine.php',
            ),
        'RuleList' =>
            array(
                0 => '/module/RuleEngine/RuleList.php',
            ),
        'ShopAutoloader' =>
            array(
                0 => '/module/ShopAutoloader.php',
            ),
    );

    public function register()
    {
        spl_autoload_register([__NAMESPACE__ . '\\' . __CLASS__, 'resolve']);
    }

    public static function resolve($class)
    {
        if (isset(static::$map[$class])) {
            foreach (static::$map[$class] as $includePath) {
                include(rtrim(dirname(__DIR__),'/\\') . $includePath);
            }
        }
    }

}