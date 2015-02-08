<?php

namespace Backend\Modules\Forum\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Modules\Forum\Engine\Model as BackendForumModel;

/**
 * This is the index-action (default), it will display the overview of forum posts
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Index extends BackendBaseActionIndex
{
    /**
     * The category where is filtered on
     *
     * @var    array
     */
    private $category;

    /**
     * The id of the category where is filtered on
     *
     * @var    int
     */
    private $categoryId;

    /**
     * DataGrids
     *
     * @var    BackendDataGridDB
     */
    private $dgDrafts;
    private $dgPosts;
    private $dgRecent;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // set category id
        $this->categoryId = \SpoonFilter::getGetValue('category', null, null, 'int');
        if ($this->categoryId == 0) {
            $this->categoryId = null;
        } else {
            // get category
            $this->category = BackendForumModel::getCategory($this->categoryId);

            // reset
            if (empty($this->category)) {
                // reset GET to trick Spoon
                $_GET['category'] = null;

                // reset
                $this->categoryId = null;
            }
        }

        $this->loadDataGrids();
        $this->parse();
        $this->display();
    }

    /**
     * Loads the datagrid with all the posts
     */
    private function loadDataGridAllPosts()
    {
        // create datagrid
        $this->dgPosts = new BackendDataGridDB(
            BackendForumModel::QRY_DATAGRID_BROWSE,
            array('active')
        );

        // set headers
        $this->dgPosts->setHeaderLabels(
            array(
                'profile_id' => \SpoonFilter::ucfirst(BL::lbl('Profile')),
                'publish_on' => \SpoonFilter::ucfirst(BL::lbl('PublishedOn'))
            )
        );

        // hide columns
        $this->dgPosts->setColumnsHidden(array('revision_id', 'profile_id'));

        // sorting columns
        $this->dgPosts->setSortingColumns(array('publish_on', 'title', 'profile_id', 'comments'), 'publish_on');
        $this->dgPosts->setSortParameter('desc');

        // set column functions
        $this->dgPosts->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getLongDate'),
            array('[publish_on]'),
            'publish_on',
            true
        );

        // our JS needs to know an id, so we can highlight it
        $this->dgPosts->setRowAttributes(array('id' => 'row-[revision_id]'));

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // set column URLs
            $this->dgPosts->setColumnURL(
                'title',
                BackendModel::createURLForAction('Edit') .
                '&amp;id=[id]&amp;category=' . $this->categoryId
            );

            // add edit column
            $this->dgPosts->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createURLForAction('Edit') .
                '&amp;id=[id]&amp;category=' . $this->categoryId,
                BL::lbl('Edit')
            );
        }
    }

    

    /**
     * Loads the datagrid with the most recent posts.
     */
    private function loadDataGridRecentPosts()
    {
        // filter on category?
        if ($this->categoryId != null) {
            // create datagrid
            $this->dgRecent = new BackendDataGridDB(
                BackendForumModel::QRY_DATAGRID_BROWSE_RECENT_FOR_CATEGORY,
                array($this->categoryId, 'active', BL::getWorkingLanguage(), 4)
            );

            // set the URL
            $this->dgRecent->setURL('&amp;category=' . $this->categoryId, true);
        } else {
            // create datagrid
            $this->dgRecent = new BackendDataGridDB(
                BackendForumModel::QRY_DATAGRID_BROWSE_RECENT,
                array('active', BL::getWorkingLanguage(), 4)
            );
        }

        // set headers
        $this->dgRecent->setHeaderLabels(array('user_id' => \SpoonFilter::ucfirst(BL::lbl('Author'))));

        // hide columns
        $this->dgRecent->setColumnsHidden(array('revision_id'));

        // set paging
        $this->dgRecent->setPaging(false);

        // set column functions
        $this->dgRecent->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getLongDate'),
            array('[edited_on]'),
            'edited_on',
            true
        );
        $this->dgRecent->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getUser'),
            array('[user_id]'),
            'user_id',
            true
        );

        // our JS needs to know an id, so we can highlight it
        $this->dgRecent->setRowAttributes(array('id' => 'row-[revision_id]'));

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // set colum URLs
            $this->dgRecent->setColumnURL(
                'title',
                BackendModel::createURLForAction('Edit') .
                '&amp;id=[id]&amp;category=' . $this->categoryId
            );

            // add edit column
            $this->dgRecent->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createURLForAction('Edit') .
                '&amp;id=[id]&amp;category=' . $this->categoryId,
                BL::lbl('Edit')
            );
        }
    }

    /**
     * Loads the datagrids for the forumposts
     */
    private function loadDataGrids()
    {
        $this->loadDataGridAllPosts();

        // the most recent forumposts, only shown when we have more than 1 page in total
        if ($this->dgPosts->getNumResults() > $this->dgPosts->getPagingLimit()) {
            $this->loadDataGridRecentPosts();
        }
    }

    /**
     * Parse all datagrids
     */
    protected function parse()
    {
        parent::parse();

        // parse the datagrid for all forumposts
        $this->tpl->assign('dgPosts', (string) $this->dgPosts->getContent());

        // parse the datagrid for the most recent forumposts
        $this->tpl->assign('dgRecent', (is_object($this->dgRecent)) ? $this->dgRecent->getContent() : false);
    }
}
