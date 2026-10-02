<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */


if ( ! function_exists('get_phrase'))
{
	/**
	 * Translate a phrase key into the system language.
	 *
	 * The whole column for the current language is loaded once per request
	 * (instead of 3 queries per call). Unknown keys are inserted so they show
	 * up in Manage Language; untranslated ones fall back to "Title Case" text.
	 */
	function get_phrase($phrase = '') {
		static $phrases = null, $language = null;
		$CI	=&	get_instance();
		$CI->load->database();

		if ($phrases === null) {
			$lang_setting	=	$CI->db->get_where('settings' , array('type' => 'language'))->row();
			$language		=	$lang_setting ? $lang_setting->description : '';
			if ( ! sms_is_language($language, $CI->db->list_fields('language')))
				$language	=	'english';

			$phrases = array();
			foreach ($CI->db->select('phrase, ' . $CI->db->protect_identifiers($language) . ' AS t', FALSE)
							->get('language')->result_array() as $row) {
				// MySQL compares phrase keys case-insensitively, so key the cache the same way.
				$phrases[mb_strtolower($row['phrase'])] = $row['t'];
			}
		}

		$key = mb_strtolower((string)$phrase);
		if ( ! array_key_exists($key, $phrases)) {
			// INSERT IGNORE: the unique phrase index makes concurrent first-use safe.
			$CI->db->query('INSERT IGNORE INTO `language` (`phrase`) VALUES (' . $CI->db->escape((string)$phrase) . ')');
			$phrases[$key] = '';
		}

		return ($phrases[$key] !== '' && $phrases[$key] !== null) ? $phrases[$key] : sms_humanize_phrase($phrase);
	}
}

// ------------------------------------------------------------------------
/* End of file language_helper.php */
/* Location: ./system/helpers/language_helper.php */
