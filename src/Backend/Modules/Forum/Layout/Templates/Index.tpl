{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>
		{$lblForum|ucfirst}:

		{option:!filterCategory}{$lblPosts}{/option:!filterCategory}
		{option:filterCategory}{$msgPostsFor|sprintf:{$filterCategory.title}}{/option:filterCategory}
	</h2>
</div>

{form:filter}
	<p class="oneLiner">
		<label for="category">{$msgShowOnlyItemsInCategory}</label>
		&nbsp;{$ddmCategory} {$ddmCategoryError}
	</p>
{/form:filter}

{option:dgRecent}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblRecentlyEdited|ucfirst}</h3>
		</div>
		{$dgRecent}
	</div>
{/option:dgRecent}

{option:dgDrafts}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblDrafts|ucfirst}</h3>
		</div>
		{$dgDrafts}
	</div>
{/option:dgDrafts}

{option:dgPosts}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblPublishedPosts|ucfirst}</h3>
		</div>
		{$dgPosts}
	</div>
{/option:dgPosts}

{option:!dgPosts}
	<p>{$msgNoItems}</p>
{/option:!dgPosts}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
