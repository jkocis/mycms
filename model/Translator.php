<?php
/**
 * MyCMS
 * 
 * @author Jan Kocis
 */
namespace MyCms;

use Nette;

class Translator implements Nette\Localization\ITranslator
{
    /**
     * Translates the given string.
     * @param  string   message
     * @param  int      plural count
     * @return string
     */
    public function translate($message, $count = null)
    {
        return $message;
    }
}
