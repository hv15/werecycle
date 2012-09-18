	<div class="main-container">
            <div class="main wrapper clearfix">	
            	<a href="<?=$_SERVER['HTTP_REFERER']?>"><div class="button back"><p>&#8617; Return to Map</p></div></a>
		
		<?php print_r( $info ); ?>
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