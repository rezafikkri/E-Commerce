<?php

spl_autoload_register(function($class){
	include 'class/class_'.$class.'.php';
});