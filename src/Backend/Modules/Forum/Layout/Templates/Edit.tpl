{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblForum|ucfirst}: {$msgEditPost|sprintf:{$item.title}}</h2>
	<div class="buttonHolderRight">
		<a href="{$detailURL}/{$item.url}{option:item.revision_id}?revision={$item.revision_id}{/option:item.revision_id}" class="button icon iconZoom previewButton targetBlank">
			<span>{$lblView|ucfirst}</span>
		</a>
	</div>
</div>

{form:edit}
    <p>
        <label for="title">{$lblTitle|ucfirst}</label>
        {$txtTitle} {$txtTitleError}
    </p>

    {*
	<div id="pageUrl">
		<div class="oneLiner">
			{option:detailURL}<p><span><a href="{$detailURL}/{$item.url}">{$detailURL}/<span id="generatedUrl">{$item.url}</span></a></span></p>{/option:detailURL}
			{option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
		</div>
	</div>
	*}

	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
			<li><a href="#tabVersions">{$lblVersions|ucfirst}</a></li>
			<li><a href="#tabPermissions">{$lblComments|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<table width="100%">
				<tr>
					<td id="leftColumn">

						{* Main content *}
						<div class="box">
                            <div class="tabs">
                                <ul>
                                    <li><a href="#tabText">{$lblText|ucfirst}</a></li>
                                    <li><a href="#tabPreview">{$lblPreview|ucfirst}</a></li>
                                </ul>

                                <div id="tabText">
                                    {$txtText} {$txtTextError}
                                </div>
                                <div id="tabPreview">
                                    <div class="preview"></div>
                                    <div class="preview-error"></div>
                                </div>
                            </div>
						</div>

					</td>

					<td id="sidebar">
						<div id="publishOptions" class="box">
                            <div class="heading">
                                <h3>{$lblStatus|ucfirst}</h3>
                            </div>

                            <div class="options">
                                <ul class="inputList">
                                    {iteration:hidden}
                                        <li>
                                            {$hidden.rbtHidden}
                                            <label for="{$hidden.id}">{$hidden.label}</label>
                                        </li>
                                    {/iteration:hidden}
                                </ul>
                            </div>

                            <div class="options">
                                <p class="p0"><label for="publishOnDate">{$lblPublishOn|ucfirst}</label></p>
                                <div class="oneLiner">
                                    <p>
                                        {$txtPublishOnDate} {$txtPublishOnDateError}
                                    </p>
                                    <p>
                                        <label for="publishOnTime">{$lblAt}</label>
                                    </p>
                                    <p>
                                        {$txtPublishOnTime} {$txtPublishOnTimeError}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div id="settings" class="box">
                            <div class="heading">
                                <h3>{$lblSettings|ucfirst}</h3>
                            </div>

                            <div class="options">
                                {$chkAllowComments} <label for="allowComments">{$lblAllowComments|ucfirst}</label>
                            </div>

                        </div>

						<div class="box" id="postMeta">
							<div class="heading">
								<h3>{$lblMetaData|ucfirst}</h3>
							</div>
							<div class="options">
								<label for="categoryId">{$lblCategory|ucfirst}</label>
								{$ddmCategoryId} {$ddmCategoryIdError}
							</div>
							<div class="options">
								<label for="profileId">{$lblAuthor|ucfirst}</label>
                                <ul>
                                    <li><a href="{$var|geturl:'edit':'profiles'}&amp;id={$item.profile.id}">{$item.profile.email}</a></li>
                                </ul>

							</div>
							{option:showTagsIndex}
								<div class="options">
									<label for="tags">{$lblTags|ucfirst}</label>
									{$txtTags} {$txtTagsError}
								</div>
							{/option:showTagsIndex}
						</div>

					</td>
				</tr>
			</table>
		</div>

		<div id="tabPermissions">
			<table width="100%">
				<tr>
					<td>
						{*{$chkAllowComments} <label for="allowComments">{$lblAllowComments|ucfirst}</label>*}
					</td>
				</tr>
			</table>
		</div>

		<div id="tabVersions">
			{option:drafts}
				<div class="tableHeading">
					<div class="oneLiner">
						<h3 class="oneLinerElement">{$lblDrafts|ucfirst}</h3>
						<abbr class="help">(?)</abbr>
						<div class="tooltip" style="display: none;">
							<p>{$msgHelpDrafts}</p>
						</div>
					</div>
				</div>

				<div class="dataGridHolder">
					{$drafts}
				</div>
			{/option:drafts}

			<div class="tableHeading">
				<div class="oneLiner">
					<h3 class="oneLinerElement">{$lblPreviousVersions|ucfirst}</h3>
					<abbr class="help">(?)</abbr>
					<div class="tooltip" style="display: none;">
						<p>{$msgHelpRevisions}</p>
					</div>
				</div>
			</div>

			{option:revisions}
			<div class="dataGridHolder">
				{$revisions}
			</div>
			{/option:revisions}

			{option:!revisions}
				<p>{$msgNoRevisions}</p>
			{/option:!revisions}
		</div>
	</div>

	<div class="fullwidthOptions">
		{option:showForumDelete}
		<a href="{$var|geturl:'delete'}&amp;id={$item.id}{option:categoryId}&amp;category={$categoryId}{/option:categoryId}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>

		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmDelete|sprintf:{$item.title}}
			</p>
		</div>
		{/option:showForumDelete}

		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblPublish|ucfirst}" />
		</div>
	</div>

	<div id="addCategoryDialog" class="forkForms" title="{$lblAddCategory|ucfirst}" style="display: none;">
		<div id="templateList">
			<p>
				<label for="categoryTitle">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				<input type="text" name="categoryTitle" id="categoryTitle" class="inputText" maxlength="255" />
				<span class="formError" id="categoryTitleError" style="display: none;">{$errFieldIsRequired|ucfirst}</span>
			</p>
		</div>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
