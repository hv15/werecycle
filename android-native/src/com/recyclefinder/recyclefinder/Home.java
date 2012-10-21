package com.recyclefinder.recyclefinder;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.RelativeLayout;

public class Home extends Activity {
	
	RelativeLayout home;

	@Override
	public void onCreate(Bundle savedInstanceState) {
	    super.onCreate(savedInstanceState);
	    setContentView(R.layout.home);
	    home = (RelativeLayout) findViewById(R.id.home);
	    
	    Button home_search = (Button) findViewById(R.id.home_search);
	    home_search.setOnClickListener(new View.OnClickListener() {
            	public void onClick(View view) {
                Intent loadMap = new Intent(view.getContext(), MainActivity.class);
                startActivityForResult(loadMap, 0);
            }

        });
	}

}
