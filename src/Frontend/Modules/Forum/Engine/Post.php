<?php

namespace Frontend\Modules\Forum\Engine;

use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Forum\Engine\Model as FrontendForumModel;
use Frontend\Modules\Forum\Engine\Helper as FrontendForumHelper;
use Frontend\Modules\Profiles\Engine\Profile as FrontendProfilesProfile;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Frontend\Modules\Forum\Engine\Comment as FrontendForumComment;

/**
 *
 *
 * @author Glenn Coppens <glenn.coppens@gmail.com>
 */
class FrontendForumPost
{
    /**
     * Properties.
     *
     * @var
     */
    private $profileId;
    private $profile;

    private $id;
    private $revisionId;
    private $status;
    private $url;
    private $title;
    private $text;
    private $comments;
    private $hidden;
    private $categoryId;
    private $type;
    private $allowComments;

    private $createdOn;
    private $editedOn;
    private $publishOn;

    /**
     * Constructor.
     *
     * @param int $postId
     * @param int $revision
     */
    public function __construct($postId = null, $revision = null)
    {
        if($postId !== null) $this->loadPost((int) $postId, (int)$revision);
    }

    /**
     * Load the data of a post
     *
     * @param int $id Profile id to load.
     * @param $revisionId
     */
    private function loadPost($id, $revisionId)
    {
        // get record
        $post = FrontendForumModel::getById($id);
        $url = isset($record['url']) ? $post['url'] : null;

        if ($revisionId != 0 && $url !== null) {
            // get data
            $post = FrontendForumModel::getRevision($url, $revisionId);
        }

        // set properties
        $this->loadByData($post);
    }

    /**
     * Array to object
     *
     * @param $post
     */
    public function loadByData($post)
    {
        $this->setProfileId($post['profile_id']);
//        $this->setProfile($post['profile_id']);

        $this->setId($post['id']);
        $this->setRevisionId($post['revision_id']);
        $this->setStatus($post['status']);
        $this->setUrl($post['url']);
        $this->setTitle($post['title']);
        $this->setText($post['text']);
//        $this->setComments($post['comments']);
        $this->setHidden($post['hidden']);
        $this->setCategoryId($post['category_id']);
        $this->setType($post['type']);
        $this->setAllowComments($post['allow_comments']);

        $this->setCreatedOn($post['created_on']);
        $this->setEditedOn($post['edited_on']);
        $this->setPublishOn($post['publish_on']);
    }

    /**
     * Load the data of a topic by URL
     *
     * @param string $url
     * @param string $revision
     * @throws \Exception
     */
    public function loadTopicByUrl($url, $revision = null)
    {
        if ($url == '') throw new \Exception('You should provide a url to fetch the corresponding post.');
        $revision = (int)$revision;
        $post = array();

        // load revision
        if ($revision != 0) {
            // get data
            $post = FrontendForumModel::getRevision($url, $revision);
        } else {

            // get by URL
            $post = FrontendForumModel::get($url);
        }

        // set properties
        $this->loadByData($post);
    }

    /**
     * Get all the comments on this post
     *
     */
    public function getComments()
    {
        // clear
        $this->comments = array();

        // id of the post is set
        if($this->getId() != null)
        {
            // get comments as array
            $comments = FrontendForumModel::getComments($this->getId());

            // create objects from array
            foreach($comments as $c)
            {
                $comment = new FrontendForumComment();
                $comment->loadByData($c);
                $this->comments[] = $comment;
            }
        }

        return $this->comments;
    }

    /**
     * Get profile object.
     *
     */
    public function getProfile()
    {
        if($this->profile == null) $this->profile = new FrontendProfilesProfile($this->getProfileId());

        return $this->profile;
    }

    /**
     * Is the current logged in profile the author of the post?
     *
     * @return bool
     */
    public function isAuthor()
    {
        if(FrontendProfilesAuthentication::isLoggedIn() && $this->getProfile()->getId() == FrontendProfilesAuthentication::getProfile()->getId()) {

            return true;
        }

        return false;
    }

    /**
     * Convert the object into an array for usage in the template
     *
     * @return array
     */
    public function toArray()
    {
        $return['title'] = $this->getTitle();
        $return['text'] = $this->getText();
        $return['id'] = $this->getId();
        $return['revision_id'] = $this->getRevisionId();
        $return['profile_id'] = $this->getProfileId();
        $return['type'] = $this->getType();
        $return['status'] = $this->getStatus();
        $return['url'] = $this->getUrl();
        $return['is_author'] = $this->isAuthor();
        $return['created_on'] = $this->getCreatedOn();
        $return['edited_on'] = $this->getEditedOn();
        $return['publish_on'] = $this->getPublishOn();

        $return['profile']['display_name'] = $this->getProfile()->getDisplayName();
        $return['profile']['email'] = $this->getProfile()->getEmail();
        $return['profile']['url'] = $this->getProfile()->getUrl();
        $return['profile']['avatar'] = $this->getProfile()->getSetting('avatar');

        foreach($this->getComments() as $comment)
        {
            $return['comments'][] = $comment->toArray();
        }

        return $return;
    }

    /**
     * Get html to display.
     *
     * @param bool $raw
     * @return string
     */
    public function getText($raw = false)
    {
        if($raw !== false) {
            // use helper object to parse text
            $helper = new FrontendForumHelper();

            return $helper->parseMarkdown($this->getText(true));
        } else {

            return $this->getText(true);
        }
    }

    /**
     * Getters and setters without extra logic
     *
     */
    public function getTitle() { return $this->title; }
    public function getProfileId() { return $this->profileId; }
    public function getId() { return $this->id; }
    public function getRevisionId() { return $this->revisionId; }
    public function getType() { return $this->type; }
    public function getStatus() { return $this->status; }
    public function getHidden() { return $this->hidden; }
    public function getCategoryId() { return $this->categoryId; }
    public function getAllowComments() { return $this->allowComments; }
    public function getUrl() { return $this->url; }
    public function getCreatedOn() { return $this->createdOn; }
    public function getEditedOn() { return $this->editedOn; }
    public function getPublishOn() { return $this->publishOn; }

    public function setTitle($value) { $this->title = $value; }
    public function setText($value) { $this->text = $value; }
    public function setProfileId($value) { $this->profileId = $value; }
    public function setId($value) { $this->id = $value; }
    public function setRevisionId($value) { $this->revisionId = $value; }
    public function setType($value) { $this->type = $value; }
    public function setStatus($value) { $this->status = $value; }
    public function setHidden($value) { $this->hidden = $value; }
    public function setCategoryId($value) { $this->categoryId = $value; }
    public function setAllowComments($value) { $this->allowComments = $value; }
    public function setUrl($value) { $this->url = $value; }
    public function setCreatedOn($value) { $this->createdOn = $value; }
    public function setEditedOn($value) { $this->editedOn = $value; }
    public function setPublishOn($value) { $this->publishOn = $value; }
}