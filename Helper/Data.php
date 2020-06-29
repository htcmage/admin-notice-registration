<?php
/**
 * Created by HTCMage
 */
namespace HTCMage\AdminNoticeRegistration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @package HTCMage\AdminNoticeRegistration\Helper
 */
class Data extends AbstractHelper
{

    /**
     * Const
     */
    const PREFIX_MESSAGE_LOG = 'Module HTCMage_AdminNoticeRegistration has few bugs.';
    const KEY_ENABLE_MODULE_XML = 'admin_notice_registration/general/enable';
    const XML_PATH_GENERAL = 'admin_notice_registration/general/';

    /**
     * Write log
     *
     * @param string $message
     * @return void
     */
    public function writeLog($message)
    {
        $this->_logger->info(self::PREFIX_MESSAGE_LOG . $message);
    }

    /**
     * Is module enable
     *
     * @return string
     */
    public function isModuleEnable()
    {
        return $this->scopeConfig->getValue(
            self::KEY_ENABLE_MODULE_XML,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GENERAL . $code, $storeId);
    }

}
