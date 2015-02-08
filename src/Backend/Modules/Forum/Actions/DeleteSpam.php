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
 * This action will delete a forumpost
 *
 * @author Tijs Verkoyen <tijs@verkoyen.eu>
 */
class DeleteSpam extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        BackendForumModel::deleteSpamComments();

        // item was deleted, so redirect
        $this->redirect(
            BackendModel::createURLForAction('Comments') .
            '&report=deleted-spam#tabSpam'
        );
    }
}
