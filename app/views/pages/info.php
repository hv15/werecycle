	<div class="main-container">
            <div class="main wrapper clearfix">	
            	<a href="<?=(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/')?>"><div class="button back"><p>&#8617; Return to Map</p></div></a>
		
		<span class='nametitle'>Name</span><br />
		<span class='name'><?=$info['name']?></span><br /><br />
		<span class='phonetitle'>Phone</span><br />
		<span class='phone'><?=$info['phone']?></span><br /><br />

		<span class='addresstitle'>Address</span><br />
		<div class='address'>
			<a href='http://maps.google.com/maps?q=<?=$info['latitude']?>,<?=$info['longitude']?>' target='_blank'>
				<?=$info['address']?>
			</a>
		</div><br /><br />

		<span class='openhourstitle'>Opening Hours</span><br />
		<div class='openhours'>
			<?=$info['openhours']?>
		</div>
		
		<div class="info"><p>Press a category to expand</p></div>
		<?php foreach($categories as $category_id => $category) { ?>
			<div class="category" id="category<?=$category_id?>">
				<div class="categoryToggle"></div>
				<div class="categoryName"><?=$category['name'];?></div>
			</div>
			<div class="types" id="category<?=$category_id?>Types">
				<?php foreach($category['types'] as $id => $recycle_type) { ?>
					<div class="type" id="type<?=$id?>">
						<p class="typeInfoButton">i</p>
						<p class="typeName"><?=$recycle_type['name']?></p>
						<div class="typeInfoText"><?=$recycle_type['description']?></div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>	
            </div> <!-- #main -->
        </div> <!-- #main-container -->