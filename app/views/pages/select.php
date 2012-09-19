<?php 
	$userdata = $this->session->all_userdata(); 
	if( !isset($userdata['latitude']) OR !isset($userdata['longitude']) ) {
		$this->session->set_flashdata('message', 'Your session expired, please start again.');
		echo '<script type="text/javascript">window.location.href = "/";</script>'; return;
	}
?>
	<div class="main-container">
        <div class="main wrapper clearfix">
			<div id="Info">
				<p>Press the items you want to recycle.</p>
				<p>Selecting none will return all types.</p>
			</div>

			<?php 
			$userdata = $this->session->all_userdata();
			$selectedtypes  = (isset($userdata['types_selected']) ? explode(',',$userdata['types_selected']) : array(1,7,6,16));
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
						<div class="type left" id="type<?=$id?>">
							<div class="typeCheckboxItem simplebutton roundLeft" onclick="">
								<input class="typeCheckbox" type="checkbox" <?=(in_array($id,$selectedtypes) ? 'checked="checked"' : '')?> id="checkbox<?=$id?>" name="checkbox<?=$id?>" value="<?=$id?>" />
								<label for="checkbox<?=$id?>" class="typeName"><?=$recycle_type['name']?></label>
							</div>
							<p class="simplebutton roundRight typeInfoButton">i</p>
							<div class="typeInfoText"><?=$recycle_type['description']?></div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
			<div id="SearchButton" class="button" onclick="setTypes();">
				<p>Search recycling facilities...</p>
				<div id="SearchSpin" class="invisible"></div>
			</div>
			<div id="Confirm">
				<b>Confirm</b>
				<p class="confirmText" id="ConfirmText">There are no recycle points within 1 mile of this location which allow all of the types you have selected; however, there is at least one within 5 miles - you may need to zoom out on the map!</p>
				<div id="Cancel" class="button"><p>Cancel</p></div>
				<div id="Continue" class="button"><p>Ok</p></div>
			</div>
			<div id="Alert">
				<b>Alert</b>
				<p class="alertText" id="AlertText">There are no recycle points within 1 mile of this location which allow all of the types you have selected; however, there is at least one within 5 miles - you may need to zoom out on the map!</p>
				<div id="Ok" class="button"><p>OK</p></div>
			</div>
        </div> <!-- #main -->
    </div> <!-- #main-container -->