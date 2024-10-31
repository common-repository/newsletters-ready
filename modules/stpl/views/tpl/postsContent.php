<?php
	$titleTagOpen = '<'. $this->params['title_style']. ' style="text-align: '. $this->params['title_align']. '">';
	$titleTagClose = '</'. $this->params['title_style']. '>';
?>
<div style="clear: both;"></div>
<?php foreach($this->posts as $post) { ?>
	<div style="<?php echo $this->params['content_styles']?>">
		<?php echo $titleTagOpen. $post['post_title']. $titleTagClose?>
		<div>
			<?php if($this->params['show_content'] == 'excerpt') {
				if(!empty($this->params['image_align']) && $this->params['image_align'] != 'no_image') {
					$thumbClass = '';
					$thumbAttrs = array();
					if($this->params['image_align'] == 'left')
						$thumbClass = 'alignleft';
					elseif($this->params['image_align'] == 'right')
						$thumbClass = 'alignright';
					elseif($this->params['image_align'] == 'center')
						$thumbClass = 'aligncenter';
					if(!empty($thumbClass))
						$thumbAttrs['class'] = $thumbClass;
					$thumb = get_the_post_thumbnail($post['ID'], 'post-thumbnail', $thumbAttrs);
					echo $thumb;
				}
				echo $post['post_excerpt'];
				if(!empty($this->params['read_more_text'])) {
					echo '<br /><a href="'. get_permalink($post['ID']). '" style="'. $this->params['read_more_styles']. '">'. $this->params['read_more_text']. '</a>';
				}
			} else {
				$content = $post['post_content'];
				$content = apply_filters('the_content', $content);
				$content = str_replace(']]>', ']]&gt;', $content);
				echo $content;
			}?>
		</div>
	</div>
<?php }?>