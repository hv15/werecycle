/*
 * Copyright 2008 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
package com.google.android.apps.recyclefinder;

import com.google.android.apps.recyclefinder.services.RemoveTempFilesService;
import com.google.android.apps.recyclefinder.util.AnalyticsUtils;
import com.google.android.apps.recyclefinder.util.ApiAdapterFactory;
import com.google.android.maps.recyclefinder.BuildConfig;

import android.app.Application;
import android.content.Intent;

/**
 * RecycleFinderApplication for keeping global state.
 * 
 * @author Jimmy Shih
 */
public class RecycleFinderApplication extends Application {

  @Override
  public void onCreate() {
    super.onCreate();
    if (BuildConfig.DEBUG) {
      ApiAdapterFactory.getApiAdapter().enableStrictMode();
    }
    AnalyticsUtils.sendPageViews(getApplicationContext(), "/appstart");
    Intent intent = new Intent(this, RemoveTempFilesService.class);
    startService(intent);
  }
}
