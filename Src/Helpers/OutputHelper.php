<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 10.02.2016
 * Time: 11:17
 */

namespace famoser\phpFrame\Helpers;


class OutputHelper extends HelperBase
{
    public function sanitizeOutput($buffer) {

        // Searching textarea and pre
        preg_match_all('#\<textarea.*\>.*\<\/textarea\>#Uis', $buffer, $foundTxt);
        preg_match_all('#\<pre.*\>.*\<\/pre\>#Uis', $buffer, $foundPre);

        // replacing both with <textarea>$index</textarea> / <pre>$index</pre>
        $buffer = str_replace($foundTxt[0], array_map(function($el){ return '<textarea>'.$el.'</textarea>'; }, array_keys($foundTxt[0])), $buffer);
        $buffer = str_replace($foundPre[0], array_map(function($el){ return '<pre>'.$el.'</pre>'; }, array_keys($foundPre[0])), $buffer);

        // your stuff
        $search = array(
            '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
            '/[^\S ]+\</s',  // strip whitespaces before tags, except space
            '/(\s)+/s'       // shorten multiple whitespace sequences
        ,
            // t = text
            // o = tag open
            // c = tag close
            // Keep important white-space(s) after self-closing HTML tag(s)
            '#<(img|input)(>| .*?>)#s',
            // Remove a line break and two or more white-space(s) between tag(s)
            '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
            '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
            '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
            '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
            '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
            '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
            // Remove HTML comment(s) except IE comment(s)
            '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
        );

        $replace = array(
            '>',
            '<',
            '\\1',


            '<$1$2</$1>',
            '$1$2$3',
            '$1$2$3',
            '$1$2$3$4$5',
            '$1$2$3$4$5$6$7',
            '$1$2$3',
            '<$1$2',
            '$1 ',
            '$1',
            ""
        );

        $buffer = preg_replace($search, $replace, $buffer);

        // Replacing back with content
        $buffer = str_replace(array_map(function($el){ return '<textarea>'.$el.'</textarea>'; }, array_keys($foundTxt[0])), $foundTxt[0], $buffer);
        $buffer = str_replace(array_map(function($el){ return '<pre>'.$el.'</pre>'; }, array_keys($foundPre[0])), $foundPre[0], $buffer);

        return $buffer;
    }
}