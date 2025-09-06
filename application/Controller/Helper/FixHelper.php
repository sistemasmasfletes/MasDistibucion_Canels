<?php
class Helper_FixHelper
{
    public function generateFix($text)
    {
        $text = strtolower($text);
        $text = htmlentities($text, ENT_COMPAT, 'UTF-8');
        $text = trim($text);
		$patron = array (
			// Espacios, puntos y simbolos
			'/[\., _]+/' => '-',
            '/[\?+\/\#\*(){}$%!·|¡="]+/' => '',

            /* & */
            '/&amp;/' => '',
            '/amp;/' => '',
            /* / */
            '/&iquest;/' => '',
            /* ç */
            '/&ccedil;/' => '',
            '/&Ccedil;/' => '',
            /* " */
            '/&quot;/' => '',

            //¡·ºª'“”÷¬¢
            '/&iexcl;/' => '',
            '/&middot;/' => '',
            '/&ordm;/' => '',
            '/&ordf;/' => '',
            '/&acute;/' => '',
            '/&rdquo;/' => '',
            '/&ldquo;/' => '',
            '/&divide;/' => '',
            '/&not;/' => '',
            '/&cent;/' => '',
            '/&lt;/' => '',
            '/&gt;/' => '',
            '/&uml;/' => '',

                    
			// Vocales
            /*Acento invertido*/
			'/&agrave;/' => 'a',
			'/&egrave;/' => 'e',
			'/&igrave;/' => 'i',
			'/&ograve;/' => 'o',
			'/&ugrave;/' => 'u',
			'/&Agrave;/' => 'a',
			'/&Egrave;/' => 'e',
			'/&Igrave;/' => 'i',
			'/&Ograve;/' => 'o',
			'/&Ugrave;/' => 'u',
            /*Acento*/
			'/&aacute;/' => 'a',
			'/&eacute;/' => 'e',
			'/&iacute;/' => 'i',
			'/&oacute;/' => 'o',
			'/&uacute;/' => 'u',
                        '/&Aacute;/' => 'a',
			'/&Eacute;/' => 'e',
			'/&Iacute;/' => 'i',
			'/&Oacute;/' => 'o',
			'/&Uacute;/' => 'u',
            /*acento circunflejo*/
			'/&acirc;/' => 'a',
			'/&ecirc;/' => 'e',
			'/&icirc;/' => 'i',
			'/&ocirc;/' => 'o',
			'/&ucirc;/' => 'u',
                        '/&Acirc;/' => 'a',
			'/&Ecirc;/' => 'e',
			'/&Icirc;/' => 'i',
			'/&Ocirc;/' => 'o',
			'/&Ucirc;/' => 'u',
            /*tildes*/
			'/&atilde;/' => 'a',
			'/&etilde;/' => 'e',
			'/&itilde;/' => 'i',
			'/&otilde;/' => 'o',
			'/&utilde;/' => 'u',
			'/&Atilde;/' => 'a',
			'/&Etilde;/' => 'e',
			'/&Itilde;/' => 'i',
			'/&Otilde;/' => 'o',
			'/&Utilde;/' => 'u',
            /*dieresis*/
			'/&auml;/' => 'a',
			'/&euml;/' => 'e',
			'/&iuml;/' => 'i',
			'/&ouml;/' => 'o',
			'/&uuml;/' => 'u',
			'/&Auml;/' => 'a',
			'/&Euml;/' => 'e',
			'/&Iuml;/' => 'i',
			'/&Ouml;/' => 'o',
			'/&Uuml;/' => 'u',
			// Otras letras
			'/&aring;/' => 'a',
			'/&ntilde;/' => 'n',
			'/&Ntilde;/' => 'n',
		);

		$text = preg_replace(array_keys($patron),array_values($patron),$text);
		return $text;

    }

    public function generateFix2($text)
    {
        $text = strtolower($text);
        $text = htmlentities($text, ENT_COMPAT, 'UTF-8');
        $text = trim($text);
		$patron = array (
			// Espacios, puntos y simbolos
            '/[\.,\?+\/\#\*(){}$%!·|¡="]+/' => '',

            /* & */
            '/&amp;/' => '',
            '/amp;/' => '',
            /* / */
            '/&iquest;/' => '',
            /* ç */
            '/&ccedil;/' => '',
            '/&Ccedil;/' => '',
            /* " */
            '/&quot;/' => '',

            //¡·ºª'“”÷¬¢
            '/&iexcl;/' => '',
            '/&middot;/' => '',
            '/&ordm;/' => '',
            '/&ordf;/' => '',
            '/&acute;/' => '',
            '/&rdquo;/' => '',
            '/&ldquo;/' => '',
            '/&divide;/' => '',
            '/&not;/' => '',
            '/&cent;/' => '',
            '/&lt;/' => '',
            '/&gt;/' => '',
            '/&uml;/' => '',


			// Vocales
            /*Acento invertido*/
			'/&agrave;/' => 'a',
			'/&egrave;/' => 'e',
			'/&igrave;/' => 'i',
			'/&ograve;/' => 'o',
			'/&ugrave;/' => 'u',
			'/&Agrave;/' => 'a',
			'/&Egrave;/' => 'e',
			'/&Igrave;/' => 'i',
			'/&Ograve;/' => 'o',
			'/&Ugrave;/' => 'u',
            /*Acento*/
			'/&aacute;/' => 'a',
			'/&eacute;/' => 'e',
			'/&iacute;/' => 'i',
			'/&oacute;/' => 'o',
			'/&uacute;/' => 'u',
                        '/&Aacute;/' => 'a',
			'/&Eacute;/' => 'e',
			'/&Iacute;/' => 'i',
			'/&Oacute;/' => 'o',
			'/&Uacute;/' => 'u',
            /*acento circunflejo*/
			'/&acirc;/' => 'a',
			'/&ecirc;/' => 'e',
			'/&icirc;/' => 'i',
			'/&ocirc;/' => 'o',
			'/&ucirc;/' => 'u',
                        '/&Acirc;/' => 'a',
			'/&Ecirc;/' => 'e',
			'/&Icirc;/' => 'i',
			'/&Ocirc;/' => 'o',
			'/&Ucirc;/' => 'u',
            /*tildes*/
			'/&atilde;/' => 'a',
			'/&etilde;/' => 'e',
			'/&itilde;/' => 'i',
			'/&otilde;/' => 'o',
			'/&utilde;/' => 'u',
			'/&Atilde;/' => 'a',
			'/&Etilde;/' => 'e',
			'/&Itilde;/' => 'i',
			'/&Otilde;/' => 'o',
			'/&Utilde;/' => 'u',
            /*dieresis*/
			'/&auml;/' => 'a',
			'/&euml;/' => 'e',
			'/&iuml;/' => 'i',
			'/&ouml;/' => 'o',
			'/&uuml;/' => 'u',
			'/&Auml;/' => 'a',
			'/&Euml;/' => 'e',
			'/&Iuml;/' => 'i',
			'/&Ouml;/' => 'o',
			'/&Uuml;/' => 'u',
			// Otras letras
			'/&aring;/' => 'a',
			'/&ntilde;/' => 'n',
			'/&Ntilde;/' => 'n',
		);

		$text = preg_replace(array_keys($patron),array_values($patron),$text);
		return $text;

    }
}