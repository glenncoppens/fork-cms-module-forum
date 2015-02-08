{*
	variables that are available:
	- {$widgetForumArchive}:
*}

{cache:{$LANGUAGE}_forumWidgetArchiveCache}
	{option:widgetForumArchive}
		<section id="forumArchiveWidget" class="mod">
			<div class="inner">
				<header class="hd">
					<h3>{$lblArchive|ucfirst}</h3>
				</header>
				<div class="bd content">
					<ul>
						{iteration:widgetForumArchive}
							<li>
								{option:widgetForumArchive.url}<a href="{$widgetForumArchive.url}">{/option:widgetForumArchive.url}
									{$widgetForumArchive.label}
									{option:widgetForumArchive.url}({$widgetForumArchive.total}){/option:widgetForumArchive.url}
								{option:widgetForumArchive.url}</a>{/option:widgetForumArchive.url}

								{option:widgetForumArchive.months}
									<ul>
										{iteration:widgetForumArchive.months}
											<li>
												{option:widgetForumArchive.months.url}<a href="{$widgetForumArchive.months.url}">{/option:widgetForumArchive.months.url}
													{$widgetForumArchive.months.label|date:'F':{$LANGUAGE}}
													{option:widgetForumArchive.months.url}({$widgetForumArchive.months.total}){/option:widgetForumArchive.months.url}
												{option:widgetForumArchive.months.url}</a>{/option:widgetForumArchive.months.url}
											</li>
										{/iteration:widgetForumArchive.months}
									</ul>
								{/option:widgetForumArchive.months}
							</li>
						{/iteration:widgetForumArchive}
					</ul>
				</div>
			</div>
		</section>
	{/option:widgetForumArchive}
{/cache:{$LANGUAGE}_forumWidgetArchiveCache}