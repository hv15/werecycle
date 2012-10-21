package com.recyclefinder.recyclefinder;

import java.util.List;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import com.actionbarsherlock.app.SherlockMapActivity;
import com.actionbarsherlock.view.Menu;
import com.actionbarsherlock.view.MenuInflater;
import com.actionbarsherlock.view.MenuItem;
import com.google.android.maps.GeoPoint;
import com.google.android.maps.MapView;
import com.google.android.maps.Overlay;
import com.google.android.maps.OverlayItem;

public class MainActivity extends SherlockMapActivity {
	
	MapView map;
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu){
		MenuInflater inflater = getSupportMenuInflater();
		inflater.inflate(R.menu.main_activity, menu);
		return true;
	}

	@Override
	protected boolean isRouteDisplayed() {
		// TODO Auto-generated method stub
		return false;
	}
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
	    super.onCreate(savedInstanceState);
	    setContentView(R.layout.map);
	    map = (MapView) findViewById(R.id.map);
	    
	    // Adding Marker to map, which is tappable
	    List<Overlay> mapOverlays = map.getOverlays();
	    Drawable drawable = this.getResources().getDrawable(R.drawable.testmarker);
	    HelloItemizedOverlay itemizedoverlay = new HelloItemizedOverlay(drawable, this);
	    
	    //Single location, should be Mexico City
	    GeoPoint point = new GeoPoint(19240000,-99120000);
	    OverlayItem overlayitem = new OverlayItem(point, "Hola, Mundo!", "I'm in Mexico City!");
	    
	    itemizedoverlay.addOverlay(overlayitem);
	    mapOverlays.add(itemizedoverlay);	    
	}


	 /**
	 * Event Handling for Individual menu item selected
	 * Identify single menu item by it's id
	 * */
	 @Override
	 public boolean onOptionsItemSelected(MenuItem item)
	 {

		 switch (item.getItemId())
		 {
/*		 case R.id.my_location:
			 // Single menu item is selected do something
			 Toast.makeText(GooglemapsActivity.this, "Moving To Current location", Toast.LENGTH_SHORT).show();
			 locLstnr.gpsCurrentLocation();

			 return true;

		 case R.id.mapview_normal:
		 Toast.makeText(GooglemapsActivity.this, "Map Normal Street View", Toast.LENGTH_SHORT).show();
		 if(mapView.isSatellite()==true){
			 mapView.setSatellite(false);
		 }
		 return true;

		 case R.id.mapview_satellite:
			 Toast.makeText(GooglemapsActivity.this, "Map Satellite View", Toast.LENGTH_SHORT).show();
			 if(mapView.isSatellite()==false){
				 mapView.setSatellite(true);
			 }
			 return true;
*/
		 default:
			 return super.onOptionsItemSelected(item);
		 }
	 }
	 
	 
}
