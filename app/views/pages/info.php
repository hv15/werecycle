	<div class="main-container">
            <div class="main wrapper clearfix">	
            	<a href="<?=$_SERVER['HTTP_REFERER']?>" class="button" id="ButtonBack">&#8617; Return to Map</a>
		
		<?php echo $info; 
		
		foreach($categories as $category_id => $category) { ?>
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