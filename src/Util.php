<?php

/////////////////////////////////////////////////////////////////////
// Copyright (c) Autodesk, Inc. All rights reserved
// Written by Forge Partner Development
//
// Permission to use, copy, modify, and distribute this software in
// object code form for any purpose and without fee is hereby granted,
// provided that the above copyright notice appears in all copies and
// that both that copyright notice and the limited warranty and
// restricted rights notice below appear in all supporting
// documentation.
//
// AUTODESK PROVIDES THIS PROGRAM "AS IS" AND WITH ALL FAULTS.
// AUTODESK SPECIFICALLY DISCLAIMS ANY IMPLIED WARRANTY OF
// MERCHANTABILITY OR FITNESS FOR A PARTICULAR USE.  AUTODESK, INC.
// DOES NOT WARRANT THAT THE OPERATION OF THE PROGRAM WILL BE
// UNINTERRUPTED OR ERROR FREE.
/////////////////////////////////////////////////////////////////////

namespace Autodesk\ADN;

use Exception;
use Webmozart\PathUtil\Path;

class Util
{
    private static function checkDocType($pageType)
    {
        $type = 'Guide';
        if(strpos($pageType, 'Members') !== false)
        {
            $type = 'Section';
        }
        elseif(strpos($pageType, 'Method') !== false)
        {
            $type = 'Method';
        }
        elseif(strpos($pageType, 'Property') !== false || strpos($pageType, 'Properties') !== false)
        {
            $type = 'Property';
        }
        elseif(strpos($pageType, 'Constructor') !== false)
        {
            $type = 'Constructor';
        }
        elseif(strpos($pageType, 'Class') !== false)
        {
            $type = 'Class';
        }
        elseif(strpos($pageType, 'Enum') !== false)
        {
            $type = 'Enum';
        }
        elseif(strpos($pageType, 'Interface') !== false)
        {
            $type = 'Interface';
        }
        elseif(strpos($pageType, 'Event') !== false)
        {
            $type = 'Event';
        }
        elseif(strpos($pageType, 'Operator') !== false)
        {
            $type = 'Operator';
        }
        elseif(strpos($pageType, 'Field') !== false)
        {
            $type = 'Field';
        }
        elseif(strpos($pageType, 'Conversion') !== false)
        {
            $type = 'Shortcut';
        }
        elseif(strpos($pageType, 'Namespace') !== false)
        {
            $type = 'Namespace';
        }
        elseif(strpos($pageType, 'Structure') !== false)
        {
            $type = 'Struct';
        }
        elseif(strpos($pageType, 'Delegate') !== false)
        {
            $type = 'Delegate';
        }

        return $type;
    }

    public static function getDocType($name)
    {
        $result = \explode(' ', $name);
        $pageName = $result[0];
        $pageType = $result[1];

        $type = self::checkDocType($pageType);
        if($type == 'Guide')
        {
            $type = self::checkDocType(end($result));
        }

        return $type;
    }

    public static function getContentPath($basePath)
    {
        return Path::join($basePath, "Contents", "Resources", "Documents");
    }

    public static function getDatabasePath($basePath)
    {
        return Path::join($basePath, "Contents", "Resources", "docSet.dsidx");
    }

    public static function getPlistPath($basePath)
    {
        return Path::join($basePath, "Contents", "Info.plist");
    }

    /**
     * Parse command line arguments
     * 
     * @return mixed
     * ref: https://www.php.net/manual/en/features.commandline.php#78093
     */
    public static function arguments($argv)
    {
      $_ARG = array();
      foreach($argv as $arg)
      {
        if(preg_match('#^-{1,2}([a-zA-Z0-9]*)=?(.*)$#', $arg, $matches))
        {
          $key = $matches[1];
          switch ($matches[2])
          {
            case '':
            case 'true':
              $arg = true;
              break;
            case 'false':
              $arg = false;
              break;
            default:
              $arg = $matches[2];
          }
          $_ARG[$key] = $arg;
        }
        else
        {
          $_ARG['input'][] = $arg;
        }
      }
      return $_ARG;
    }

    /**
     * Convert Base64 to image
     * 
     * ref: https://base64.guru/developers/php/examples/decode-image
     */
    public static function genImageFromBase64($str, $filename)
    {
        // Obtain the original content (usually binary data)
        $result = \explode(';base64,', $str);
        $bin = base64_decode($result[1]);

        // Load GD resource from binary data
        $im = imageCreateFromString($bin);

        // Make sure that the GD library was able to load the image
        // This is important, because you should not miss corrupted or unsupported images
        if (!$im)
            throw new  Exception('Base64 value is not a valid image');

        // Save the GD resource as PNG in the best possible quality (no compression)
        // This will strip any metadata or invalid contents (including, the PHP backdoor)
        // To block any possible exploits, consider increasing the compression level
        imagepng($im, $filename, 0);
    }
}

?>