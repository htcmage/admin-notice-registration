<?php
namespace HTCMage\AdminNoticeRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;

class SendMailToAdmin implements ObserverInterface
{

    const XML_PATH_EMAIL_RECIPIENT = 'trans_email/ident_general/email';
    protected $_transportBuilder;
    protected $inlineTranslation;
    protected $scopeConfig;
    protected $storeManager;
    protected $_escaper;
    protected $helperData;

    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        \HTCMage\AdminNoticeRegistration\Helper\Data $helperData
    ) {
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->helperData = $helperData;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getData('customer');
        $emailTemplate = $this->helperData->getGeneralConfig('admin_notice_registration');
        $listEmail = $this->helperData->getGeneralConfig('list_email_to');
        $listEmail = explode(',', $listEmail);

        $this->inlineTranslation->suspend();
        try
        {
            $error = false;

            $sender = [
                'name' => $this->_escaper->escapeHtml($customer->getFirstName() . $customer->getLastName()),
                'email' => $this->_escaper->escapeHtml($customer->getEmail()),
            ];
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($sender);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE; 

            $transport = $this->_transportBuilder
                ->setTemplateIdentifier( $emailTemplate )
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($sender)
                ->setReplyTo($sender['email']);

            foreach ($listEmail as $email) {
                $transport->addTo($email);
            }
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        }
        catch (\Exception $e)
        {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($e->getMessage());
        }

    }
}