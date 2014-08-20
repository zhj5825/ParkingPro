package com.yxzz.parkingpro.comm;

import java.util.ArrayList;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.BasicResponseHandler;

import com.yxzz.parkingpro.protocol.MPPResponse;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.http.AndroidHttpClient;
import android.nfc.NdefMessage;
import android.nfc.NfcAdapter;
import android.os.AsyncTask;
import android.os.Parcelable;

public class CommServerAsyncTask extends AsyncTask<String, Integer, Boolean> 
{
	public final static String SIGNUPAC = "SIGNUPAC";
	public final static String LOGINAC = "LOGINAC";
	public final static String LOGINNFCAC = "LOGINNFCAC";
	public final static String CHARGEACNFC = "CHARGEACNFC";
	public final static String CHARGEACQR = "CHARGEACQR";
	public final static String VERIFYCHARGEACQR = "VERIFYCHARGEAC";
	public final static String PAYQR = "PAYQR";
	public final static String ADDCARD = "ADDCARD";
	
	public final static String SERVERURL = "http://honeyproject1.fulton.asu.edu/mpayment/interface/sendmpp.php";
	//public final static String SERVERURL = "http://10.211.22.11/mpayment/interface/sendmpp.php";
	
	private String previousactivity_;
	private Activity mainactivity_;
	private ProgressDialog dialog_;
    private AndroidHttpClient httpclient_;
    
    private int errortype_;
    private String reasonphrase_;
	
    CommServerAsyncTask(Activity activity, ProgressDialog dialog)
	{
		this.mainactivity_ = activity;
		this.dialog_ = dialog;
	}
	
	@Override
	protected void onPreExecute() {
		super.onPreExecute();
	}

	@Override
	protected Boolean doInBackground(String... arg) {
		
		previousactivity_ = arg[1];
		
		if (httpclient_ == null)
    		httpclient_ = createHttpClient();

    	HttpPost request = new HttpPost(SERVERURL);
    	
    	try { 		
        	request.setEntity(new StringEntity(arg[0]));
        	request.setHeader("Content-type", "application/x-www-form-urlencoded");
    		String response = httpclient_.execute(request, new BasicResponseHandler());
    
    	} catch (Exception e) {
    		//Toast.makeText(getApplicationContext(), "Can't communicate with bank!", Toast.LENGTH_LONG).show();
    		e.printStackTrace();
    		return false;
    	}
		return true;
	}
	
	@Override
	protected void onProgressUpdate(Integer... message) {
		super.onProgressUpdate(message);
	}

	@Override
	protected void onPostExecute(Boolean result) {
		super.onPostExecute(result);

		if (!result) {
			if (dialog_ != null)
				dialog_.dismiss();

			AlertDialog.Builder networkerrdialogbuilder_;

			networkerrdialogbuilder_ = new AlertDialog.Builder(mainactivity_);
			networkerrdialogbuilder_
					.setMessage(
							"Network Error. Can not connect to GFS mPayment Gateway. Please try later.")
					.setCancelable(false)
					.setPositiveButton("OK",
							new DialogInterface.OnClickListener() {
								public void onClick(DialogInterface dialog,
										int id) {
									// do things
								}
							});

			AlertDialog ad = networkerrdialogbuilder_.create();
			ad.show();
		}
		else
		{
			if (dialog_ != null)
				dialog_.dismiss();

			if (errortype_ == 2000) {
				
			}
			else if (errortype_ == 4501)
			{
			
			}
			else { // Error goes here

				AlertDialog.Builder networkerrdialogbuilder_;

				networkerrdialogbuilder_ = new AlertDialog.Builder(
						mainactivity_);
				networkerrdialogbuilder_
						.setMessage(reasonphrase_)
						.setCancelable(false)
						.setPositiveButton("OK",
								new DialogInterface.OnClickListener() {
									public void onClick(DialogInterface dialog,
											int id) {
										// do things
									}
								});

				AlertDialog ad = networkerrdialogbuilder_.create();
				ad.show();
			}
		}
	}

	private AndroidHttpClient createHttpClient() 
	{
		AndroidHttpClient httpClient = AndroidHttpClient.newInstance("Mozilla/5.0 (Linux; U; Android 2.1; en-us; ADR6200 Build/ERD79) AppleWebKit/530.17 (KHTML, like Gecko) Version/ 4.0 Mobile Safari/530.17");
		return httpClient;
  }

}

