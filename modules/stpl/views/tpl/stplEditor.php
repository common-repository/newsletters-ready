<div class="subStplCanvasShell">
	<div class="subStplCanvasOptions">
		<div style="float: right;">
			<label style="">
				<?php echo htmlSub::checkbox('show_grid', array('attrs' => 'onchange="stplCanvasSwitchGridButtClick(this);"', 'checked' => 1))?>
				<?php _e('Show Grid')?>
			</label>
			<label style="">
				<?php echo htmlSub::checkbox('show_images', array('attrs' => 'onchange="stplCanvasSwitchImagesButtClick(this);"', 'checked' => 1))?>
				<?php _e('Show Images')?>
			</label>
		</div>
		<?php echo htmlSub::button(array('value' => __('Add Row'), 'attrs' => 'onclick="stplCanvasAddRowSub(this); return false;" class="button button-primary" style="margin-left: 10px;"'))?>
	</div>
	<div class="subStplCanvas"></div>
	<div class="subStplCanvasPreviewShell">
		<a href="#" onclick="stplCanvasPreviewInBrowserLinkClick(this); return false;"><?php _e('Preview in Browser')?></a>
	</div>
	<div class="subStplCanvasRowSettings subExample">
		<div class="subStplCanvasRowIconMove subStplCanvasRowSetting" title="<?php _e('Move')?>"></div>
		<?php ?><?php echo htmlSub::text('background_color', array('attrs' => 'class="subStplCanvasRowIconBgColor"'))?><?php ?>
		<?php /*?><div class="subStplCanvasRowIconBgColor subStplCanvasRowSetting" title="<?php _e('Background Color')?>"></div><?php */?>
		<div class="subStplCanvasRowIconColumns subStplCanvasRowSetting" title="<?php _e('Columns')?>"></div>
		<div class="subStplCanvasRowIconRemove subStplCanvasRowSetting" title="<?php _e('Remove')?>"></div>
	</div>
	<div class="subStplCanvasRowColumnsNumShell subExample">
		<div class="subStplCanvasRowColumnsNumItem">
			<?php echo htmlSub::text('columns_num', array('value' => 1/*By default*/, 'attrs' => 'class="subStplCanvasRowColumnsNumText"'))?>
		</div>
		<div class="subStplCanvasRowColumnsNumItem">
			<?php echo htmlSub::button(array('value' => __('Ok'), 'attrs' => 'class="subStplCanvasRowColumnsNumButt button"'))?>
		</div>
	</div>
	<div class="subStplCanvasCellSettings subExample">
		<div class="subStplCanvasCellIconRemove subStplCanvasCellSetting" title="<?php _e('Remove')?>"></div>
		<div class="subStplCanvasCellIconEdit subStplCanvasCellSetting" title="<?php _e('Edit')?>"></div>
		<div class="subStplCanvasCellIconMove subStplCanvasCellSetting" title="<?php _e('Move')?>"></div>
	</div>
