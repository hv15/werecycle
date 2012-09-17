<?php 
	$userdata = $this->session->all_userdata(); 
	if( !isset($userdata['home_latitude']) OR !isset($userdata['home_longitude']) ) {
		$this->session->set_flashdata('message', 'Your session expired, please start again.');
		echo 'window.location.href = "/";'; return;
	}
?>
	<div class="main-container">
            <div class="main wrapper clearfix">
		<div id="info">
			<p>Select the items you want to recycle</p>
		</div>
		
		<?php 
		$userdata = $this->session->all_userdata();
		$selectedtypes  = (isset($userdata['types_selected']) ? explode(',',$userdata['types_selected']) : array(1,7,6,16));
		foreach($categories as $category_id => $category) { ?>
			<div class="category" id="category<?=$category_id?>">
				<div class="categoryToggle">►</div>
				<div class="categoryName"><?=$category['name'];?></div>
				<div class="categoryCount">
					<p class="selectedCount">0</p>/<p class="totalCount"><?=count($category['types'])?></p>
				</div>
			</div>
			<div class="types" id="category<?=$category_id?>Types">
				<?php foreach($category['types'] as $id => $recycle_type) { ?>
					<div class="type" id="type<?=$id?>">
						<p class="typeCheckboxP">
							<input class="typeCheckbox" type="checkbox" <?=(in_array($id,$selectedtypes) ? 'checked="checked"' : '')?> id="checkbox<?=$id?>" name="checkbox<?=$id?>" value="<?=$id?>" />
						</p>
						<label for="checkbox<?=$id?>" class="typeName"><?=$recycle_type['name']?></label>
						<p class="typeInfoButton">i</p>
						<p class="typeInfoText"><?=$recycle_type['description']?></p>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
		
		<div id="searchButton" class="button left" onclick="createQuery();">
			<p>Search recycling facilities...</p>
		</div>
            </div> <!-- #main -->
        </div> <!-- #main-container -->