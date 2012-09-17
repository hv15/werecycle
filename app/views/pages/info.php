	<div class="main-container">
            <div class="main wrapper clearfix">	
            	<div onclick="history.back();" class="button" id="ButtonBack">Back</div>
		
		<?php echo $info; 
		
		foreach($categories as $category_id => $category) { ?>
			<div class="category" id="category<?=$category_id?>">
				<div class="categoryToggle"></div>
				<div class="categoryName"><?=$category['name'];?></div>
				<div class="categoryCount">
					<p class="selectedCount">0</p>/<p class="totalCount"><?=count($category['types'])?></p>
				</div>
			</div>
			<div class="types" id="category<?=$category_id?>Types">
				<?php foreach($category['types'] as $id => $recycle_type) { ?>
					<div class="type" id="type<?=$id?>">
						<p class="typeName"><?=$recycle_type['name']?></p>
						<p class="typeInfoButton">i</p>
						<div class="typeInfoText"><?=$recycle_type['description']?></div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>	
            </div> <!-- #main -->
        </div> <!-- #main-container -->