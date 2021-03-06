<?php

namespace Backend\Modules\Forum\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Modules\Forum\Engine\Model as BackendForumModel;

/**
 * This widget will show the latest comments
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class Comments extends BackendBaseWidget
{
    /**
     * The comments
     *
     * @var	array
     */
    private $comments;

    /**
     * An array that contains the number of comments / status
     *
     * @var int
     */
    private $numCommentStatus;

    /**
     * Execute the widget
     */
    public function execute()
    {
        $this->setColumn('middle');
        $this->setPosition(0);
        $this->loadData();
        $this->parse();
        $this->display();
    }

    /**
     * Load the data
     */
    private function loadData()
    {
        $this->comments = BackendForumModel::getLatestComments('published', 5);
        $this->numCommentStatus = BackendForumModel::getCommentStatusCount();
    }

    /**
     * Parse into template
     */
    private function parse()
    {
        $this->tpl->assign('forumComments', $this->comments);

        // comments to moderate
        if (isset($this->numCommentStatus['moderation']) && (int) $this->numCommentStatus['moderation'] > 0) {
            $this->tpl->assign('forumNumCommentsToModerate', $this->numCommentStatus['moderation']);
        }
    }
}
