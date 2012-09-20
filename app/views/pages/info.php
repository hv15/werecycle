	<div class="main-container">
            <div class="main wrapper clearfix">	
            	<a onclick="window.location.href='<?=(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/')?>'">
					<div class="button back"><p>&#8617; Return to Map</p></div>
				</a>
				
				<div class='nametitle'>Name</div>
				<div class='name'><?=$info['name']?></div>
				<br />
				<?php if($info['phone']) { ?>
				<div class='phonetitle'>Phone</div>
				<div class='phone'>
					<a href='tel:<?=preg_replace('|[^0-9]|','',$info['phone'])?>'><?=$info['phone']?></a>
				</div>
				<br />
				<?php } if($info['address']) { ?>
				<div class='addresstitle'>Address</div>
				<div class='address'>
					<a href='http://maps.google.com/maps?q=<?=$info['latitude']?>,<?=$info['longitude']?>' target='_blank'>
						<?=$info['address']?>
					</a>
				</div>
				<br />
				<?php } if($info['openhours']) { ?>
				<div class='openhourstitle'>Opening Hours</div>
				<div class='openhours'><?=$info['openhours']?>
				<br />
				<?php } ?>
				<div class="info">
					<p>Press a category to expand</p>
				</div>
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
