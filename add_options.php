
<?php

system('clear');
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Magento\Framework\App\Bootstrap;
use Magento\Framework\Indexer\StateInterface;
use Magento\Indexer\Model\Indexer;

try {
    require __DIR__ . '/app/bootstrap.php';

    $params = $_SERVER;
    $bootstrap = Bootstrap::create(BP, $params);
    $obj = $bootstrap->getObjectManager();
    $obj->get('Magento\Framework\Registry')->register('isSecureArea', true);
    $appState = $obj->get('\Magento\Framework\App\State');
    $appState->setAreaCode('adminhtml');

    $attributeCode = 'size';// @TODO: Set attribute code here

    $optionsToAdd = "XS,S,M,L,XL,XXL";// @TODO: Enlist the values for an attribute here, comma separated. Eg.: S,M,L,XL

    $values = explode(',', $optionsToAdd);

    $optionManagement = $obj->create('Magento\Catalog\Api\ProductAttributeOptionManagementInterface');
    $option = $obj->create('Magento\Eav\Api\Data\AttributeOptionInterface');
    $optionLabel = $obj->create('Magento\Eav\Api\Data\AttributeOptionLabelInterface');

    $sortOrder = 100;// @TODO: Set sort order here (optional)
    foreach ($values as $attributeValue) {
        $option = $obj->create('Magento\Eav\Api\Data\AttributeOptionInterface');
        $optionLabel = $obj->create('Magento\Eav\Api\Data\AttributeOptionLabelInterface');
        $option->setLabel($attributeValue);
        $option->setValue($attributeValue);
        $option->setSortOrder($sortOrder);
        $option->setIsDefault(0);

        $sortOrder++;

        try {
            if (!$optionManagement->add($attributeCode,$option)) {
                throw new \Exception('Couldn\'t add option for attribute '.$attributeCode.': '.$attributeValue);
            }
        } catch (\Exception $e) {
            throw new \Exception('Couldn\'t create option for attribute '.$attributeCode.': '.$attributeValue.' (['.get_class($e).']) '.$e->getMessage().')');
        }

        unset($option);
        unset($optionLabel);
    }
} catch (Exception $e) {
    echo $e->getMessage().PHP_EOL;
}
