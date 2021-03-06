<?php

namespace Frontend\Modules\Forum\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Forum\Engine\Model as FrontendForumModel;

/**
 * This is the category-action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class Category extends FrontendBaseBlock
{
    /**
     * The posts
     *
     * @var    array
     */
    private $items;

    /**
     * The requested category
     *
     * @var    array
     */
    private $category;

    /**
     * The pagination array
     * It will hold all needed parameters, some of them need initialization
     *
     * @var    array
     */
    protected $pagination = array(
        'limit' => 10,
        'offset' => 0,
        'requested_page' => 1,
        'num_items' => null,
        'num_pages' => null
    );

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->getData();
        $this->parse();
    }

    /**
     * Load the data, don't forget to validate the incoming data
     */
    private function getData()
    {
        // get categories
        $categories = FrontendForumModel::getAllCategories();
        $possibleCategories = array();
        foreach ($categories as $category) {
            $possibleCategories[$category['url']] = $category['id'];
        }

        // requested category
        $requestedCategory = \SpoonFilter::getValue(
            $this->URL->getParameter(1, 'string'),
            array_keys($possibleCategories),
            'false'
        );

        // requested page
        $requestedPage = $this->URL->getParameter('page', 'int', 1);

        // validate category
        if ($requestedCategory == 'false') {
            $this->redirect(FrontendNavigation::getURL(404));
        }

        // set category
        $this->category = $categories[$possibleCategories[$requestedCategory]];

        // set URL and limit
        $this->pagination['url'] = FrontendNavigation::getURLForBlock('Forum', 'Category') . '/' . $requestedCategory;
        $this->pagination['limit'] = FrontendModel::getModuleSetting('Forum', 'overview_num_items', 10);

        // populate count fields in pagination
        $this->pagination['num_items'] = FrontendForumModel::getAllForCategoryCount($requestedCategory);
        $this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

        // redirect if the request page doesn't exists
        if ($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) {
            $this->redirect(
                FrontendNavigation::getURL(404)
            );
        }

        // populate calculated fields in pagination
        $this->pagination['requested_page'] = $requestedPage;
        $this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

        // get posts
        $this->items = FrontendForumModel::getAllForCategory(
            $requestedCategory,
            $this->pagination['limit'],
            $this->pagination['offset']
        );
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        // get RSS-link
        $rssTitle = FrontendModel::getModuleSetting('Forum', 'rss_title_' . FRONTEND_LANGUAGE);
        $rssLink = FrontendNavigation::getURLForBlock('Forum', 'Rss');

        // add RSS-feed
        $this->header->addRssLink($rssTitle, $rssLink);

        // add into breadcrumb
        $this->breadcrumb->addElement(\SpoonFilter::ucfirst(FL::lbl('Category')));
        $this->breadcrumb->addElement($this->category['label']);

        // set pageTitle
        $this->header->setPageTitle(\SpoonFilter::ucfirst(FL::lbl('Category')));
        $this->header->setPageTitle($this->category['label']);

        // advanced SEO-attributes
        if (isset($this->category['meta_data']['seo_index'])) {
            $this->header->addMetaData(
                array('name' => 'robots', 'content' => $this->category['meta_data']['seo_index'])
            );
        }
        if (isset($this->category['meta_data']['seo_follow'])) {
            $this->header->addMetaData(
                array('name' => 'robots', 'content' => $this->category['meta_data']['seo_follow'])
            );
        }

        // assign category
        $this->tpl->assign('category', $this->category);

        // assign posts
        $this->tpl->assign('items', $this->items);

        // parse the pagination
        $this->parsePagination();
    }
}