</div>
<?php /*This block will be hidden for now*/?>
<div class="subStplCanvasSettings" id="subStplCanvasSettings">
	<ul>
		<li><span class="left-corner"></span><a href="#subStplCanvasSettingsContentTab"><?php _e('Content')?></a><span class="right-corner"></span></li>
		<li><span class="left-corner"></span><a href="#subStplCanvasSettingsStylesTab"><?php _e('Styles')?></a><span class="right-corner"></li>
	</ul>
	<div id="subStplCanvasSettingsContentTab">
		<div class="subStplCanvasContentElementOriginal" data-element="stplCanvasElementText">
			<img src="<?php echo $this->getModule()->getModPath()?>img/element_icons/text.png" />
			<div class="subStplCanvasContentElementOriginalLabel"><?php _e('Text and Titles')?></div>
		</div>
		<div class="subStplCanvasContentElementOriginal" data-element="stplCanvasElementImage">
			<img src="<?php echo $this->getModule()->getModPath()?>img/element_icons/images.png" />
			<div class="subStplCanvasContentElementOriginalLabel"><?php _e('Images')?></div>
		</div>
		<div style="clear: both;"></div>
		<div class="subStplCanvasContentElementOriginal" data-element="stplCanvasElementSocial">
			<img src="<?php echo $this->getModule()->getModPath()?>img/element_icons/soc_icons.png" />
			<div class="subStplCanvasContentElementOriginalLabel"><?php _e('Social Icons and Bookmarks')?></div>
		</div>
		<div class="subStplCanvasContentElementOriginal" data-element="stplCanvasElementDivider">
			<img src="<?php echo $this->getModule()->getModPath()?>img/element_icons/dividers.png" />
			<div class="subStplCanvasContentElementOriginalLabel"><?php _e('Dividers')?></div>
		</div>
		<div style="clear: both;"></div>
		<div class="subStplCanvasContentElementOriginal" data-element="stplCanvasElementNewContent">
			<img src="<?php echo $this->getModule()->getModPath()?>img/element_icons/dynamic.png" />
			<div class="subStplCanvasContentElementOriginalLabel"><?php _e('Dynamic Wordpress Content')?></div>
		</div>
		<div class="subStplCanvasContentElementOriginal" data-element="stplCanvasElementStaticContent">
			<img src="<?php echo $this->getModule()->getModPath()?>img/element_icons/static.png" />
			<div class="subStplCanvasContentElementOriginalLabel"><?php _e('Static Wordpress Content')?></div>
		</div>
		<div style="clear: both"></div>
	</div>
	<div id="subStplCanvasSettingsStylesTab">
		<fieldset class="subStplCanvasSettingFieldSet">
			<legend><?php _e('Fonts')?></legend>
			<div class="subStplCanvasSettingStylesShell">
				<table width="100%">
				<?php foreach($this->styleElements as $elKey => $elData) { ?>
					<tr class="subStplCanvasSettingFontStyleRow">
						<td>
							<?php echo htmlSub::hidden('font_style['. $elKey. '][selector]', array('value' => $elData['selector']));?>
							<?php echo $elData['label']?>:
						</td>
						<td><?php echo htmlSub::selectbox('font_style['. $elKey. '][font-family]', array('options' => $this->fonts, 'value' => $elData['defaults']['font-family'], 'attrs' => 'onchange="stplCanvasOnFontStyleChange(this);"'))?></td>
						<td><?php echo htmlSub::selectbox('font_style['. $elKey. '][font-size]', array('options' => $this->fontSizes, 'value' => $elData['defaults']['font-size'], 'attrs' => 'onchange="stplCanvasOnFontStyleChange(this);"'))?></td>
						<td><?php echo htmlSub::colorpicker('font_style['. $elKey. '][color]', array('value' => $elData['defaults']['color'], 'change' => 'stplCanvasOnFontStyleChange'))?></td>
					</tr>
				<?php }?>
				</table>
			</div>
		</fieldset>
		<fieldset class="subStplCanvasSettingFieldSet">
			<legend><?php _e('Background')?></legend>
			<div class="subStplCanvasSettingBgTypeShell">
				<div class="subStplCanvasSettingBgTypeRadio">
					<label for="subStplCanvasSettingBgTypeNone"><?php _e('None')?></label><?php echo htmlSub::radiobutton('background_type', array('value' => 'none', 'attrs' => 'id="subStplCanvasSettingBgTypeNone"'))?>
					<label for="subStplCanvasSettingBgTypeColor"><?php _e('Color')?></label><?php echo htmlSub::radiobutton('background_type', array('value' => 'color', 'attrs' => 'id="subStplCanvasSettingBgTypeColor"'))?>
					<label for="subStplCanvasSettingBgTypeImage"><?php _e('Image')?></label><?php echo htmlSub::radiobutton('background_type', array('value' => 'image', 'attrs' => 'id="subStplCanvasSettingBgTypeImage"'))?>
				</div>
				<div id="subStplCanvasSettingBgTypeColorContainer" class="subStplCanvasSettingBgTypeContainer">
					<?php echo htmlSub::colorpicker('background_color', array('change' => 'stplCanvasSetBgColorChange'))?>
				</div>
				<div id="subStplCanvasSettingBgTypeImageContainer" class="subStplCanvasSettingBgTypeContainer">
					<div class="subStplCanvasSettingBgImgPosRadio">
						<label for="subStplCanvasSettingBgImgStretch"><?php _e('Stretch')?></label><?php echo htmlSub::radiobutton('background_img_pos', array('value' => 'stretch', 'attrs' => 'id="subStplCanvasSettingBgImgStretch"'))?>
						<label for="subStplCanvasSettingBgImgTile"><?php _e('Tile')?></label><?php echo htmlSub::radiobutton('background_img_pos', array('value' => 'tile', 'attrs' => 'id="subStplCanvasSettingBgImgTile"'))?>
						<label for="subStplCanvasSettingBgImgCenter"><?php _e('Center')?></label><?php echo htmlSub::radiobutton('background_img_pos', array('value' => 'center', 'attrs' => 'id="subStplCanvasSettingBgImgCenter"'))?>
					</div>
					<div class="subStplCanvasSettingImageUploaderContainer">
						<?php echo htmlSub::hidden('background_image', array('attrs' => 'class="subStplCanvasSettingImageUploaderValue"'))?>
						<?php echo htmlSub::button(array('value' => __('Select Image'), 'attrs' => 'class="button"'))?>
						<?php echo htmlSub::img('', 0, array('attrs' => 'class="subStplCanvasSettingImageUploaderExample" style="max-width: 190px; display: none;"'))?>
					</div>
				</div>
			</div>
		</fieldset>
	</div>
