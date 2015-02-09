<?php

namespace Frontend\Modules\Forum\Engine;

/**
 * 
 *
 * @author Glenn Coppens <glenn.coppens@gmail.com>
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

use Frontend\Core\Engine\Theme as FrontendTheme;
use Symfony\Component\Routing\Exception\InvalidParameterException;

use Github\Client as GithubClient;
use Github\Api\Markdown as GithubMarkdown;
use Parsedown as Parsedown;

class Helper {

    public static function parseMarkdown($text = null, $type = 'default') {

        // variables
        $parsedContent = null;

        // no text given
        if($text === null) throw new InvalidParameterException('You should provide some markdown text.');

        // default parse option
        if($type === 'default') {

            // create markdown object
            $parsedown = new Parsedown();

            // set parsedown options
            $parsedown->setBreaksEnabled(true);
            $parsedown->setMarkupEscaped(true);

            // parse content
            $parsedContent = $parsedown->text($text);

        } elseif($type === 'github') {

            // create github client
            $client = new GithubClient();

            // init api
            $api = new GithubMarkdown($client);

            // parse content ('markdown' = straight markdown, 'gfm' = github flavoured)
            $parsedContent = $api->render($text, 'gfm', null);
        }

        return $parsedContent;
    }

}