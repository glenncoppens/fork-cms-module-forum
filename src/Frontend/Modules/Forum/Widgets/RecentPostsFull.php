<?php

namespace Frontend\Modules\Forum\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Forum\Engine\Model as FrontendForumModel;

/**
 * This is a widget with recent forum-posts
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class RecentPostsFull extends FrontendBaseWidget
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
        // get RSS-link
        $rssTitle = FrontendModel::getModuleSetting('Forum', 'rss_title_' . FRONTEND_LANGUAGE);
        $rssLink = FrontendNavigation::getURLForBlock('Forum', 'Rss');

        // add RSS-feed
        $this->header->addRssLink($rssTitle, $rssLink);

        // assign comments
        $this->tpl->assign(
            'widgetForumRecentPostsFull',
            FrontendForumModel::getAll(FrontendModel::getModuleSetting('Forum', 'recent_posts_full_num_items', 5))
        );
        $this->tpl->assign('widgetForumRecentPostsFullRssLink', $rssLink);
    }
}
