<?php

/**
 * {html_options} is a custom function  that creates the html <select><option> group
 * with the assigned data.
 * It takes care of which item(s) are selected by default as well.
 *
 * <pre>
 *  * options  : Options array
 *  * rest     : Rest attributes for the <select> tag
 * </pre>
 *
 *
 * NOTE:
 * - If the optional "name" attribute is given, the <select></select> tags are created,
 *    otherwise ONLY the <option> list is generated.
 *
 * - If a given value is an array, it will treat it as an html <optgroup>,
 *    and display the groups.
 *
 * - All parameters that are not in the list above are printed as name/value-pairs inside the <select> tag.
 *    They are ignored if the optional name is not given.
 *
 * - All output is XHTML compliant.
 *
 *
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 *
 * This file is released under the LGPL
 * "GNU Lesser General Public License"
 * More information can be found here:
 * {@link http://www.gnu.org/copyleft/lesser.html}
 *
 * @author     Constantin Bejenaru <http://www.frozenminds.com>
 * @copyright  Copyright (c) 2008, Constantin Bejenaru
 * @license    http://www.gnu.org/copyleft/lesser.html  GNU Lesser General Public License
 * @link       http://dwoo.org/
 * @date       2008-06-13
 * @package    Dwoo
 */
class Dwoo_Plugin_html_options extends Dwoo_Plugin
{

   /**
    * Set objects
    *
    * $var object
    */
   protected $dom , $select , $option , $optgroup , $parent = NULL;

   public function process (array $options , array $rest = array())
   {
      //Build DOM elemenent
      $this->dom = new DOMDocument ('1.0');

      //Set encoding
      $this->dom->encoding = $this->dwoo->getCharset ();

      //Define selected value
      $selected = NULL;
      if (isset ($rest['selected']))
      {
         $selected = $rest['selected'];

         //Unset from $rest array
         unset ($rest['selected']);
      }

      //Determine if we have a "name" attribute for the <select> element
      if (isset ($rest['name']) && $rest['name'] !== '')
      {
         //Create <select> element
         $this->select = $this->dom->createElement ('select');
         $this->select = $this->dom->appendChild ($this->select);

         //Loop through all attributes and set them
         foreach ($rest as $key => $value)
         {
            $this->select->setAttribute ($key, $value);
         }

         $this->parent = & $this->select;
      } else
      {
         //There is not "name" attribute,
         //skip <select> element
         $this->parent = & $this->dom;
      }

      //Test if we have options
      if ( ! empty ($options))
      {
         //Loop through options
         foreach ($options as $key => & $value)
         {
            if ( ! is_array ($value))
            {
               //Escape value
               $value = $this->escape ($value);

               //Create <option> element
               $this->option = $this->dom->createElement ('option', $value);

               //Append element to <select> (if possible)
               $this->option = $this->parent->appendChild ($this->option);

               //Set label
               $this->option->setAttribute ('label', $value);
            } else
            {
               //Create <optgroup> element
               $this->optgroup = $this->dom->createElement ('optgroup');

               //Append element to <select>
               $this->optgroup = $this->parent->appendChild ($this->optgroup);

               //Set label
               $this->optgroup->setAttribute ('label', $this->escape ($key));

               //Loop through <optgroup> options and create elements
               foreach ($value as $opt_key => & $opt_value)
               {
                  //Escape value
                  $opt_value = $this->escape ($opt_value);

                  //Create <option> element
                  $this->option = $this->dom->createElement ('option', $opt_value);

                  //Append element to <optgroup>
                  $this->option = $this->optgroup->appendChild ($this->option);

                  //Set label
                  $this->option->setAttribute ('label', $opt_value);
               }
            }

            //Set value
            $this->option->setAttribute ('value', $this->escape ($key));

            //Select option
            if ($selected !== '' && (string) $key === (string) $selected)
            {
               $this->option->setAttribute ('selected', 'selected');
            }
         }
      }

      //Indent and format output nicely
      $this->dom->preserveWhiteSpace = FALSE;
      $this->dom->formatOutput = TRUE;

      //Dump internal XML tree into a string
      //We use "saveXML()" over "saveHTML()" because the second
      //currently knows only HTML 4.0 and we want XHTML
      $output = $this->dom->saveXML ();

      //Strip <?xml> prefix and we have valid XHTML
      $output = preg_replace ('#<\?xml[^>]*>#i', '', $output);

      return trim ($output);
   }

   /**
    * Escape a string
    *
    * @param  string $string String to be escaped
    * @return string Escaped string
    */
   protected function escape ($string)
   {
      //Take care of values
      $string = (string) $string;

      //Decode first (to prevent double escaping)
      $string = htmlspecialchars_decode ($string, ENT_QUOTES);

      //Escape string
      $string = htmlspecialchars ($string, ENT_QUOTES, $this->dwoo->getCharset ());

      return trim ($string);
   }
}
