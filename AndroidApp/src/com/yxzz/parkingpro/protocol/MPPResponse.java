package com.yxzz.parkingpro.protocol;

import java.util.ArrayList;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class MPPResponse {
	
	private String _buf;
	private String _StatusLine;
	private ArrayList<String> _ResponseHeaders;
	private Pattern _ErrorCode;
	
	public MPPResponse(String s) {
		_buf = s;
		_ResponseHeaders = new ArrayList<String>();
	};
	
	/*
	 * Return 1 for ill-formed response 
	 */
	public int parse() {
		
		String[] Lines = _buf.split("\r\n");
		_StatusLine = new String(Lines[0]);
		if (!_StatusLine.matches("^MPP/1.0 [0-9]{4} [A-Za-z\\s]+"))
			return 1;
		
		for (String item : Lines)
		{
			_ResponseHeaders.add(item);
		}
		
		return 0;
	};
	
	public String getStatusLine() {
	
		return _StatusLine;
	};
	
	public String getErrorCode() {
		return _StatusLine.substring(8, 12);
	};
	
	public int getNumericalErrorCode() {
		return Integer.parseInt(getErrorCode());
	};

	public String getReasonPhrase() {
		return _StatusLine.substring(13, _StatusLine.length());
	};
	
	public String getSessionID() {
		
		for (String item : _ResponseHeaders)
		{
			if (item.contains(MPPRequestHeaderKey.SESSIONID))
			{
				return item.substring(MPPRequestHeaderKey.SESSIONID.length() + 2, item.length());
			}
		}
		
		return "";
	};	
	
	public String getResponseHeader(String key) {
		
		for (String item : _ResponseHeaders)
		{
			if (item.contains(key))
			{
				return item.substring(key.length() + 2, item.length());
			}
		}
		
		return "";
	};	
}
