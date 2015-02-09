<?php

namespace Backend\Modules\Forum\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Forum\Engine\Model as BackendForumModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/**
 * This action will delete a forumpost
 *
 * @author Glenn Coppens <glenn.coppens@gmail.com>
 */
class Delete extends BackendBaseActionDelete
{
    /**
     * The id of the category where is filtered on
     *
     * @var	int
     */
    private $categoryId;

    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendForumModel::exists($this->id)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // set category id
            $this->categoryId = \SpoonFilter::getGetValue('category', null, null, 'int');
            if ($this->categoryId == 0) {
                $this->categoryId = null;
            }

            // get data
            $this->record = (array) BackendForumModel::get($this->id);

            // delete item
            BackendForumModel::delete($this->id);

            // trigger event
            BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $this->id));

            // delete search indexes
            BackendSearchModel::removeIndex($this->getModule(), $this->id);

            // build redirect URL
            $redirectUrl = BackendModel::createURLForAction('Index') . '&report=deleted&var=' . urlencode($this->record['title']);

            // append to redirect URL
            if ($this->categoryId != null) {
                $redirectUrl .= '&category=' . $this->categoryId;
            }

            // item was deleted, so redirect
            $this->redirect($redirectUrl);
        } else {
            // something went wrong
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }
}
