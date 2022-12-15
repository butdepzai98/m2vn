<?php
namespace Cowell\Region\Controller\Adminhtml\Index;

use Cowell\Region\Model\RegionFactory;
use Magento\Framework\Registry;
class Delete extends \Cowell\Region\Controller\Adminhtml\Action
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        RegionFactory $regionFactory

    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->regionFactory = $regionFactory;
        parent::__construct($context, $coreRegistry);
    }


    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('region_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->regionFactory->create();
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Region.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['region_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Region to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

