{*
	variables that are available:
	- {$item}: contains data about the post
	- {$comments}: contains an array with the comments for the post, each element contains data about the comment.
	- {$commentsCount}: contains a variable with the number of comments for this forum post.
	- {$navigation}: contains an array with data for previous and next post
*}
<div id="forumAddPost">
	<section id="postForm" class="mod">
		<div class="inner">
			<header class="hd">
				<h3 id="{$actAddPost}">{$addPost|ucfirst}</h3>
			</header>
			<div class="bd">
				{form:addForm}
					<p class="bigInput{option:txtTitleError} errorArea{/option:txtTitleError}">
						<label for="title">{$lblTitle|ucfirst}</label>
						{$txtTitle} {$txtTitleError}
					</p>
					<p class="bigInput{option:txtTextError} errorArea{/option:txtTextError}">
						<label for="text">{$lblText|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtText} {$txtTextError}
					</p>
					<p>
                        <a href="#" id="preview-button" class="btn">{$msgPreview|ucfirst}</a>&nbsp;
                        <input class="inputSubmit" type="submit" name="add_post" value="{$msgPost|ucfirst}" />
					</p>
				{/form:addForm}

                <section>
                    <h3>Preview</h3>
                    <div id="preview-error"></div>
                    <div id="preview">

                    </div>
                </section>

			</div>
		</div>
	</section>
</div>
