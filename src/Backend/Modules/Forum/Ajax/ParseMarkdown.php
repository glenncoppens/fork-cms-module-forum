<?php

namespace Backend\Modules\Forum\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Forum\Engine\Model as BackendForumModel;

use Backend\Modules\Forum\Engine\Helper as BackendForumHelper;


/**
 * This is an AJAX action to parse the user input.
 *
 * @author Glenn Coppens <glenn.coppens@gmail.com>
 */
class ParseMarkdown extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // get parameters
        $text = \SpoonFilter::getPostValue('text', null, null, 'string');
        $type = trim(\SpoonFilter::getPostValue('type', array('default', 'github'), 'default', 'string'));

        // variable which holds the parsed content
        $parsedContent = null;

        // validate parameters
        if ($text !== null) {

            // parse text
            $helper = new BackendForumHelper();
            $parsedContent = $helper->parseMarkdown($text, $type);

            // output
            $this->output(self::OK, $parsedContent);

        } else {
            $this->output(self::OK, null, BL::err('NoContentProvided'));
        }
    }
}
