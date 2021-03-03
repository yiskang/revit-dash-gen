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
use CHMLib\CHM;
use \CHMLib\Entry;

class DocSet
{
    /**
     * The full path of the docSet folder.
     * 
     * @var string
     */
    private $path;

    /**
     * The docSet configuration (i.e. the Info.plist).
     * 
     * @var \Autodesk\ADN\Configuration
     */
    private $configuration;

    /**
     * Index DB Helper Class.
     * 
     * @var \Autodesk\ADN\SearchIndexDB
     */
    private $db;

    /**
     * The CHM instance.
     * 
     * @var \CHMLib\CHM
     */
    private $chm;

    /**
     * Initializes the instance.
     * 
     * @param \Autodesk\ADN\Configuration $configuration The docSet platform.
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Initializes the package
     * 
     * @param string $basePath The base path where the docSet is.
     * 
     * @throws \Exception Throws an Exception in case of errors.
     */
    public function initialize(string $basePath, string $chmPath)
    {
        $this->path = Path::join($basePath, $this->configuration->getIdentifier() . '.docset');
        $contentFolder = Util::getContentPath($this->path);

        if(!\mkdir($contentFolder, 0777, true))
        {
            throw new Exception("Failed to create DocSet package");
        }

        $this->chm = CHM::fromFile($chmPath);
    }

    private function buildPlist()
    {
        echo "\nStart building Info.plist ... \n";
        $filename = Util::getPlistPath($this->path);
        $this->configuration->enableJavaScript();
        $content = $this->configuration->toXML();

        \file_put_contents($filename, $content);
        echo "\nAll done.\n";
    }

    private function extractContents()
    {
        echo "\nStart extracting CHM ... \n";

        $contentFolder = Util::getContentPath($this->path);

        foreach($this->chm->getEntries(Entry::TYPE_FILE) as $entry)
        {
            //echo "\tProcessing {$entry->getPath()}... ";
            $entryPath = ltrim(str_replace('/', DIRECTORY_SEPARATOR, $entry->getPath()), DIRECTORY_SEPARATOR);
            $parts = explode(DIRECTORY_SEPARATOR, $entryPath);
            $subDirectory = count($parts) > 1 ? implode(DIRECTORY_SEPARATOR, array_splice($parts, 0, -1)) : '';
            $filename = array_pop($parts);
            $path = $contentFolder;
            if($subDirectory !== '')
            {
                $path .= DIRECTORY_SEPARATOR . $subDirectory;
                if(!is_dir($path))
                {
                    mkdir($path, 0777, true);
                }
            }
            $path .= DIRECTORY_SEPARATOR . $filename;
            file_put_contents($path, $entry->getContents());
            //echo "\tdone.\n";
        }
        echo "\nAll done.\n";
    }

    private function buildIndexDb()
    {
        echo "\nStart building IndexDb ... \n";
        $filename = Util::getDatabasePath($this->path);
        $this->db = new SearchIndexDB($filename);
        $this->db->initialize();

        function traverse($tree, $level, $db)
        {
            if ($tree === null)
                return;

            foreach ($tree->getItems() as $child)
            {
                $pageName = $child->getName();
                $pageType = Util::getDocType($pageName);

                $pagePath = null;
                $pageEntry = $child->findEntry();

                if($pageEntry !== null)
                    $pagePath = $pageEntry->getPath();

                $db->insertIndex($pageName, $pageType, $pagePath);

                traverse($child->getChildren(), $level + 1, $db);
            }
        }

        $toc = $this->chm->getTOC(); // Parse the contents of the .hhc file
        $index = $this->chm->getIndex(); // Parse the contents of the .hhk file

        traverse($toc, 0, $this->db);

        $this->db->close();
        echo "\nAll done.\n";
    }

    public function extract()
    {
        $this->buildIndexDb();
        $this->extractContents();
    }

    public function setIndexPage()
    {
        $toc = $this->chm->getTOC();
        $rootItem = $toc->getItems()[0];
        $pageEntry = $rootItem->findEntry();

        if($pageEntry === null)
            return;

        $pagePath = $pageEntry->getPath();
        $this->configuration->setIndexFilePath($pagePath);
    }

    public function finalize()
    {
        $this->setIndexPage();
        $this->buildPlist();
    }

    public function rename($newName)
    {
        $parent = dirname($this->path);
        $newPath = Path::join($parent, $newName);
        \rename($this->path, $newPath);
    }
}

?>