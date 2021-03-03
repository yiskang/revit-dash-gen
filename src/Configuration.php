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

/**
 * The docSet configuration (i.e. the Info.plist).
 */
class Configuration
{
    /**
     * The CFBundleIdentifier for the docSet.
     * @var string
     */
    private $identifier;

    /**
     * The CFBundleName for the docSet.
     * @var string
     */
    private $name;

    /**
     * The DashDocSetFamily for the docSet.
     * @var string
     */
    private $family = 'dashtoc3';

    /**
     * The DocSetPlatformFamily for the docSet.
     * @var string
     */
    private $platform;

    /**
     * The dashIndexFilePath for the docSet.
     * @var string
     */
    private $indexFilePath;

    /**
     * The isJavaScriptEnabled for the docSet.
     * @var bool
     */
    private $isJavaScriptEnabled = false;

    /**
     * Initializes the instance.
     * @param string $identifier The docSet's CFBundleIdentifier.
     * @param string $name The docSet's CFBundleName.
     */
    public function __construct(string $identifier, string $name)
    {
        $this->name = $name;
        $this->platform = $identifier;
        $this->identifier = $identifier;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Get file path of the index file.
     * 
     * @return string
     */
    public function getIndexFilePath()
    {
        return $this->indexFilePath;
    }

    /**
     * Set file path of the index file.
     * 
     * @param string $path The file path of the index file.
     */
    public function setIndexFilePath($path)
    {
        $this->indexFilePath = $path;
    }

    /**
     * Enable JavaScript support for this docSet.
     * 
     * @return bool
     */
    public function enableJavaScript()
    {
        $this->isJavaScriptEnabled = true;
    }

    /**
     * True if the JavaScript support is enabled for this docSet.
     * 
     * @return bool
     */
    public function isJavaScriptEnabled()
    {
        return $this->isJavaScriptEnabled;
    }

    /**
     * Serialize configuration to XML.
     * 
     * @return string
     */
    public function toXML()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
        ."\n". '<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">'
        ."\n". '<plist version="1.0">'
        ."\n\t". '<dict>'
        ."\n\t\t". '<key>CFBundleIdentifier</key>'
        ."\n\t\t". '<string>'. $this->identifier .'</string>'
        ."\n\t\t". '<key>CFBundleName</key>'
        ."\n\t\t". '<string>'. $this->name .'</string>'
        ."\n\t\t". '<key>DashDocSetFamily</key>'
        ."\n\t\t". '<string>'. $this->family .'</string>'
        ."\n\t\t". '<key>DocSetPlatformFamily</key>'
        ."\n\t\t". '<string>'. $this->platform .'</string>'
        ."\n\t\t". '<key>isDashDocset</key>'
        ."\n\t\t". '<true/>';

        if(!empty($this->indexFilePath))
        {
            $xml .= "\n\t\t". '<key>dashIndexFilePath</key>'
            ."\n\t\t". '<string>'. $this->indexFilePath .'</string>';
        }

        if($this->isJavaScriptEnabled)
        {
            $xml .= "\n\t\t". '<key>isJavaScriptEnabled</key>'
            ."\n\t\t". '<true/>';
        }

        $xml .= "\n\t</dict>"
        ."\n". '</plist>';

        return $xml;
    }
}

?>