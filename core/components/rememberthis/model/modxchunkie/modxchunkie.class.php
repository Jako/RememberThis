<?php
/**
 * Name: modxChunkie
 * Original name: Chunkie
 * Version: 2.2
 *
 * Modified, documented and enhanced for Revolution by Thomas Jakobi <thomas.jakobi@partout.info>
 * Date: July 3, 2013
 *
 * Original Author: Armand "bS" Pondman <apondman@zerobarrier.nl>
 * Date: Oct 8, 2006
 *
 */

class modxChunkie {

	/**
	 * The name of a MODX chunk (could be prefixed by @FILE, @INLINE or
	 * @CHUNK). Chunknames starting with '@FILE ' are loading a chunk from
	 * the filesystem (prefixed by $basepath). Chunknames starting with
	 * '@INLINE ' contain the template code itself.
	 *
	 * @var string $template
	 * @access private
	 */
	public $template;

	/**
	 * The basepath @FILE is prefixed with.
	 * @var string $basepath
	 * @access private
	 */
	private $basepath;

	/**
	 * Uncached MODX tags are not parsed inside of revoChunkie.
	 * @var boolean $parseLazy
	 * @access private
	 */
	private $parseLazy;

	/**
	 * Array of placeholder that are not parsed but only replaced.
	 * @var string $replaceOnly
	 * @access private
	 */
	private $replaceOnly;

	/**
	 * A collection of all placeholder.
	 * @var array $placeholders
	 * @access private
	 */
	public $placeholders;

	/**
	 * The current depth of the placeholder keypath.
	 * @var int $depth
	 * @access private
	 */
	private $depth;

	/**
	 * The maximum depth of the placeholder keypath.
	 * @var int $maxdepth
	 * @access private
	 */
	private $maxdepth;

	/**
	 * The basepath @FILE is prefixed with.
	 * @var array $chunkieCache
	 * @access private
	 */
	private $chunkieCache;

	/**
	 * revoChunkie constructor
	 *
	 * @param modX $modx A reference to the modX instance
	 * @param array $config An array of configuration properties
	 */
	public function __construct(modX &$modx, array $config = array()) {
		$this->modx = & $modx;
		$this->depth = 0;
		$this->maxdepth = 4;
		if ($this->modx->getOption('useCorePath', $config, FALSE)) {
			$this->basepath = MODX_CORE_PATH . $this->modx->getOption('basepath', $config, ''); // Basepath @FILE is prefixed with.
		} else {
			$this->basepath = MODX_BASE_PATH . $this->modx->getOption('basepath', $config, ''); // Basepath @FILE is prefixed with.
		}
		$this->template = $this->getTemplate($this->modx->getOption('template', $config, '@INLINE '));
		$this->parseLazy = (boolean) $this->modx->getOption('parseLazy', $config, FALSE);
		$this->replaceOnly = (array) $this->modx->getOption('replaceOnly', $config, array());
	}

	/**
	 * Set the basepath @FILE is prefixed with.
	 *
	 * @access public
	 * @param string $basepath The basepath @FILE is prefixed with.
	 */
	public function setBasepath($basepath) {
		$this->basepath = $basepath;
	}

	/**
	 * Fill placeholder array with values. If $value contains a nested
	 * array the key of the subarray is prefixed to the placeholder key
	 * separated by dot sign.
	 *
	 * @access public
	 * @param string $value The value(s) the placeholder array is filled
	 * with. If $value contains an array, all elements of the array are
	 * filled into the placeholder array using key/value. If one array
	 * element contains a subarray the function will be called recursive
	 * prefixing $keypath with the key of the subarray itself.
	 * @param string $key The key $value will get in the placeholder array
	 * if it is not an array, otherwise $key will be used as $keypath
	 * @param string $keypath The string separated by dot sign $key will
	 * be prefixed with
	 */
	public function createVars($value = '', $key = '', $keypath = '') {
		if ($this->depth > $this->maxdepth) {
			return;
		}
		$keypath = !empty($keypath) ? $keypath . '.' . $key : $key;

		if (is_array($value)) {
			$this->depth++;
			foreach ($value as $subkey => $subval) {
				$this->createVars($subval, $subkey, $keypath);
			}
			$this->depth--;
		} else {
			$this->placeholders[$keypath] = $value;
		}
	}

