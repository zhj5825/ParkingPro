package com.yxzz.parkingpro.protocol;

public class MPPRequestHeader {
	
	String _key;
	String _value;
	
	public MPPRequestHeader(String key, String value)
	{
		_key = key;
		_value = value;
	}
	
	public boolean isValid()
	{
		return true;
	};
}
