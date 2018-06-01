<?php
/**
 * Language - simple language handler.
 *
 * @author Bartek Kuśmierczuk - contact@qsma.pl - http://qsma.pl
 * @version 3.0
 */

namespace Language;

use Helpers\Inflector;
use Language\LanguageManager as Manager;

use MessageFormatter;


/**
 * A Language class to load the requested language file.
 */
class Language
{
    /**
     * The Language Manager Instance.
     *
     * @var \Language\LanguageManager
     */
    protected $manager;

    /**
     * Holds an array with the Domain's Messages.
     *
     * @var array
     */
    private $messages = array();

    /**
     * The current Language Domain.
     */
    private $domain = null;

    /**
     * The current Language information.
     */
    private $code      = 'en';
    private $info      = 'English';
    private $name      = 'English';
    private $locale    = 'en-US';
    private $direction = 'ltr';

    /**
     * Holds an array with the Legacy Messages.
     *
     * @var array
     */
    private $legacyMessages = array();


    /**
     * Language constructor.
     * @param string $domain
     * @param string $code
     */
    public function __construct(Manager $manager, $domain, $code)
    {
        $this->manager = $manager;

        //
        $languages = $manager->getLanguages();

        if (isset($languages[$code]) && ! empty($languages[$code])) {
            $info = $languages[$code];

            $this->code = $code;

            //
            $this->info      = $info['info'];
            $this->name      = $info['name'];
            $this->locale    = $info['locale'];
            $this->direction = $info['dir'];
        } else {
            $code = 'en';
        }

        $this->domain = $domain;

        //
        $pathName = Inflector::classify($domain);

        if ($pathName == 'Nova') {
            $basePath = SYSTEMDIR;
        } else if ($pathName == 'Shared') {
            $basePath = ROOTDIR .'shared' .DS;
        } else if (is_dir(APPDIR .'Modules' .DS .$pathName)) {
            $basePath = APPDIR .'Modules/' .$pathName .DS;
        } else if (is_dir(APPDIR .'Templates' .DS .$pathName)) {
            $basePath = APPDIR .'Templates/' .$pathName .DS;
        } else {
            $basePath = APPDIR;
        }

        $filePath = $basePath .'Language' .DS .ucfirst($code) .DS .'messages.php';

        // Check if the language file is readable.
        if (! is_readable($filePath)) {
            return;
        }

        // Get the Domain's messages from the Language file.
        $messages = include($filePath);

        // A final consistency check.
        if (is_array($messages) && ! empty($messages)) {
            $this->messages = $messages;
        }
    }

    /**
     * Translate a message with optional formatting
     * @param string $message Original message.
     * @param array $params Optional params for formatting.
     * @return string
     */
    public function translate($message, array $params = array())
    {
        // Update the current message with the domain translation, if we have one.
        if (isset($this->messages[$message]) && ! empty($this->messages[$message])) {
            $message = $this->messages[$message];
        }

        if (empty($params)) {
            return $message;
        }

        // Standard Message formatting, using the standard PHP Intl and its MessageFormatter.
        // The message string should be formatted using the standard ICU commands.
        return MessageFormatter::formatMessage($this->locale, $message, $params);

        // The VSPRINTF alternative for Message formatting, for those die-hard against ICU.
        // The message string should be formatted using the standard PRINTF commands.
        //return vsprintf($message, $arguments);
    }

    // Public Getters

    /**
     * Get current domain
     * @return string
     */
    public function domain()
    {
        return $this->domain;
    }

    /**
     * Get current code
     * @return string
     */
    public function code()
    {
        return $this->code;
    }

    /**
     * Get current info
     * @return string
     */
    public function info()
    {
        return $this->info;
    }

    /**
     * Get current name
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get current locale
     * @return string
     */
    public function locale()
    {
        return $this->locale;
    }

    /**
     * Get all messages
     * @return array
     */
    public function messages()
    {
        return $this->messages;
    }

    /**
     * Get the current direction
     *
     * @return string rtl or ltr
     */
    public function direction()
    {
        return $this->direction;
    }

    //--------------------------------------------------------------------
    // Legacy API Methods
    //--------------------------------------------------------------------

    /**
     * Load language function.
     *
     * @param string $name
     * @param string $code
     * @return void
     */
    public function load($name, $code = null)
    {
        $code = $code ?: $this->getLocale();

        // Language file.
        $filePath = APPDIR .'Language' .DS .ucfirst($code) .DS .$name .'.php';

        // Check if it is readable.
        if (! is_readable($filePath)) {
            return;
        }

        // Require the file.
        $messages = include $filePath;

        // A small sanity check.
        $messages = is_array($messages) ? $messages : array();

        if (isset($this->legacyMessages[$code])) {
            $messages = array_merge($this->legacyMessages[$code], $messages);
        }

        $this->legacyMessages[$code] = $messages;
    }

    /**
     * Retrieve an element from the language array by its key.
     *
     * @param  string $value
     *
     * @return string
     */
    public function get($value, $code = null, $params = [])
    {
        $code = $code ?: $this->getLocale();

        $messages = isset($this->legacyMessages[$code]) ? $this->legacyMessages[$code] : array();

        if (isset($messages[$value]) && ! empty($messages[$value])) {
            $message = $messages[$value];

            if (empty($params)) {
                return $message;
            }

            $languages = $this->manager->getLanguages();
            $info = $languages[$code];
            $locale = $info['locale'];

            // Standard Message formatting, using the standard PHP Intl and its MessageFormatter.
            // The message string should be formatted using the standard ICU commands.
            return MessageFormatter::formatMessage($locale, $message, $params);
        }

        return $value;
    }

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    protected function getLocale()
    {
        return $this->manager->getLocale();
    }

}