	/**
	 * Add one value to the placeholder array with its key.
	 *
	 * @access public
	 * @param string $key The key for the placeholder added
	 * @param string $value The value for the placeholder added
	 */
	public function addVar($key, $value) {
		$this->placeholders[$key] = $value;
	}

	/**
	 * Get the placeholder array.
	 *
	 * @access public
	 */
	public function getVars() {
		return $this->placeholders;
	}

	/**
	 * Render the current template with the current placeholder.
	 *
	 * @access public
	 * @param boolean $reset Reset placeholder
	 * @return string
	 */
	public function render($reset = TRUE) {
		$template = $this->template;

		$chunk = $this->modx->newObject('modChunk');
		$chunk->setCacheable(false);
		$template = $chunk->process($this->placeholders, $template);
		unset($chunk);
		if ($this->parseLazy) {
			$template = str_replace(array('[[#!'), array('[[!'), $template);
		}
		if (count($this->replaceOnly)) {
			foreach ($this->replaceOnly as $placeholdername) {
				$template = str_replace('[[#+' . $placeholdername . ']]', $this->placeholders[$placeholdername], $template);
			}
		}
		if ($reset) {
			$this->placeholders = array();
		}
		return $template;
	}

	/**
	 * Get a template chunk. All chunks retrieved by this function are
	 * cached in $this->chunkieCache for later reusage
	 *
	 * @access public
	 * @param string $tpl The name of a MODX chunk (could be prefixed by
	 * @FILE, @INLINE or @CHUNK). Chunknames starting with '@FILE ' are
	 * loading a chunk from the filesystem (prefixed by $basepath).
	 * Chunknames starting with '@INLINE ' contain the template code itself.
	 * @return string
	 */
	public function getTemplate($tpl) {
		$template = '';

		if (substr($tpl, 0, 6) == '@FILE ') {
			$filename = substr($tpl, 6);
			if (!isset($this->chunkieCache['@FILE'])) {
				$this->chunkieCache['@FILE'] = array();
			}
			if (!array_key_exists($filename, $this->chunkieCache['@FILE'])) {
				if (file_exists($this->basepath . $filename)) {
					$template = file_get_contents($this->basepath . $filename);
				}
				$this->chunkieCache['@FILE'][$filename] = $template;
			} else {
				$template = $this->chunkieCache['@FILE'][$filename];
			}
		} elseif (substr($tpl, 0, 8) == '@INLINE ') {
			$template = substr($tpl, 8);
		} else {
			if (substr($tpl, 0, 7) == '@CHUNK ') {
				$chunkname = substr($tpl, 7);
			} else {
				$chunkname = $tpl;
			}
			if (!isset($this->chunkieCache['@CHUNK'])) {
				$this->chunkieCache['@CHUNK'] = array();
			}
			if (!array_key_exists($chunkname, $this->chunkieCache['@CHUNK'])) {
				$chunk = $this->modx->getObject('modChunk', array('name' => $chunkname));
				if ($chunk) {
					$this->chunkieCache['@CHUNK'][$chunkname] = $chunk->getContent();
				} else {
					$this->chunkieCache['@CHUNK'][$chunkname] = FALSE;
				}
			}
			$template = $this->chunkieCache['@CHUNK'][$chunkname];
		}

		if ($this->parseLazy) {
			$template = str_replace('[[!', '[[#!', $template);
		}

		if (count($this->replaceOnly)) {
			foreach ($this->replaceOnly as $placeholdername) {
				$template = str_replace('[[+' . $placeholdername . ']]', '[[#+' . $placeholdername . ']]', $template);
			}
		}

		return $template;
	}

	/**
	 * Change the template for rendering.
	 *
	 * @access public
	 * @param string $template The new template string for rendering.
	 */
	public function setTemplate($template) {
		$this->template = $template;
	}

}

?>
