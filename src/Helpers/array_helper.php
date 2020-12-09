<?php

if (!\function_exists('is_assoc_array')) {
	/**
	 * Checks if the array is associative.
	 *
	 * @param array $array Array to check
	 *
	 * @return bool True if associative, false otherwise
	 */
	function is_assoc_array(array $array): bool
	{
		return \array_keys($array) !== \range(0, \count($array) - 1);
	}
}

if (!\function_exists('is_multi_array')) {
	/**
	 * Checks if the array is multi dimensional.
	 *
	 * @param array $array Array to check
	 *
	 * @return bool True if associative, false otherwise
	 */
	function is_multi_array(array $array): bool
	{
		foreach ($array as $item) {
			if (\is_array($item)) {
				return true;
			}
		}

		return false;
	}
}

if (!\function_exists('is_nonempty_array')) {
	/**
	 * Checks if the variable is array and not empty.
	 *
	 * @param mixed $var Variable to check
	 *
	 * @return bool True if the variable is an array and not empty, false otherwise
	 */
	function is_nonempty_array($var): bool
	{
		return \is_array($var) && $var != [];
	}
}
