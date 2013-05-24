<?php

class Phetiche_array {

	public static function mergeAtPosition($original = [], $merge = [], $position = 0)
	{
		$split_elements = array_splice($original, $position);
    	$original = array_merge($original, $merge, $split_elements);

		return $original;
	}


	public static function isassoc($array)
	{
		return is_array($array) && array_diff_key($array, array_keys(array_keys($array)));
	}


	public static function keyname($array, $position)
	{
		$new_array = array_slice($array, $position, 1, true);
		return key($new_array);
	}


	public static function excludekeys($data, $table_cols)
	{
		if (!is_array($data) && !is_object($data)) {
			return false;
		}

		$new_data = [];
		foreach ($table_cols as $col) {
			foreach ($data as $key => $item) {
				if ($col == $key) {
					$new_data[$key] = $item;
				}
			}
		}

		return $new_data;
	}


	/**
	 * Sort an array on any given column and in any given order.
	 *
	 * @access	Public
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @version	0.1
	 * @since	30/12/2011
	 * @param	(array) $array_to_sort The array to be sorted (Passed by reference)
	 * @param	(string) $order How to order the array
	 * @param	(string) $column_to_sort The column on which to sort the array
	 */
	public static function order(&$array_to_sort, $order = 'ASC', $column_to_sort = 'match_amount')
	{
		$column = [];
		foreach ($array_to_sort as $key => $row) {
		    $column[$key] = $row[$column_to_sort];
		}

		$order = (strtoupper($order) == 'ASC') ? SORT_ASC : SORT_DESC;
		array_multisort($column, $order, $array_to_sort);
	}

}