</div>
<div class="subStplCanvasNewContentShell" style="display: none;">
	<table width="100%">
		<tr>
			<td><?php _e('Title Style')?>:</td>
			<td><?php echo htmlSub::selectbox('title_style', array('options' => $this->titleStyles));?></td>
		</tr>
		<tr>
			<td><?php _e('Title Align')?>:</td>
			<td><?php echo htmlSub::selectbox('title_align', array('options' => $this->aligns))?></td>
		</tr>
		<tr>
			<td>
				<?php _e('Content CSS additional styles')?>:<br />
				<i><?php _e('For advanced users')?></i>
			</td>
			<td><?php echo htmlSub::text('content_styles')?></td>
		</tr>
		<tr>
			<td><?php _e('Image Align')?></td>
			<td><?php echo htmlSub::selectbox('image_align', array('options' => array_merge($this->aligns, array('no_image' => __('No Image')))))?></td>
		</tr>
		<tr>
			<td><?php _e('Show Content')?>:</td>
			<td><?php echo htmlSub::selectbox('show_content', array('options' => $this->showContent))?></td>
		</tr>
		<tr>
			<td><?php _e('"Read More" text')?>:</td>
			<td><?php echo htmlSub::text('read_more_text', array('value' => __('Read More...')))?></td>
		</tr>
		<tr>
			<td>
				<?php _e('"Read More" CSS additional styles')?>:<br />
				<i><?php _e('For advanced users')?></i>
			</td>
			<td><?php echo htmlSub::text('read_more_styles')?></td>
		</tr>
		<tr>
			<td><?php _e('Posts Number')?>:</td>
			<td><?php echo htmlSub::selectbox('posts_num', array('options' => $this->postsNum))?></td>
		</tr>
		<tr>
			<td><?php _e('From category')?>:</td>
			<td><?php echo htmlSub::selectbox('category')?></td>
		</tr>
	</table>
</div>
<div class="subStplCanvasDividersShell" style="display: none;">
	<?php for($i = 1; $i <= 27; $i++) { ?>
	<div class="subStplCanvasDividerOriginal">
		<img class="subStplCanvasDividerImg" style="width: 100%; height: 100%;" src="<?php echo $this->getModule()->getModPath()?>img/dividers/<?php echo $i?>.png" />
	</div>
	<?php }?>
