/*
 * V1 for ParkingPro
 **/

package com.yxzz.parkingpro.activities;

import com.google.android.gms.common.GooglePlayServicesUtil;
import com.google.android.gms.location.LocationRequest;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.LatLng;
import com.yxzz.parkingpro.R;

import android.content.Context;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;

public class MainActivity extends FragmentActivity implements LocationListener {

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
		// Check the status later ...
		
		_LocationManager = (LocationManager) getSystemService(Context.LOCATION_SERVICE);
	}   

	@Override
	protected void onResume() {
		super.onResume();
		setUpMapIfNeeded();
		
		Location location = getLastLocation();
		LatLng latlng = new LatLng(location.getLatitude(), location.getLongitude());
		_map.animateCamera(CameraUpdateFactory.newLatLngZoom(latlng, 15));
	}

	@Override
	public void onPause() {
		super.onPause();
	}    

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.menu_main, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// Handle item selection
		if (item.getItemId() == R.id.menu_maptype) {
			// Start choose map type activity here
			return true;
		}
		return super.onOptionsItemSelected(item);
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
}
