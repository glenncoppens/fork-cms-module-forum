<?php

namespace Backend\Modules\Forum\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Forum\Engine\Model as BackendForumModel;

/**
 * This action will delete a category
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class DeleteCategory extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendForumModel::existsCategory($this->id)) {
            // get data
            $this->record = (array) BackendForumModel::getCategory($this->id);

            // allowed to delete the category?
            if (BackendForumModel::deleteCategoryAllowed($this->id)) {
                // call parent, this will probably add some general CSS/JS or other required files
                parent::execute();

                // delete item
                BackendForumModel::deleteCategory($this->id);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_delete_category', array('id' => $this->id));

                // category was deleted, so redirect
                $this->redirect(
                    BackendModel::createURLForAction('Categories') . '&report=deleted-category&var=' .
                    urlencode($this->record['title'])
                );
            } else {
                $this->redirect(
                    // delete category not allowed
                    BackendModel::createURLForAction('Categories') . '&error=delete-category-not-allowed&var=' .
                    urlencode($this->record['title'])
                );
            }
        } else {
            // something went wrong
            $this->redirect(BackendModel::createURLForAction('Categories') . '&error=non-existing');
        }
    }
}
