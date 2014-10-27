/*
 * V1 for ParkingPro
 **/

package com.yxzz.parkingpro.activities;

import com.google.android.gms.common.GooglePlayServicesUtil;
import com.google.android.gms.location.LocationRequest;
import com.google.android.gms.maps.CameraUpdate;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.MarkerOptions;
import com.yxzz.parkingpro.R;
import com.yxzz.parkingpro.providers.GooglePlaceProvider;

import android.app.SearchManager;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.LoaderManager.LoaderCallbacks;
import android.support.v4.content.CursorLoader;
import android.support.v4.content.Loader;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.SearchView;

public class MainActivity extends FragmentActivity implements LocationListener, LoaderCallbacks<Cursor> {

	private GoogleMap _map;
	private LocationManager _LocationManager;

	// The minimum distance to change Updates in meters
	private static final long MIN_DISTANCE_CHANGE_FOR_UPDATES = 10; // 10 meters
	// The minimum time between updates in milliseconds
	private static final long MIN_TIME_BW_UPDATES = 1000 * 60 * 1; // 1 minute

	private static final LocationRequest REQUEST = LocationRequest.create()
			.setInterval(5000)         // 5 seconds
			.setFastestInterval(16)    // 16ms = 60fps
			.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);

		int status = GooglePlayServicesUtil.isGooglePlayServicesAvailable(getApplicationContext());
		// Check the status, warn the user if the phone does not support our app
		
		_LocationManager = (LocationManager) getSystemService(Context.LOCATION_SERVICE);
		
		setUpMapIfNeeded();
		
		Location location = getLastLocation();
		LatLng latlng = new LatLng(location.getLatitude(), location.getLongitude());
		_map.animateCamera(CameraUpdateFactory.newLatLngZoom(latlng, 15));
		
		handleIntent(getIntent());
	}   

	@Override
	protected void onResume() {
		super.onResume();
	}

	@Override
	public void onPause() {
		super.onPause();
	}    

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.menu_main, menu);
		
		// Get the SearchView and set the searchable configuration
	    SearchManager searchManager = (SearchManager) getSystemService(Context.SEARCH_SERVICE);
	    SearchView searchView = (SearchView) menu.findItem(R.id.menuitem_search).getActionView();
	    // Assumes current activity is the searchable activity
	    searchView.setSearchableInfo(searchManager.getSearchableInfo(getComponentName()));
	    searchView.setIconifiedByDefault(false); // Do not iconify the widget; expand it by default

		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// Handle item selection
		switch (item.getItemId())
		{
			case R.id.menuitem_maptype:
			{
				return true;
			}
			case R.id.action_satellite:
			{
				if (item.isChecked())
				{
					_map.setMapType(GoogleMap.MAP_TYPE_NORMAL);
					item.setChecked(false);
					return true;
				}
				else 
				{
					_map.setMapType(GoogleMap.MAP_TYPE_SATELLITE);
					item.setChecked(true);
					return true;					
				}
			}
		}
		return super.onOptionsItemSelected(item);
	}
	
	@Override
    public boolean onMenuItemSelected(int featureId, MenuItem item) {
        switch(item.getItemId()){
        case R.id.menuitem_search:
            onSearchRequested();
            break;
        }
        return super.onMenuItemSelected(featureId, item);
    }

	@Override
	public boolean onPrepareOptionsMenu(Menu menu) 
	{
		//getMenuInflater().inflate(R.menu.menu_maptype, menu);
		return super.onPrepareOptionsMenu(menu);
	}
	
	private void setUpMapIfNeeded() {
		// Do a null check to confirm that we have not already instantiated the map.
		if (_map == null) {
			// Try to obtain the map from the SupportMapFragment.
			_map = ((SupportMapFragment) getSupportFragmentManager().findFragmentById(R.id.map))
					.getMap();
			// Check if we were successful in obtaining the map.
			if (_map != null) {
				setUpMap();
				_map.setMyLocationEnabled(true);
			}
		}
	}

	private void setUpMap() {
		//_map.addMarker(new MarkerOptions().position(new LatLng(0, 0)).title("Marker"));
	}

	public Location getLastLocation() {

		Location location = null;
		if (_LocationManager == null)
			return null;

		try {
			// getting GPS status
			boolean isGPSEnabled = _LocationManager.isProviderEnabled(LocationManager.GPS_PROVIDER);

			// getting network status
			boolean isNetworkEnabled = _LocationManager.isProviderEnabled(LocationManager.NETWORK_PROVIDER);

			if (!isGPSEnabled && !isNetworkEnabled) {
				// no network provider is enabled
			} else {
				//this.canGetLocation = true;
				// First get location from Network Provider
					if (isNetworkEnabled) {
						_LocationManager.requestLocationUpdates(
								LocationManager.NETWORK_PROVIDER,
								MIN_TIME_BW_UPDATES,
								MIN_DISTANCE_CHANGE_FOR_UPDATES, this);
						Log.d("Network", "Network");
						location = _LocationManager.getLastKnownLocation(LocationManager.NETWORK_PROVIDER);
					} 
					else if (isGPSEnabled)
					{
						_LocationManager.requestLocationUpdates(
								LocationManager.GPS_PROVIDER,
								MIN_TIME_BW_UPDATES,
								MIN_DISTANCE_CHANGE_FOR_UPDATES, this);
						location = _LocationManager.getLastKnownLocation(LocationManager.GPS_PROVIDER);
					}
				}
		} catch (Exception e) {
			e.printStackTrace();
		}

		return location;
	}

	@Override
	public void onLocationChanged(Location arg0) {
		// TODO Auto-generated method stub

	}

	@Override
	public void onProviderDisabled(String arg0) {
		// TODO Auto-generated method stub

	}

	@Override
	public void onProviderEnabled(String arg0) {
		// TODO Auto-generated method stub

	}

	@Override
	public void onStatusChanged(String arg0, int arg1, Bundle arg2) {
		// TODO Auto-generated method stub

	}
	
	private void handleIntent(Intent intent){
        if(intent.getAction().equals(Intent.ACTION_SEARCH)){
            doSearch(intent.getStringExtra(SearchManager.QUERY));
        }else if(intent.getAction().equals(Intent.ACTION_VIEW)){
            getPlace(intent.getStringExtra(SearchManager.EXTRA_DATA_KEY));
        }
    }
	
	@Override
    protected void onNewIntent(Intent intent) {
        super.onNewIntent(intent);
        setIntent(intent);
        handleIntent(intent);
    }
 
    private void doSearch(String query){
        Bundle data = new Bundle();
        data.putString("query", query);
        getSupportLoaderManager().restartLoader(0, data, this);
    }
    
    private void getPlace(String query){
        Bundle data = new Bundle();
        data.putString("query", query);
        getSupportLoaderManager().restartLoader(1, data, this);
    }
    
    @Override
    public Loader<Cursor> onCreateLoader(int arg0, Bundle query) {
        CursorLoader cLoader = null;
        if(arg0==0)
            cLoader = new CursorLoader(getBaseContext(), GooglePlaceProvider.SEARCH_URI, null, null, new String[]{ query.getString("query") }, null);
        else if(arg0==1)
            cLoader = new CursorLoader(getBaseContext(), GooglePlaceProvider.DETAILS_URI, null, null, new String[]{ query.getString("query") }, null);
        return cLoader;
    }
 
    @Override
    public void onLoadFinished(Loader<Cursor> arg0, Cursor c) {
    	// Add if cursor is null
        showLocations(c);
    }
 
    @Override
    public void onLoaderReset(Loader<Cursor> arg0) {
        // TODO Auto-generated method stub
    }
 
    private void showLocations(Cursor c){
        MarkerOptions markerOptions = null;
        LatLng position = null;
        _map.clear();
        while(c.moveToNext()){
            markerOptions = new MarkerOptions();
            position = new LatLng(Double.parseDouble(c.getString(1)),Double.parseDouble(c.getString(2)));
            markerOptions.position(position);
            markerOptions.title(c.getString(0));
            _map.addMarker(markerOptions);
        }
        if(position!=null){
            CameraUpdate cameraPosition = CameraUpdateFactory.newLatLng(position);
            _map.animateCamera(cameraPosition);
        }
    }
}
