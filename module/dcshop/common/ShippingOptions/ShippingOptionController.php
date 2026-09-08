<?php
/**
 * Created by PhpStorm.
 * User: lorenz
 * Date: 06.03.2018
 * Time: 09:56
 */

namespace DynCom\dc\dcShop\ShippingOptions;

use DynCom\dc\common\classes\NewValidator;
use DynCom\dc\dcShop\classes\CurrShopConfiguration;
use DynCom\dc\dcShop\interfaces\UserBasket;
use DynCom\dc\dcShop\ShippingOptions\ShippingOption;
use DynCom\dc\dcShop\ShippingOptions\ShippingOptionConfig;
use DynCom\dc\dcShop\ShippingOptions\ShippingOptionRepository;
use DynCom\dc\regionalization\RegionalizedTextProvider;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ShippingOptionController
{
    protected const TEMPLATE_NAME_SHIPPING_OPTIONS_SELECT = 'shipping_options_select';
    protected const TEMPLATE_NAME_SHIPPING_OPTIONS_LIST = 'shipping_options_list';
    /**
     * @var ShippingOptionRepository
     */
    private $shippingOptionRepository;
    /**
     * @var RegionalizedTextProvider
     */
    private $regionalizedText;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var string  */
    private $templateDirectory;
    private $locale_code;

    /**
     * ShippingOptionController constructor.
     * @param ShippingOptionRepository $shippingOptionRepository
     * @param RegionalizedTextProvider $regionalizedText
     * @param $locale_code
     * @param LoggerInterface $logger
     */
    public function __construct(ShippingOptionRepository $shippingOptionRepository, RegionalizedTextProvider $regionalizedText, $locale_code, LoggerInterface $logger = NULL)
    {
        $this->shippingOptionRepository = $shippingOptionRepository;
        $this->templateDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'templates';
        $mustacheOptions = [
            'loader' => new \Mustache_Loader_FilesystemLoader($this->templateDirectory),
        ];
        $this->mustacheEngine = new \Mustache_Engine($mustacheOptions);
        $this->regionalizedText = $regionalizedText;
        $logger = $logger ?? new NullLogger();
        $this->logger = $logger;
        $this->locale_code = $locale_code;
    }

    /**
     * @param CurrShopConfiguration $configuration
     * @param UserBasket $basket
     * @param $countryCode
     * @param $postCode
     * @param null $coupon
     * @return String
     * @throws \ErrorException
     * @throws \Exception
     */
    public function getShippingOptions(CurrShopConfiguration $configuration, UserBasket $basket, $countryCode, $postCode, $coupon = NULL): string
    {
        $shippingOptions = $this->shippingOptionRepository->getAllForOrder($configuration, $basket, $countryCode, $postCode, $coupon);

        if (count($shippingOptions) == 0) {
            return '';
        }

        $firstShippingAgent = $shippingOptions->getFirst();

        if ($_POST["input_shipping_line_no"] == '' && $_SESSION['shipping_line_no'] == '') {
            $_POST["input_shipping_line_no"] = $firstShippingAgent->line_no;
        } elseif ($_POST["input_shipping_line_no"] == '' && $_SESSION['shipping_line_no'] != '') {
            $_POST["input_shipping_line_no"] = $_SESSION['shipping_line_no'];
        }

        $_SESSION['shipping_line_no'] = $firstShippingAgent->line_no;
        $_SESSION['shipping_cost'] = $firstShippingAgent->shipping_cost;
        $locale = \Locale::acceptFromHttp($this->locale_code);

        $viewModel = new ShippingOptionViewModel($this->regionalizedText, $locale, $configuration->getCurrencyCode());

        foreach ($shippingOptions as $shippingOption) {
            if ($_POST["input_shipping_line_no"] == $shippingOption->line_no) {
                $_SESSION['shipping_line_no'] = $shippingOption->line_no;
                $_SESSION['shipping_cost'] = $shippingOption->shipping_cost;
                $viewModel->setSelectedLineNo($shippingOption->line_no);
                break;
            }
        }

        $viewModel->shippingOptionCollection = $shippingOptions;
        $viewModel->setIsSelected();
        $viewModel->setIsSelectedOption();
        $viewModel->setIsChecked();
        if ($configuration->getShop()->getOrderOptionsDisplay() === 0) {
            //select
            $string = $this->mustacheEngine->render('' . self::TEMPLATE_NAME_SHIPPING_OPTIONS_SELECT . '', $viewModel);
        } else {
            //list
            $string = $this->mustacheEngine->render(self::TEMPLATE_NAME_SHIPPING_OPTIONS_LIST, $viewModel);
        }


        return $string;
    }
}