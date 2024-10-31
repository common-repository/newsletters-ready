<?php
	$isPreview = ($this->options && isset($this->options['preview']) && $this->options['preview']);
	$isTestMode = false;
	$colPadding = $isTestMode ? 3 : 2;	// !!!! 3 is with borders in test mode, make it 2 - equal to margin of cols !!!
	$maxColsWidth = 0;
	// Let's detect max col width
	foreach($this->stpl['rows'] as $row) {
		$colsWidthSum = 0;
		foreach($row['cols'] as $col) {
			$colsWidthSum += $col['width'] + 2 * $colPadding;
		}
		$maxColsWidth = $colsWidthSum > $maxColsWidth ? $colsWidthSum : $maxColsWidth;
	}
	$colTestAddStyle = '';
	if($isTestMode) {
		$colTestAddStyle = 'border: 1px grey dashed;';
	}
	$additionaContentStyles = array();
	switch($this->stpl['style_params']['background_type']) {
		case 'color':
			$additionaContentStyles[] = 'background-color:'. $this->stpl['style_params']['background_color'];
			break;
		case 'image':
			if(!empty($this->stpl['style_params']['background_image'])) {
				$additionaContentStyles[] = 'background-image:url('. $this->stpl['style_params']['background_image']. ')';
				switch($this->stpl['style_params']['background_img_pos']) {
					case 'stretch':
						$additionaContentStyles[] = 'background-position:center center';
						$additionaContentStyles[] = 'background-repeat:no-repeat';
						$additionaContentStyles[] = '-webkit-background-size:cover';
						$additionaContentStyles[] = '-moz-background-size:cover';
						$additionaContentStyles[] = '-o-background-size:cover';
						$additionaContentStyles[] = 'background-size:cover';
						break;
					case 'tile':
						$additionaContentStyles[] = 'background-repeat:repeat';
						break;
					case 'center':
						$additionaContentStyles[] = 'background-position:center center';
						$additionaContentStyles[] = 'background-repeat:no-repeat';
						break;
				}
			}
			break;
	}
	// Some shadow for main content
	$additionaContentStyles[] = '-webkit-box-shadow:2px 2px 5px 0px rgba(50, 50, 50, 0.75)';
	$additionaContentStyles[] = '-moz-box-shadow:2px 2px 5px 0px rgba(50, 50, 50, 0.75)';
	$additionaContentStyles[] = 'box-shadow:2px 2px 5px 0px rgba(50, 50, 50, 0.75)';
	// Add border radius for main content - 5px
	$additionaContentStyles[] = '-webkit-border-radius:5px';
	$additionaContentStyles[] = '-moz-border-radius:5px;';
	$additionaContentStyles[] = 'border-radius:5px;';
?>
<?php if($isPreview) { ?>
<html>
	<head>
		<?php if(isset($this->options['subject']) && !empty($this->options['subject'])) { ?>
		<title><?php echo $this->options['subject']?></title>
		<?php }?>
	</head>
	<body style="background-color: #EEEEEE;">
<?php }?>
<div class="stplBody">
	<div class="stplContent" style="margin-left:auto;margin-right:auto;width:<?php echo $maxColsWidth?>px;<?php echo (empty($additionaContentStyles) ? '' : implode(';', $additionaContentStyles). ';')?>">
		<?php foreach($this->stpl['rows'] as $row) { ?>
		<?php
			$colsWidthSum = 0;
			$additionalRowStyles = array();
			if(!empty($row['background_color'])) {
				$additionalRowStyles[] = 'background-color:'. $row['background_color'];
			}
		?>
		<div class="stplRow" <?php ?>style="height: <?php echo $row['height']?>px;<?php echo (empty($additionalRowStyles) ? '' : implode(';', $additionalRowStyles). ';')?>"<?php ?>>
			<?php foreach($row['cols'] as $col) { ?>
			<?php 
				$colsWidthSum += $col['width'] + 2 * $colPadding;
			?>
			<div class="stplCol" style="float: left; margin: 2px; width: <?php echo $col['width']?>px;<?php echo $colTestAddStyle?>">
				<?php echo do_shortcode($col['content']);?>
			</div>
			<?php }?>
		</div>
		<?php
			$maxColsWidth = $colsWidthSum > $maxColsWidth ? $colsWidthSum : $maxColsWidth;
		?>
		<?php }?>
		<div style="clear: both;"></div>
	</div>
</div>
<?php if($isPreview) { ?>
	</body>
</html>
<?php } ?>
