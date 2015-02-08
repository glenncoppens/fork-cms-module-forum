{*
	variables that are available:
	- {$widgetForumRecentPostsFull}: contains an array with all posts, each element contains data about the post
*}

{option:widgetForumRecentPostsFull}
	<section id="forumRecentPostsFullWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblRecentPosts|ucfirst}</h3>
			</header>
			<div class="bd">
				{iteration:widgetForumRecentPostsFull}
					<post class="mod post">
						<div class="inner">
							<header class="hd">
								<h4><a href="{$widgetForumRecentPostsFull.full_url}" title="{$widgetForumRecentPostsFull.title}">{$widgetForumRecentPostsFull.title}</a></h4>
								<ul>
									<li>{$msgWrittenBy|ucfirst|sprintf:{$widgetForumRecentPostsFull.user_id|usersetting:'nickname'}} {$lblOn} {$widgetForumRecentPostsFull.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}</li>
									{option:widgetForumRecentPostsFull.allow_comments}
										<li>
											{option:!widgetForumRecentPostsFull.comments}<a href="{$widgetForumRecentPostsFull.full_url}#{$actComment}">{$msgForumNoComments|ucfirst}</a>{/option:!widgetForumRecentPostsFull.comments}
											{option:widgetForumRecentPostsFull.comments}
												{option:widgetForumRecentPostsFull.comments_multiple}<a href="{$widgetForumRecentPostsFull.full_url}#{$actComments}">{$msgForumNumberOfComments|sprintf:{$widgetForumRecentPostsFull.comments_count}}</a>{/option:widgetForumRecentPostsFull.comments_multiple}
												{option:!widgetForumRecentPostsFull.comments_multiple}<a href="{$widgetForumRecentPostsFull.full_url}#{$actComments}">{$msgForumOneComment}</a>{/option:!widgetForumRecentPostsFull.comments_multiple}
											{/option:widgetForumRecentPostsFull.comments}
										</li>
									{/option:widgetForumRecentPostsFull.allow_comments}
									<li><a href="{$widgetForumRecentPostsFull.category_full_url}" title="{$widgetForumRecentPostsFull.category_title}">{$widgetForumRecentPostsFull.category_title}</a></li>
								</ul>
							</header>
							<div class="bd content">
								{option:!widgetForumRecentPostsFull.introduction}{$widgetForumRecentPostsFull.text}{/option:!widgetForumRecentPostsFull.introduction}
								{option:widgetForumRecentPostsFull.introduction}{$widgetForumRecentPostsFull.introduction}{/option:widgetForumRecentPostsFull.introduction}
							</div>
						</div>
					</post>
				{/iteration:widgetForumRecentPostsFull}
			</div>
			<footer class="ft">
				<p>
					<a href="{$var|geturlforblock:'Forum'}">{$lblForumArchive|ucfirst}</a>
					<a id="RSSfeed" href="{$widgetForumRecentPostsFullRssLink}">{$lblSubscribeToTheRSSFeed|ucfirst}</a>
				</p>
			</footer>
		</div>
	</section>
{/option:widgetForumRecentPostsFull}
