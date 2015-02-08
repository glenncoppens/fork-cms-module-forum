<?php

namespace Backend\Modules\Forum\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the forum module
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Installer extends ModuleInstaller
{
    /**
     * Default category id
     *
     * @var    int
     */
    private $defaultCategoryId;

    /**
     * Add a category for a language
     *
     * @param string $language The language to use.
     * @param string $title    The title of the category.
     * @param string $url      The URL for the category.
     * @return int
     */
    private function addCategory($language, $title, $url)
    {
        $item = array();
        $item['meta_id'] = $this->insertMeta($title, $title, $title, $url);
        $item['language'] = (string) $language;
        $item['title'] = (string) $title;

        return (int) $this->getDB()->insert('forum_categories', $item);
    }

    /**
     * Fetch the id of the first category in this language we come across
     *
     * @param string $language The language to use.
     * @return int
     */
    private function getCategory($language)
    {
        return (int) $this->getDB()->getVar(
            'SELECT id FROM forum_categories WHERE language = ?',
            array((string) $language)
        );
    }

    /**
     * Insert an empty admin dashboard sequence
     */
    private function insertWidget()
    {
        $comments = array(
            'column' => 'right',
            'position' => 1,
            'hidden' => false,
            'present' => true
        );

        $this->insertDashboardWidget('Forum', 'Comments', $comments);
    }

    /**
     * Install the module
     */
    public function install()
    {
        // load install.sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // add 'forum' as a module
        $this->addModule('Forum');

        // import locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // general settings
        $this->setSetting('Forum', 'allow_comments', true);
        $this->setSetting('Forum', 'requires_akismet', true);
        $this->setSetting('Forum', 'spamfilter', false);
        $this->setSetting('Forum', 'moderation', true);
        $this->setSetting('Forum', 'ping_services', true);
        $this->setSetting('Forum', 'overview_num_items', 10);
        $this->setSetting('Forum', 'recent_posts_full_num_items', 3);
        $this->setSetting('Forum', 'recent_posts_list_num_items', 5);
        $this->setSetting('Forum', 'max_num_revisions', 20);

        $this->makeSearchable('Forum');

        // module rights
        $this->setModuleRights(1, 'Forum');

        // action rights
        $this->setActionRights(1, 'Forum', 'AddCategory');
        $this->setActionRights(1, 'Forum', 'Add');
        $this->setActionRights(1, 'Forum', 'Categories');
        $this->setActionRights(1, 'Forum', 'Comments');
        $this->setActionRights(1, 'Forum', 'DeleteCategory');
        $this->setActionRights(1, 'Forum', 'DeleteSpam');
        $this->setActionRights(1, 'Forum', 'Delete');
        $this->setActionRights(1, 'Forum', 'EditCategory');
        $this->setActionRights(1, 'Forum', 'EditComment');
        $this->setActionRights(1, 'Forum', 'Edit');
        $this->setActionRights(1, 'Forum', 'ImportWordpress');
        $this->setActionRights(1, 'Forum', 'Index');
        $this->setActionRights(1, 'Forum', 'MassCommentAction');
        $this->setActionRights(1, 'Forum', 'Settings');

        // insert dashboard widget
        $this->insertWidget();

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationForumId = $this->setNavigation($navigationModulesId, 'Forum');
        $this->setNavigation(
            $navigationForumId,
            'Posts',
            'forum/index',
            array('forum/add', 'forum/edit', 'forum/import_wordpress')
        );
        $this->setNavigation($navigationForumId, 'Comments', 'forum/comments', array('forum/edit_comment'));
        $this->setNavigation(
            $navigationForumId,
            'Categories',
            'forum/categories',
            array('forum/add_category', 'forum/edit_category')
        );

        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Forum', 'forum/settings');

        // add extra's
        $forumId = $this->insertExtra('Forum', 'block', 'Forum', null, null, 'N', 1000);
        $this->insertExtra('Forum', 'widget', 'RecentComments', 'RecentComments', null, 'N', 1001);
        $this->insertExtra('Forum', 'widget', 'Categories', 'Categories', null, 'N', 1002);
        $this->insertExtra('Forum', 'widget', 'Archive', 'Archive', null, 'N', 1003);
        $this->insertExtra('Forum', 'widget', 'RecentPostsFull', 'RecentPostsFull', null, 'N', 1004);
        $this->insertExtra('Forum', 'widget', 'RecentPostsList', 'RecentPostsList', null, 'N', 1005);

        // get search extra id
        $searchId = (int) $this->getDB()->getVar(
            'SELECT id FROM modules_extras
             WHERE module = ? AND type = ? AND action = ?',
            array('Search', 'widget', 'Form')
        );

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // fetch current categoryId
            $this->defaultCategoryId = $this->getCategory($language);

            // no category exists
            if ($this->defaultCategoryId == 0) {
                // add category
                $this->defaultCategoryId = $this->addCategory($language, 'Default', 'default');
            }

            // RSS settings
            $this->setSetting('Forum', 'rss_meta_' . $language, true);
            $this->setSetting('Forum', 'rss_title_' . $language, 'RSS');
            $this->setSetting('Forum', 'rss_description_' . $language, '');

            // check if a page for forum already exists in this language
            if (!(bool) $this->getDB()->getVar(
                'SELECT 1
                 FROM pages AS p
                 INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                 WHERE b.extra_id = ? AND p.language = ?
                 LIMIT 1',
                array($forumId, $language)
            )
            ) {
                $this->insertPage(
                    array('title' => 'Forum', 'language' => $language),
                    null,
                    array('extra_id' => $forumId, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );
            }

            if ($this->installExample()) {
                $this->installExampleData($language);
            }
        }
    }

    /**
     * Install example data
     *
     * @param string $language The language to use.
     */
    private function installExampleData($language)
    {
        // get db instance
        $db = $this->getDB();

        // check if forumposts already exist in this language
        if (!(bool) $db->getVar(
            'SELECT 1
             FROM forum_posts
             WHERE language = ?
             LIMIT 1',
            array($language)
        )
        ) {
            // insert sample forumpost 1
            $db->insert(
                'forum_posts',
                array(
                    'id' => 1,
                    'category_id' => $this->defaultCategoryId,
                    'user_id' => $this->getDefaultUserID(),
                    'meta_id' => $this->insertMeta(
                        'Nunc sediam est',
                        'Nunc sediam est',
                        'Nunc sediam est',
                        'nunc-sediam-est'
                    ),
                    'language' => $language,
                    'title' => 'Nunc sediam est',
                    'introduction' => file_get_contents(
                        PATH_WWW . '/src/Backend/Modules/Forum/Installer/Data/' . $language . '/sample1.txt'
                    ),
                    'text' => file_get_contents(
                        PATH_WWW . '/src/Backend/Modules/Forum/Installer/Data/' . $language . '/sample1.txt'
                    ),
                    'status' => 'active',
                    'publish_on' => gmdate('Y-m-d H:i:00'),
                    'created_on' => gmdate('Y-m-d H:i:00'),
                    'edited_on' => gmdate('Y-m-d H:i:00'),
                    'hidden' => 'N',
                    'allow_comments' => 'Y',
                    'num_comments' => '2'
                )
            );

            // insert sample forumpost 2
            $db->insert(
                'forum_posts',
                array(
                    'id' => 2,
                    'category_id' => $this->defaultCategoryId,
                    'user_id' => $this->getDefaultUserID(),
                    'meta_id' => $this->insertMeta('Lorem ipsum', 'Lorem ipsum', 'Lorem ipsum', 'lorem-ipsum'),
                    'language' => $language,
                    'title' => 'Lorem ipsum',
                    'introduction' => file_get_contents(
                        PATH_WWW . '/src/Backend/Modules/Forum/Installer/Data/' . $language . '/sample1.txt'
                    ),
                    'text' => file_get_contents(
                        PATH_WWW . '/src/Backend/Modules/Forum/Installer/Data/' . $language . '/sample1.txt'
                    ),
                    'status' => 'active',
                    'publish_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
                    'created_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
                    'edited_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
                    'hidden' => 'N',
                    'allow_comments' => 'Y',
                    'num_comments' => '0'
                )
            );

            // insert example comment 1
            $db->insert(
                'forum_comments',
                array(
                    'post_id' => 1,
                    'language' => $language,
                    'created_on' => gmdate('Y-m-d H:i:00'),
                    'author' => 'Davy Hellemans',
                    'email' => 'forkcms-sample@spoon-library.com',
                    'website' => 'http://www.spoon-library.com',
                    'text' => 'awesome!',
                    'type' => 'comment',
                    'status' => 'published',
                    'data' => null
                )
            );

            // insert example comment 2
            $db->insert(
                'forum_comments',
                array(
                    'post_id' => 1,
                    'language' => $language,
                    'created_on' => gmdate('Y-m-d H:i:00'),
                    'author' => 'Tijs Verkoyen',
                    'email' => 'forkcms-sample@sumocoders.be',
                    'website' => 'http://www.sumocoders.be',
                    'text' => 'wicked!',
                    'type' => 'comment',
                    'status' => 'published',
                    'data' => null
                )
            );
        }
    }
}
