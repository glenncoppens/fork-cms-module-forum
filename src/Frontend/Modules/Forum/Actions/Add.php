<?php

namespace Frontend\Modules\Forum\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Cookie as CommonCookie;
use Common\Uri as CommonUri;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Frontend\Modules\Forum\Engine\Model as FrontendForumModel;
use Frontend\Modules\Forum\Engine\Helper as FrontendForumHelper;

use \Parsedown as Parsedown;

/**
 * This is the add-action
 *
 * @author Glenn Coppens <glenn.coppens@gmail.com>
 */
class Add extends FrontendBaseBlock
{
    /**
     * Form instance
     *
     * @var FrontendForm
     */
    private $frm;

    /**
     * Unique url
     *
     * @var array
     */
    private $url;

    /**
     * The settings
     *
     * @var    array
     */
    private $settings;

    /**
	 * The profile
	 *
	 * @var FrontendProfilesProfile
	 */
    private $profile;


    /**
     * Execute the extra
     */
    public function execute()
    {
    	// profile logged in
        if (FrontendProfilesAuthentication::isLoggedIn()) {
	        parent::execute();
	        $this->tpl->assign('hideContentTitle', true);
	        $this->loadTemplate();
	        $this->getData();
	        $this->loadForm();
	        $this->validateForm();
	        $this->parse();
	    } else {
            // profile not logged in
            $this->redirect(
                FrontendNavigation::getURLForBlock(
                    'Profiles',
                    'Login'
                ) . '?queryString=' . FrontendNavigation::getURLForBlock('Forum', 'Add'),
                307
            );
        }

    }

    /**
     * Load the data, don't forget to validate the incoming data
     */
    private function getData()
    {
        // get settings
        $this->settings = FrontendModel::getModuleSettings('Forum');

        // profile
        $this->profile = FrontendProfilesAuthentication::getProfile();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new FrontendForm('addForm');

        // create elements
        $this->frm->addText('title')->setAttributes(array('required' => null));
        $this->frm->addTextarea('post', null, null, null, true)->setAttributes(array('required' => null));
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        // add current action to js data
        $this->addJSData('Settings', array('Action' => $this->getAction()));

        // add js
        $this->addJS('highlight-8.4/highlight.pack.js', false);
        $this->addJS('taboverride-4.0.2/taboverride.js', false);
        $this->addJS('Purify.js', false);

        // highlight theme
        $this->addCSS('highlight-8.4/styles/github.css', false);

        // parse form
        $this->frm->parse($this->tpl);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
    	// form is submitted
        if($this->frm->isSubmitted()) {

        	// ignore fields by hackers
        	$this->frm->cleanupFields();

        	// get form fields
        	$fields = $this->frm->getFields();

        	// title required
        	$fields['title']->isFilled(FL::err('TitleIsRequired'));
        	$fields['post']->isFilled(FL::err('PostIsRequired'));

        	// form is correct
        	if($this->frm->isCorrect()) {

        		// create url
        		$this->url = CommonUri::getUrl($fields['title']->getValue());
        		$this->url = FrontendForumModel::getUniqueUrl($this->url);

                // parse markdown
                $parsedown = new Parsedown();
                $parsedown->setBreaksEnabled(true);
                $markdown = $parsedown->text($fields['post']->getValue());
//                \Spoon::dump(\Spoonfilter::htmlentities($markdown));

    			// create post item
    			$post = array(
    				'id' => FrontendForumModel::getMaximumId() + 1,
    				'title' => $fields['title']->getValue(),
    				'text' => $fields['post']->getValue(),
//                    'text' => \SpoonFilter::htmlentities($markdown),
    				'profile_id' => $this->profile->getId(),
                    'category_id' => 1,
    				'url' => $this->url,
    				'status' => 'active',
    				'publish_on' => FrontendModel::getUTCDate(),
    				'created_on' => FrontendModel::getUTCDate(),
    				'edited_on' => FrontendModel::getUTCDate(),
    				'hidden' => 'N',
    				'allow_comments' => 'Y',
    				'num_comments' => 0,
				);

				// insert post
				$post['revision_id'] = FrontendForumModel::insertPost($post);

                // invalidate cache
                FrontendForumHelper::invalidateFrontendCache('Forum');

                // redirect
                $this->redirect(FrontendNavigation::getURLForBlock('Forum'));

    			
//        		\Spoon::dump($post);
        	}


        	
        }
    }
}
