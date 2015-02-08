{*
	variables that are available:
	- {$widgetForumCategories}:
*}

{option:widgetForumCategories}
	<section id="forumCategoriesWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblCategories|ucfirst}</h3>
			</header>
			<div class="bd content">
				<ul>
					{iteration:widgetForumCategories}
						<li>
							<a href="{$widgetForumCategories.url}">
								{$widgetForumCategories.label}&nbsp;({$widgetForumCategories.total})
							</a>
						</li>
					{/iteration:widgetForumCategories}
				</ul>
			</div>
		</div>
	</section>
{/option:widgetForumCategories}