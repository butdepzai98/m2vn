<?php declare(strict_types=1);

namespace Cowell\Region\Controller\Adminhtml\Index;

use Cowell\Region\Model\RegionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;


class Save extends \Magento\Backend\App\Action
{


    protected $regionFactory;
    /**
     * @var \Cowell\Region\Model\ResourceModel\Region
     */
    protected $resourceModel;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Cowell\Region\Model\RegionFactory $regionFactory,
        \Cowell\Region\Model\ResourceModel\Region $resourceModel,
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->regionFactory = $regionFactory;
        $this->resourceModel = $resourceModel;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('region_id');

            $model = $this->regionFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Region no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
                $model->setData($data);
            try {
                $model->save();
                $this->resourceModel->saveRegionName([$model['dynamic_rows'][0]['locale'],$model['region_id'],$model['dynamic_rows'][0]['name']]);
                $this->messageManager->addSuccessMessage(__('You saved the Region.'));
                $this->dataPersistor->clear('cowell_region_index');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving.'));
            }

            $this->dataPersistor->set('cowell_region_index', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
