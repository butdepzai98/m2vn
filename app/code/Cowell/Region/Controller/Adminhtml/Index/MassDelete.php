<?php
namespace Cowell\Region\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Cowell\Region\Model\ResourceModel\Region\CollectionFactory;
use Cowell\Region\Model\RegionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        RegionFactory $regionFactory,
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    )
    {
        $this->regionFactory = $regionFactory;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $deleted = 0;
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        foreach ($collection as $item) {
            try {
                $deleteItem = $this->regionFactory->create()->load($item->getId());
                $deleteItem->delete();
                $deleted++;
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage(
                    __(
                        'A total of %1 record(s) haven\'t been deleted.',
                    )
                );
            }
        }
        if ($deleted) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $deleted)
            );
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
