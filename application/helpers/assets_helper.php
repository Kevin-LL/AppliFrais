<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('css_url'))
{
	function css_url($nom)
	{
		return base_url().'assets/css/'.$nom;
	}
}

if ( ! function_exists('img_url'))
{
	function img_url($nom)
	{
		return base_url().'assets/img/'.$nom;
	}
}

if ( ! function_exists('js_url'))
{
	function js_url($nom)
	{
		return base_url().'assets/js/'.$nom;
	}
}

if ( ! function_exists('jquery_url'))
{
	function jquery_url($nom)
	{
		return base_url().'assets/js/jquery/'.$nom;
	}
}