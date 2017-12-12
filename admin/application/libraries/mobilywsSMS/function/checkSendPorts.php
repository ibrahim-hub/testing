<?php
//Function to check fsockopen method
function fsockopenTest()
{
	$testValue = 0;
	if(function_exists("fsockopen"))
		++$testValue;
	if(function_exists("fputs"))
		++$testValue;
	if(function_exists("feof"))
		++$testValue;
	if(function_exists("fread"))
		++$testValue;
	if(function_exists("fclose"))
		++$testValue;
	return $testValue;
}

//Function to check curl method
function curlTest()
{
	$testValue = 0;
	if(function_exists("curl_init"))
		++$testValue;
	if(function_exists("curl_setopt"))
		++$testValue;
	if(function_exists("curl_exec"))
		++$testValue;
	if(function_exists("curl_close"))
		++$testValue;
	if(function_exists("curl_errno"))
		++$testValue;
	return $testValue;
}

//Function to check fopen method
function fopenTest()
{
	$testValue = 0;	
	if(function_exists("fopen"))
		++$testValue;
	if(function_exists("fclose"))
		++$testValue;	
	if(function_exists("fread"))
		++$testValue;	
	return $testValue;
}

//Function to check file method
function fileTest()
{
	$testValue = 0;	
	if(function_exists("file"))
		++$testValue;
	if(function_exists("http_build_query"))
		++$testValue;	
	if(function_exists("stream_context_create"))
		++$testValue;	
	return $testValue;
}

//Function to check filegetcontentsTest method
function filegetcontentsTest()
{
	$testValue = 0;	
	if(function_exists("file_get_contents"))
		++$testValue;
	if(function_exists("http_build_query"))
		++$testValue;	
	if(function_exists("stream_context_create"))
		++$testValue;	
	return $testValue;
}
?>