<?php

namespace Frontend\Modules\Forum\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Forum\Engine\Model as FrontendForumModel;

/**
 * This is a widget with recent comments on all forum-posts
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class RecentComments extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {
        $this->tpl->assign('widgetForumRecentComments', FrontendForumModel::getRecentComments(5));
    }
}
