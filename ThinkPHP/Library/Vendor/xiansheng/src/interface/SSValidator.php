<?php
namespace SSPHPSDK\SSInterface;
use \Closure;

interface SSValidator{
	public static function registRequestValidateFunction(Closure $validator);
}