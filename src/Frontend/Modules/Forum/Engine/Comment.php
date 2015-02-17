<?php

namespace Frontend\Modules\Forum\Engine;

use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Forum\Engine\Model as FrontendForumModel;
use Frontend\Modules\Forum\Engine\Helper as FrontendForumHelper;
use Frontend\Modules\Profiles\Engine\Profile as FrontendProfilesProfile;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

/**
 *
 *
 * @author Glenn Coppens <glenn.coppens@gmail.com>
 */
class Comment
{
    /**
     * Properties
     */
    private $id;
    private $status;
    private $text;
    private $hidden;
    private $type;
    private $data;

    private $profileId;
    private $profile;
    private $author;
    private $email;
    private $website;

    private $postId;

    private $createdOn;
    private $editedOn;
    private $publishOn;

    /**
     * Constructor.
     *
     * @param int[optional] $topicId The topic id to load data from.
     */
    public function __construct($commentId = null)
    {
        if($commentId !== null) $this->loadComment((int) $commentId);
    }

    /**
     * Load the data of a comment
     *
     * @param int $id Comment id to load.
     */
    private function loadComment($id)
    {
        // get record
        $comment = FrontendForumModel::getComments($id);

        // set properties
        $this->loadByData($comment);
    }

    /**
     * Set the properties of this object by a provided array.
     *
     * @param array $data
     */
    public function loadByData($comment)
    {
        $this->setProfileId($comment['profile_id']);
        $this->setId($comment['id']);
        $this->setStatus($comment['status']);
        $this->setText($comment['text']);
        $this->setHidden($comment['hidden']);
        $this->setType($comment['type']);
        $this->setCreatedOn($comment['created_on']);
        $this->setEditedOn($comment['edited_on']);
        $this->setPublishOn($comment['publish_on']);
    }

    /**
     * Get the text. Either in html or as a raw markdown string.
     *
     * @param bool $raw
     * @return string
     */
    public function getText($raw = false) {

        if($raw !== false) {
            // use helper object to parse text
            $helper = new FrontendForumHelper();

            return $helper->parseMarkdown($this->getText(true));
        } else {

            return $this->getText(true);
        }
    }

    /**
     * Gets the profile of this comment.
     *
     * @return FrontendProfilesProfile
     */
    public function getProfile() {

        // set as property
        if($this->profile === null) $this->profile = new FrontendProfilesProfile($this->getProfileId());

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
     * Get data (un)serialized.
     *
     * @return array
     */
    public function getData($serialized = false) {

        // return as serialized
        if($serialized === false) {
            return unserialize($this->data);

        } else {
            return $this->data;
        }
    }

    /**
     * Convert the object into an array for usage in the template
     *
     * @return array
     */
    public function toArray() {

        $return['id'] = $this->getId();
        $return['text'] = $this->getText();
        $return['post_id'] = $this->getProfileId();

        $return['type'] = $this->getType();
        $return['status'] = $this->getStatus();
        $return['is_author'] = $this->isAuthor();
        $return['data'] = $this->getData(false);
        $return['created_on'] = $this->getCreatedOn();
        $return['edited_on'] = $this->getEditedOn();
        $return['publish_on'] = $this->getPublishOn();

        $return['profile']['profile_id'] = $this->getProfileId();
        $return['profile']['display_name'] = $this->getProfile()->getDisplayName();
        $return['profile']['email'] = $this->getProfile()->getEmail();
        $return['profile']['url'] = $this->getProfile()->getUrl();
        $return['profile']['avatar'] = $this->getProfile()->getSetting('avatar');

        return $return;
    }

    /**
     * Setters.
     *
     * @param $value
     */
    public function setId($value) { $this->id = $value; }
    public function setPostId($value) { $this->postId = $value; }
    public function setAuthor($value) { $this->author = $value; }
    public function setData($value) { $this->data = $value; }
    public function setEmail($value) { $this->email = $value; }
    public function setHidden($value) { $this->hidden = $value; }
    public function setProfileId($value) { $this->profileId = $value; }
    public function setStatus($value) { $this->status = $value; }
    public function setText($value) { $this->text = $value; }
    public function setType($value) { $this->type = $value; }
    public function setWebsite($value) { $this->website = $value; }
    public function setCreatedOn($value) { $this->createdOn = $value; }
    public function setEditedOn($value) { $this->editedOn = $value; }
    public function setPublishOn($value) { $this->publishOn = $value; }

    /**
     * Getters.
     *
     * @return mixed
     */
    public function getId() { return $this->id; }
    public function getPostId() { return $this->postId; }
    public function getAuthor() { return $this->author; }
    public function getEmail() { return $this->email; }
    public function getHidden() { return $this->hidden; }
    public function getProfileId() { return $this->profileId; }
    public function getStatus() { return $this->status; }
    public function getType() { return $this->type; }
    public function getWebsite() { return $this->website; }
    public function getCreatedOn() { return $this->createdOn; }
    public function getEditedOn() { return $this->editedOn; }
    public function getPublishOn() { return $this->publishOn; }
} 