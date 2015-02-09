<?php

namespace Backend\Modules\Forum\Actions;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Modules\Forum\Engine\Model as BackendForumModel;
use Backend\Modules\Forum\Engine\Helper as BackendForumHelper;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Backend\Modules\Profiles\Engine\Model as BackendProfilesModel;


/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @author Glenn Coppens <glenn.coppens@gmail.com>
 */
class Edit extends BackendBaseActionEdit
{
    /**
     * The id of the category where is filtered on
     *
     * @var	int
     */
    private $categoryId;

    /**
     * DataGrid for the drafts
     *
     * @var	BackendDataGridDB
     */
    private $dgDrafts;

    /**
     * Profile array
     *
     * @var array
     */
    protected $profile;

    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exists
        if ($this->id !== null && BackendForumModel::exists($this->id)) {
            parent::execute();

            // set category id
            $this->categoryId = \SpoonFilter::getGetValue('category', null, null, 'int');
            if ($this->categoryId == 0) {
                $this->categoryId = null;
            }

            $this->getData();
            $this->loadRevisions();
            $this->loadForm();
            $this->validateForm();
            $this->parse();
            $this->display();
        } else {
            // no item found, throw an exception, because somebody is fucking with our URL
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * Get the data
     * If a revision-id was specified in the URL we load the revision and not the actual data.
     */
    private function getData()
    {
        $this->record = (array) BackendForumModel::get($this->id);



        // is there a revision specified?
        $revisionToLoad = $this->getParameter('revision', 'int');

        // if this is a valid revision
        if ($revisionToLoad !== null) {
            // overwrite the current record
            $this->record = (array) BackendForumModel::getRevision($this->id, $revisionToLoad);

            // show warning
            $this->tpl->assign('usingRevision', true);
        }

        // no item found, throw an exceptions, because somebody is fucking with our URL
        if (empty($this->record)) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }

        // get the profile
        $this->profile = (array) BackendProfilesModel::get($this->record['profile_id']);
        $this->record['profile'] = $this->profile;
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('edit');

        // set hidden values
        $rbtHiddenValues[] = array('label' => BL::lbl('Hidden'), 'value' => 'Y');
        $rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

        // get categories
        $categories = BackendForumModel::getCategories();
        $categories['new_category'] = \SpoonFilter::ucfirst(BL::getLabel('AddCategory'));

        // create elements
        $this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
        $this->frm
            ->addTextarea('text', $this->record['text'], null, null, true)
            ->setAttributes(array('style' => 'width: 100%;'));
        $this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
        $this->frm->addCheckbox('allow_comments', ($this->record['allow_comments'] === 'Y' ? true : false));
        $this->frm->addDropdown('category_id', $categories, $this->record['category_id']);
        if (count($categories) != 2) {
            $this->frm->getField('category_id')->setDefaultElement('');
        }
        $this->frm->addText(
            'tags',
            BackendTagsModel::getTags($this->URL->getModule(), $this->record['id']),
            null,
            'inputText tagBox',
            'inputTextError tagBox'
        );
        $this->frm->addDate('publish_on_date', $this->record['publish_on']);
        $this->frm->addTime('publish_on_time', date('H:i', $this->record['publish_on']));
    }

