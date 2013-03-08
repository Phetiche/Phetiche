<?php

function smarty_modifier_powerball($string, $length = 80, $etc = '...', $break_words = false, $middle = false) {
	return $string . ' You just got POWERBALLED';
}
