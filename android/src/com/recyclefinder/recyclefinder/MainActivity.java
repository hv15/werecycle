package com.recyclefinder.recyclefinder;

import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.Window;
import android.webkit.GeolocationPermissions;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface.OnKeyListener;
import android.content.Intent;
import android.content.DialogInterface;

public class MainActivity extends Activity {
	final Activity activity = this;

	private class MyWebViewClient extends WebViewClient {
		@Override
		public boolean shouldOverrideUrlLoading(WebView view, String url) {
			if (url.startsWith("mailto:") || url.startsWith("tel:") || url.startsWith("http://maps")) { 
				Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url)); 
				startActivity(intent); 
				return true;
			}
			return false;
		}

	   public void onReceivedError(WebView view, int errorCode, String description, String failingUrl) {
	     Toast.makeText(activity, "Oh no! " + description, Toast.LENGTH_SHORT).show();
	   }
	}

	private class MyWebChromeClient extends WebChromeClient {		
		@Override
		public void onGeolocationPermissionsShowPrompt(String origin, GeolocationPermissions.Callback callback) {
			callback.invoke(origin, true, false);
		}
		
	   public void onProgressChanged(WebView view, int progress) {
		    // Activities and WebViews measure progress with different scales.
		   // The progress meter will automatically disappear when we reach 100%
	     activity.setProgress(progress * 100);
	   }
	}

	private boolean isNetworkAvailable() {
	    ConnectivityManager connectivityManager 
	          = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
	    NetworkInfo activeNetworkInfo = connectivityManager.getActiveNetworkInfo();
	    return activeNetworkInfo != null && activeNetworkInfo.isConnectedOrConnecting();
	}
	
	public void showErrorDialogAndQuit(final String message) {
	    runOnUiThread(new Runnable() {
	        @Override
	        public void run() {
	            AlertDialog aDialog = new AlertDialog.Builder(MainActivity.this).setMessage(message).setTitle("Error")
	                    .setNeutralButton("Close", new AlertDialog.OnClickListener() {
	                        public void onClick(final DialogInterface dialog, final int which) {
	                            // Exit the application.
	                        	finish();
	                            return;
	                        }
	                    }).create();
	            aDialog.setOnKeyListener(new OnKeyListener() {
	                @Override
	                public boolean onKey(DialogInterface dialog, int keyCode, KeyEvent event) {
	                    // Disables the back button.
	                    return true;
	                }

	            });
	            aDialog.show();
	        }
	    });
	}
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		getWindow().requestFeature(Window.FEATURE_PROGRESS);
		setContentView(R.layout.webview);
		WebView wv = (WebView) findViewById(R.id.webview);

		MyWebViewClient wvClient = new MyWebViewClient();
		MyWebChromeClient wvChromeClient = new MyWebChromeClient();
		
		wv.setWebChromeClient(wvChromeClient);
		wv.setWebViewClient(wvClient);

		// Configure the webview
		WebSettings s = wv.getSettings();
		s.setBuiltInZoomControls(true);
		s.setLayoutAlgorithm(WebSettings.LayoutAlgorithm.NARROW_COLUMNS);
		s.setUseWideViewPort(true);
		s.setLoadWithOverviewMode(true);
		s.setSavePassword(true);
		s.setSaveFormData(true);
		s.setJavaScriptEnabled(true);

		// enable navigator.geolocation 
		s.setGeolocationEnabled(true);

		// enable Web Storage: localStorage, sessionStorage
		s.setDomStorageEnabled(true);

		if(isNetworkAvailable()) {
			wv.loadUrl("http://www.recyclefinder.co.uk");
		} else {
			showErrorDialogAndQuit("No network connectivity");
		}
	}

}