    /**
     * Load the datagrid with revisions
     */
    private function loadRevisions()
    {
        // create datagrid
        $this->dgRevisions = new BackendDataGridDB(
            BackendForumModel::QRY_DATAGRID_BROWSE_REVISIONS,
            array('archived', $this->record['id'])
        );

        // hide columns
        $this->dgRevisions->setColumnsHidden(array('id', 'revision_id'));

        // disable paging
        $this->dgRevisions->setPaging(false);

        // set headers
        $this->dgRevisions->setHeaderLabels(array(
            'profile_id' => \SpoonFilter::ucfirst(BL::lbl('By')),
            'edited_on' => \SpoonFilter::ucfirst(BL::lbl('LastEditedOn'))
        ));

        // set column-functions
        $this->dgRevisions->setColumnFunction(
            array(new BackendForumHelper(), 'getProfileProperty'),
            array('[profile_id]', 'email'),
            'profile_id'
        );
        $this->dgRevisions->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getTimeAgo'),
            array('[edited_on]'),
            'edited_on'
        );

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // set column URLs
            $this->dgRevisions->setColumnURL(
                'title',
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]&amp;revision=[revision_id]'
            );

            // add use column
            $this->dgRevisions->addColumn(
                'use_revision',
                null,
                BL::lbl('UseThisVersion'),
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]&amp;revision=[revision_id]',
                BL::lbl('UseThisVersion')
            );
        }
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        // get url
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
        $url404 = BackendModel::getURL(404);

        // parse additional variables
        if ($url404 != $url) {
            $this->tpl->assign('detailURL', SITE_URL . $url);
        }

        // assign the active record and additional variables
        $this->tpl->assign('item', $this->record);
        $this->tpl->assign('status', BL::lbl(\SpoonFilter::ucfirst($this->record['status'])));

        // assign revisions-datagrid
        $this->tpl->assign('revisions', ($this->dgRevisions->getNumResults() != 0) ? $this->dgRevisions->getContent() : false);

        // assign category
        if ($this->categoryId !== null) {
            $this->tpl->assign('categoryId', $this->categoryId);
        }
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        // is the form submitted?
        if ($this->frm->isSubmitted()) {
            // get the status
            $status = \SpoonFilter::getPostValue('status', array('active'), 'active');

            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
            $this->frm->getField('text')->isFilled(BL::err('FieldIsRequired'));
            $this->frm->getField('publish_on_date')->isValid(BL::err('DateIsInvalid'));
            $this->frm->getField('publish_on_time')->isValid(BL::err('TimeIsInvalid'));
            $this->frm->getField('category_id')->isFilled(BL::err('FieldIsRequired'));

            // no errors?
            if ($this->frm->isCorrect()) {
                // build item
                $item['id'] = $this->id;

                // this is used to let our model know the status (active, archive, draft) of the edited item
                $item['revision_id'] = $this->record['revision_id'];
                $item['category_id'] = (int) $this->frm->getField('category_id')->getValue();
                //$item['user_id'] = $this->frm->getField('user_id')->getValue();
                //$item['language'] = BL::getWorkingLanguage();
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['text'] = $this->frm->getField('text')->getValue();
                $item['publish_on'] = BackendModel::getUTCDate(
                    null,
                    BackendModel::getUTCTimestamp(
                        $this->frm->getField('publish_on_date'),
                        $this->frm->getField('publish_on_time')
                    )
                );
                $item['edited_on'] = BackendModel::getUTCDate();
                $item['hidden'] = $this->frm->getField('hidden')->getValue();
                $item['allow_comments'] = $this->frm->getField('allow_comments')->getChecked() ? 'Y' : 'N';
                $item['status'] = $status;
                $item['profile_id'] = $this->profile['id'];
                $item['url'] = $this->record['url'];

                // update the item
                $item['revision_id'] = BackendForumModel::update($item);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $item));

                // recalculate comment count so the new revision has the correct count
                BackendForumModel::reCalculateCommentCount(array($this->id));

                // save the tags
                BackendTagsModel::saveTags($item['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());

                // active
                if ($item['status'] == 'active') {

                    // edit search index
                    BackendSearchModel::saveIndex(
                        $this->getModule(),
                        $item['id'],
                        array('title' => $item['title'], 'text' => $item['text'])
                    );

                    // ping
//                    if (BackendModel::getModuleSetting($this->URL->getModule(), 'ping_services', false)) {
//                        BackendModel::ping(
//                            SITE_URL .
//                            BackendModel::getURLForBlock($this->URL->getModule(), 'detail') .
//                            '/' . $item['url']
//                        );
//                    }

                    // build URL
                    $redirectUrl = BackendModel::createURLForAction('Index') .
                                   '&report=edited&var=' . urlencode($item['title']) .
                                   '&id=' . $this->id . '&highlight=row-' . $item['revision_id'];
                }

                // append to redirect URL
                if ($this->categoryId != null) {
                    $redirectUrl .= '&category=' . $this->categoryId;
                }

                // everything is saved, so redirect to the overview
                $this->redirect($redirectUrl);
            }
        }
    }
}