</div>
<div class="subStplCanvasSocialShell" style="display: none;">
	<div class="subStplCanvasSocialDesignsShell">
		<div class="subStplCanvasSocialDesignButtShell">
		<?php for($i = 1; $i <= 2; $i++) { ?>
			<label for="subStplCanvasSocialDesignButt<?php echo $i?>"><?php echo sprintf(__('Design %s'), $i)?></label>
			<?php echo htmlSub::radiobutton('social_design', array('value' => $i, 'attrs' => 'id="subStplCanvasSocialDesignButt'. $i. '"'))?>
		<?php }?>
		</div>
		<div class="subStplCanvasSocialDesignPresentation"></div>
	</div>
	<div style="clear: both;"></div>
	<div class="subStplCanvasSocialLinksShell">
		<table width="100%">
			<tr>
				<td><?php _e('Facebook')?>:</td><td><?php echo htmlSub::text('link_facebook', array('attrs' => 'placeholder="https://www.facebook.com/ReadyECommerce"'))?></td>
			</tr>
			<tr>
				<td><?php _e('Twitter')?>:</td><td><?php echo htmlSub::text('link_twitter', array('attrs' => 'placeholder="https://twitter.com/ReadyEcommerceW"'))?></td>
			</tr>
			<tr>
				<td><?php _e('Google+')?>:</td><td><?php echo htmlSub::text('link_google_plus', array('attrs' => 'placeholder="https://plus.google.com/u/0/105222308619741800340/about'))?></td>
			</tr>
			<tr>
				<td><?php _e('Youtube')?>:</td><td><?php echo htmlSub::text('link_youtube', array('attrs' => 'placeholder="https://www.youtube.com/channel/UCHfmzraXLZdZVJmCe-59pww"'))?></td>
			</tr>
		</table>
	</div>
	<div style="clear: both;"></div>
</div>
<div class="subStplCanvasStaticContentShell" style="display: none;">
	<table width="100%">
		<tr><td colspan="2"><?php _e('Select Any')?></td></tr>
		<tr>
			<td><?php _e('Post')?>:</td><td><?php echo htmlSub::selectbox('static_content_post')?></td>
		</tr>
		<tr><td colspan="2"><?php _e('Or')?></td></tr>
		<tr>
			<td><?php _e('Page')?>:</td><td><?php echo htmlSub::selectbox('static_content_page')?></td>
		</tr>
	</table>
	<table width="100%" style="border-top: 1px solid #D8D8D8;">
		<tr>
			<td><?php _e('Title Style')?>:</td>
			<td><?php echo htmlSub::selectbox('static_title_style', array('options' => $this->titleStyles));?></td>
		</tr>
		<tr>
			<td><?php _e('Title Align')?>:</td>
			<td><?php echo htmlSub::selectbox('static_title_align', array('options' => $this->aligns))?></td>
		</tr>
		<tr>
			<td>
				<?php _e('Content CSS additional styles')?>:<br />
				<i><?php _e('For advanced users')?></i>
			</td>
			<td><?php echo htmlSub::text('content_styles')?></td>
		</tr>
		<tr>
			<td><?php _e('Show Content')?>:</td>
			<td><?php echo htmlSub::selectbox('static_show_content', array('options' => $this->showContent))?></td>
		</tr>
		<tr>
			<td><?php _e('Image Align')?></td>
			<td><?php echo htmlSub::selectbox('image_align', array('options' => array_merge($this->aligns, array('no_image' => __('No Image')))))?></td>
		</tr>
		<tr>
			<td><?php _e('"Read More" text')?>:</td>
			<td><?php echo htmlSub::text('read_more_text', array('value' => __('Read More...')))?></td>
		</tr>
		<tr>
			<td>
				<?php _e('"Read More" CSS additional styles')?>:<br />
				<i><?php _e('For advanced users')?></i>
			</td>
			<td><?php echo htmlSub::text('read_more_styles')?></td>
		</tr>
	</table>
</div>
