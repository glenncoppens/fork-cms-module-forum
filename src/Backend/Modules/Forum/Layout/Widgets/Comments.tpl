<div class="box" id="widgetForumComments">
	<div class="heading">
		<h3><a href="{$var|geturl:'comments':'forum'}">{$lblForum|ucfirst}: {$lblLatestComments|ucfirst}</a></h3>
	</div>

	{option:forumNumCommentsToModerate}
	<div class="moderate">
		<div class="oneLiner">
			<p>{$msgCommentsToModerate|sprintf:{$forumNumCommentsToModerate}}</p>
			<div class="buttonHolder">
				<a href="{$var|geturl:'comments':'forum'}#tabModeration" class="button"><span>{$lblModerate|ucfirst}</span></a>
			</div>
		</div>
	</div>
	{/option:forumNumCommentsToModerate}

	{option:forumComments}
	<div class="dataGridHolder">
		<table class="dataGrid">
			<tbody>
				{iteration:forumComments}
				<tr class="{cycle:'odd':'even'}">
					<td><a href="{$forumComments.full_url}">{$forumComments.title}</a></td>
					<td class="name">{$forumComments.author}</td>
				</tr>
				{/iteration:forumComments}
			</tbody>
		</table>
	</div>
	{/option:forumComments}

	{option:!forumComments}
	<div class="options content">
		<p>{$msgNoPublishedComments}</p>
	</div>
	{/option:!forumComments}

	<div class="footer">
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'comments':'forum'}" class="button"><span>{$lblAllComments|ucfirst}</span></a>
		</div>
	</div>
</div>