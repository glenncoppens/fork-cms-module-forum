{*
	variables that are available:
	- {$widgetForumRecentPostsList}: contains an array with all posts, each element contains data about the post
*}

{option:widgetForumRecentPostsList}
	<section id="forumRecentPostsListWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblRecentPosts|ucfirst}</h3>
			</header>
			<div class="bd content">
				<ul>
					{iteration:widgetForumRecentPostsList}
						<li><a href="{$widgetForumRecentPostsList.full_url}" title="{$widgetForumRecentPostsList.title}">{$widgetForumRecentPostsList.title}</a></li>
					{/iteration:widgetForumRecentPostsList}
				</ul>
			</div>
			<footer class="ft">
				<p>
					<a href="{$var|geturlforblock:'Forum'}">{$lblForumArchive|ucfirst}</a>
					<a id="RSSfeed" href="{$widgetForumRecentPostsFullRssLink}">{$lblSubscribeToTheRSSFeed|ucfirst}</a>
				</p>
			</footer>
		</div>
	</section>
{/option:widgetForumRecentPostsList}
