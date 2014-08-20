package com.yxzz.parkingpro.protocol;

import java.util.ArrayList;

import android.util.Base64;

public class MPPRequest {
	
	String _RequestLine;
	ArrayList<String> _RequestHeaders;
	
	public final static String MPPVERSION = "MPP/1.0";
	public final static String SP = " ";
	public final static String CRLF = "\r\n";
	public final static String COLON = ":";
	
	public class MPPRequestMethod {

		public final static String SIGNUP = "SIGNUP";
		public final static String LOGIN = "LOGIN";
		public final static String LOGOUT = "LOGOUT";
		public final static String CHARGE = "CHARGE";
		public final static String VERIFYCHARGE = "VERIFYCHARGE";
		public final static String PAY = "PAY";
		public final static String ADDPAYMT = "ADDPAYMT";
		public final static String DELPAYMT = "DELPAYMT";
		public final static String PROMO = "PROMO";
		public final static String VIEWHISTO = "VIEWHISTO";
		public final static String ADDITEM = "ADDITEM";
	};
	
	public MPPRequest()
	{
		_RequestHeaders = new ArrayList<String>();
	};
	
	private void addRequestType(String method){
		
		if (_RequestLine == null)
		{
			_RequestLine = method + SP + MPPVERSION + CRLF;
		}
	};
	
	private void addRequestHeader(MPPRequestHeader header){
		
		if (header.isValid())
		{
			_RequestHeaders.add(header._key + COLON + SP + header._value + CRLF);
		}
	};
	
	private void populate(String method, ArrayList<MPPRequestHeader> list)
	{
		addRequestType(method);
		
		for (int i = 0; i < list.size(); i++)
		{
			addRequestHeader(list.get(i));
		}
	};
	
	public void populateSIGNUP(ArrayList<MPPRequestHeader> list){
		
		populate(MPPRequestMethod.SIGNUP, list);
	};
	
	public void populateLOGIN(ArrayList<MPPRequestHeader> list){
		
		populate(MPPRequestMethod.LOGIN, list);
	};
	
	public void populateLOGOUT(ArrayList<MPPRequestHeader> list){
		
		populate(MPPRequestMethod.LOGOUT, list);
	};
	
	public void populateCHARGE(ArrayList<MPPRequestHeader> list){
		
		populate(MPPRequestMethod.CHARGE, list);
	};
	
	public void populateVERIFYCHARGE(ArrayList<MPPRequestHeader> list){
		
		populate(MPPRequestMethod.VERIFYCHARGE, list);
	};
	
	public void populatePAY(ArrayList<MPPRequestHeader> list){
		
		populate(MPPRequestMethod.PAY, list);
	};
	
	public void populateADDPAYMT(ArrayList<MPPRequestHeader> list){
		
		populate(MPPRequestMethod.ADDPAYMT, list);
	};
	
	public void populateDELPAYMT(ArrayList<MPPRequestHeader> list){
		
		populate(MPPRequestMethod.DELPAYMT, list);
	};
	
	public void populatePROMO(ArrayList<MPPRequestHeader> list){
		
		populate(MPPRequestMethod.PROMO, list);
	};
	
	public void populateVIEWHISTO(ArrayList<MPPRequestHeader> list){
		
		populate(MPPRequestMethod.VIEWHISTO, list);
	};
	
	public void populateADDITEM(ArrayList<MPPRequestHeader> list){
		
		populate(MPPRequestMethod.ADDITEM, list);
	};
	
	/*public void populateSIGNUP(EmailRequestHeader email, 
			PasswordRequestHeader password, 
			RoletypeRequestHeader roletype, 
			String firstname, 
			String lastname,
			AddressRequestHeader address,
			String phone,
			SSNRequestHeader ssn,
			RoutingNumberRequestHeader routingnumber,
			AccountNumberRequestHeader accountnumber,
			String businessname,
			String businessdescription){
		
		addRequestType(MPPRequestMethod.SIGNUP);
		
		addRequestHeader(email);
		addRequestHeader(password);
		addRequestHeader(roletype);
		addRequestHeader(address);
		addRequestHeader(ssn);
		addRequestHeader(routingnumber);
		addRequestHeader(accountnumber);
	};*/
	
	public String generateMessage(){
		
		String tmp = _RequestLine;
		for (int i = 0; i < _RequestHeaders.size(); i++)
		{
			tmp += _RequestHeaders.get(i);
		}
		
		return tmp + CRLF;
	};
	
	public String generateEncodedMessage(){
		
		return Base64.encodeToString(generateMessage().getBytes(), Base64.DEFAULT);
	};
	
}
