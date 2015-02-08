{*
	variables that are available:
	- {$widgetForumRecentComments}: contains an array with the recent comments. Each element contains data about the comment.
*}

{option:widgetForumRecentComments}
	<section id="forumRecentCommentsWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblRecentComments|ucfirst}</h3>
			</header>
			<div class="bd content">
				<ul>
					{iteration:widgetForumRecentComments}
						<li>
							{option:widgetForumRecentComments.website}<a href="{$widgetForumRecentComments.website}" rel="nofollow">{/option:widgetForumRecentComments.website}
								{$widgetForumRecentComments.author}
							{option:widgetForumRecentComments.website}</a>{/option:widgetForumRecentComments.website}
							{$lblCommentedOn} <a href="{$widgetForumRecentComments.full_url}">{$widgetForumRecentComments.post_title}</a>
						</li>
					{/iteration:widgetForumRecentComments}
				</ul>
			</div>
		</div>
	</section>
{/option:widgetForumRecentComments}