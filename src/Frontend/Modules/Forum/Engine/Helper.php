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
use HTMLPurifier_Config as HTMLPurifierConfig;
use HTMLPurifier as HTMLPurifier;

class Helper {

    public function parseMarkdown($text = null, $type = 'default') {

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

        return $this->purifyHtml($parsedContent);
    }

    public function purifyHtml($text) {

        // purify generated html
        $config = HTMLPurifierConfig::createDefault();
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
        $config->set('Cache.DefinitionImpl', null);
        $config->set('Core.EscapeInvalidTags', true);
        $config->set(
            'HTML.Allowed',
            'a[href|title],b,strong,blockquote[cite],code,del,dd,dl,dt,em,h1,h2,h3,h4,i,li,ol,ul,p,pre,s,sup,sub,strong,strike,br,hr,img[src|alt|title]'
        );

        // new purifier
        $purifier = new HTMLPurifier($config);

        return $purifier->purify($text);
    }

}