<?php

namespace Frontend\Modules\Forum\Ajax;

use Frontend\Core\Engine\Base\AjaxAction as FrontendBaseAJAXAction;
use Frontend\Core\Engine\Language as BL;
use Frontend\Modules\Forum\Engine\Model as FrontendForumModel;

use Frontend\Modules\Forum\Engine\Helper as FrontendForumHelper;


/**
 * This is an AJAX action to parse the user input.
 *
 * @author Glenn Coppens <glenn.coppens@gmail.com>
 */
class ParseMarkdown extends FrontendBaseAJAXAction
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
            $parsedContent = FrontendForumHelper::parseMarkdown($text, $type);

            // output
            $this->output(self::OK, $parsedContent);

        } else {
            $this->output(self::OK, null, BL::err('NoContentProvided'));
        }
    }
}
